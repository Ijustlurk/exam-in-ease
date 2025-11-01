<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin-access')): ?>


<?php $__env->startSection('content'); ?>
<style>
    .exam-view-container {
        background-color: #e8f1f5;
        min-height: 100vh;
        padding: 30px;
    }

    .exam-header {
        background-color: #6ba5b3;
        color: white;
        padding: 20px 30px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
    }

    .exam-title {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
    }

    .exam-header i {
        margin-right: 0.5rem;
    }

    .approve-btn {
        background-color: #5a94aa;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .approve-btn:hover {
        background-color: #4a7d8f;
    }

    .approve-btn:disabled {
        background-color: #95a5a6;
        cursor: not-allowed;
    }

    .tabs-container {
        background: white;
        border-bottom: 2px solid #e0e0e0;
    }

    .tabs {
        display: flex;
        gap: 0;
    }

    .tab {
        padding: 18px 40px;
        background: transparent;
        border: none;
        color: #95a5a6;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }

    .tab.active {
        color: #6ba5b3;
        border-bottom-color: #6ba5b3;
    }

    .questions-container {
        background: white;
        padding: 30px;
        border-radius: 0 0 12px 12px;
        min-height: 500px;
    }

    .section-header {
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 8px;
    }

    .section-directions {
        font-size: 14px;
        color: #5a6c7d;
    }

    .question-card {
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 25px;
    }

    .question-header {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 15px;
    }

    .answer-option {
        border: 2px solid #d1d5db;
        border-radius: 25px;
        padding: 12px 20px;
        margin-bottom: 10px;
        font-size: 15px;
        color: #333;
        transition: all 0.2s;
    }

    .answer-option.correct {
        background-color: #4caf50;
        color: white;
        border-color: #4caf50;
        font-weight: 600;
    }

    .back-btn {
        background-color: #6ba5b3;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 20px;
    }

    .back-btn:hover {
        background-color: #5a94aa;
    }

    .no-questions {
        text-align: center;
        padding: 60px;
        color: #95a5a6;
        font-size: 18px;
    }
</style>

<div id="mainContent" class="main exam-view-container">
    <a href="<?php echo e(route('admin.exam-statistics.index')); ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Exams
    </a>

    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
        <!-- Exam Header -->
        <div class="exam-header">
            <div>
                <h1 class="exam-title"><?php echo e($exam->exam_title); ?></h1>
                <?php
                    // Calculate actual item count and total points from exam items (not sections)
                    $totalItems = 0;
                    $totalPoints = 0;
                    foreach ($exam->sections as $section) {
                        $totalItems += $section->examItems->count();
                        $totalPoints += $section->examItems->sum('points_awarded');
                    }
                ?>
                <div style="display: flex; gap: 2rem; margin-top: 0.75rem; font-size: 0.95rem; opacity: 0.95;">
                    <div>
                        <i class="fas fa-list-ol"></i>
                        <strong>Items:</strong> <?php echo e($totalItems); ?>

                    </div>
                    <div>
                        <i class="fas fa-star"></i>
                        <strong>Total Points:</strong> <?php echo e($totalPoints); ?>

                    </div>
                    <div>
                        <i class="fas fa-user"></i>
                        <strong>Author:</strong> 
                        <?php if($exam->teacher): ?>
                            <?php echo e($exam->teacher->first_name); ?> <?php echo e($exam->teacher->last_name); ?>

                        <?php else: ?>
                            Unknown
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if($exam->status === 'draft' || $exam->status === 'pending'): ?>
            <button class="approve-btn" onclick="approveExam(<?php echo e($exam->exam_id); ?>)">
                Approve Exam
            </button>
            <?php else: ?>
            <button class="approve-btn" disabled>
                <?php echo e(ucfirst($exam->status)); ?>

            </button>
            <?php endif; ?>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <div class="tabs">
                <button class="tab active">Questions</button>
            </div>
        </div>

        <!-- Questions Container -->
        <div class="questions-container">
            <?php if($exam->sections->count() > 0): ?>
                <?php $__currentLoopData = $exam->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <!-- Section Header -->
                    <div class="section-header">
                        <div class="section-title"><?php echo e($section->section_title); ?></div>
                        <?php if($section->section_directions): ?>
                        <div class="section-directions"><?php echo e($section->section_directions); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Questions in this section -->
                    <?php if($section->examItems->count() > 0): ?>
                        <?php $__currentLoopData = $section->examItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="question-card">
                                <div class="question-header">
                                    Question <?php echo e($index + 1); ?>. <?php echo e($item->question); ?>

                                </div>

                                <?php if($item->item_type === 'mcq'): ?>
                                    <?php
                                        // Get options and answer data
                                        $options = $item->options_array ?? $item->options ?? [];
                                        $answerData = $item->answer_array ?? $item->answer ?? [];
                                        
                                        // Convert string to array if needed
                                        if (is_string($options)) {
                                            $options = json_decode($options, true) ?? [];
                                        }
                                        if (is_string($answerData)) {
                                            $answerData = json_decode($answerData, true) ?? [];
                                        }
                                        
                                        // Handle correct answers - can be multiple
                                        $correctAnswers = [];
                                        if (is_array($answerData)) {
                                            // Check if it's a simple array of indices [2, 4] or has 'correct' key
                                            if (isset($answerData['correct'])) {
                                                // Format: {"correct": [2, 4]} or {"correct": 2}
                                                $correctAnswers = is_array($answerData['correct']) 
                                                    ? $answerData['correct'] 
                                                    : [$answerData['correct']];
                                            } else {
                                                // Format: [2, 4] - direct array of indices
                                                $correctAnswers = $answerData;
                                            }
                                        } elseif (is_numeric($answerData)) {
                                            $correctAnswers = [$answerData];
                                        }
                                    ?>

                                    <?php if(is_array($options) && count($options) > 0): ?>
                                        <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="answer-option <?php echo e(in_array($key, $correctAnswers ?? []) ? 'correct' : ''); ?>">
                                               <b><?php echo e(chr(65 + $key)); ?>. <?php echo e($option); ?></b>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <div class="answer-option" style="border-radius: 10px; padding: 15px;">
                                            <em>No options available</em>
                                        </div>
                                    <?php endif; ?>

                                <?php elseif($item->item_type === 'torf'): ?>
                                    <?php
                                        // Handle both array and decoded answer
                                        $answerData = $item->answer_array ?? $item->answer ?? [];
                                        if (is_string($answerData)) {
                                            $answerData = json_decode($answerData, true) ?? [];
                                        }
                                        
                                        $correctAnswer = isset($answerData['correct']) ? strtolower($answerData['correct']) : null;
                                    ?>

                                    <div class="answer-option <?php echo e($correctAnswer === 'true' ? 'correct' : ''); ?>">
                                        True
                                    </div>
                                    <div class="answer-option <?php echo e($correctAnswer === 'false' ? 'correct' : ''); ?>">
                                        False
                                    </div>

                                <?php elseif($item->item_type === 'essay'): ?>
                                    <div class="answer-option" style="border-radius: 10px; padding: 15px;">
                                        <em>Essay question - No predefined answer</em>
                                    </div>

                                <?php elseif($item->item_type === 'iden' || $item->item_type === 'enum'): ?>
                                    <div class="answer-option correct" style="border-radius: 10px;">
                                        Expected Answer: <?php echo e($item->expected_answer ?? 'N/A'); ?>

                                    </div>

                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="no-questions">
                            <i class="fas fa-question-circle" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                            <p>No questions in this section yet</p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="no-questions">
                    <i class="fas fa-clipboard-list" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p>No sections or questions added to this exam yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function approveExam(examId) {
        if (confirm('Are you sure you want to approve this exam?')) {
            fetch(`/admin/exam-statistics/${examId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while approving the exam');
            });
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php endif; ?>
<?php echo $__env->make('layouts.Admin.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\exam1\resources\views/admin/exam-statistics/show.blade.php ENDPATH**/ ?>