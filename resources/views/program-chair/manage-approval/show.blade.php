@extends('layouts.ProgramChair.app')

@section('main-content')
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

    .revise-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
        margin-left: 10px;
    }

    .revise-btn:hover {
        background-color: #c82333;
    }

    .approved-badge {
        background-color: #28a745;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
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
        color: white;
    }

    .no-questions {
        text-align: center;
        padding: 60px;
        color: #95a5a6;
        font-size: 18px;
    }
</style>

<div class="exam-view-container">
    <a href="{{ route('programchair.manage-approval.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back to Manage Approval
    </a>

    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
        <!-- Exam Header -->
        <div class="exam-header">
            <div>
                <h1 class="exam-title">{{ $exam->exam_title }}</h1>
                @php
                    // Calculate actual item count and total points from exam items
                    $totalItems = 0;
                    $totalPoints = 0;
                    foreach ($exam->sections as $section) {
                        $totalItems += $section->examItems->count();
                        $totalPoints += $section->examItems->sum('points_awarded');
                    }
                @endphp
                <div style="display: flex; gap: 2rem; margin-top: 0.75rem; font-size: 0.95rem; opacity: 0.95;">
                    <div>
                        <i class="bi bi-list-ol"></i>
                        <strong>Items:</strong> {{ $totalItems }}
                    </div>
                    <div>
                        <i class="bi bi-star"></i>
                        <strong>Total Points:</strong> {{ $totalPoints }}
                    </div>
                    <div>
                        <i class="bi bi-person"></i>
                        <strong>Author:</strong> 
                        @if($exam->teacher)
                            {{ $exam->teacher->first_name }} {{ $exam->teacher->last_name }}
                        @else
                            Unknown
                        @endif
                    </div>
                    <div>
                        <i class="bi bi-clock"></i>
                        <strong>Duration:</strong> {{ $exam->duration }} minutes
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: flex-start;">
                @if($exam->approval_status != 'approved')
                    <button class="revise-btn" onclick="openRevisionModal({{ $exam->exam_id }})">
                        <i class="bi bi-x-circle me-1"></i> Send for Revision
                    </button>
                    <button class="approve-btn" onclick="approveExam({{ $exam->exam_id }})">
                        <i class="bi bi-check-circle me-1"></i> Approve Exam
                    </button>
                @else
                    <span class="approved-badge">
                        <i class="bi bi-check-circle"></i> Approved
                    </span>
                @endif
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <div class="tabs">
                <button class="tab active">Questions</button>
            </div>
        </div>

        <!-- Questions Container -->
        <div class="questions-container">
            @if($exam->sections->count() > 0)
                @foreach($exam->sections as $section)
                    <!-- Section Header -->
                    <div class="section-header">
                        <div class="section-title">{{ $section->section_title }}</div>
                        @if($section->section_directions)
                        <div class="section-directions">{{ $section->section_directions }}</div>
                        @endif
                    </div>

                    <!-- Questions in this section -->
                    @if($section->examItems->count() > 0 || $section->items->count() > 0)
                        @php
                            $sectionItems = $section->examItems->count() > 0 ? $section->examItems : $section->items;
                        @endphp
                        @foreach($sectionItems as $index => $item)
                            <div class="question-card">
                                <div class="question-header">
                                    Question {{ $index + 1 }}. {{ $item->question }}
                                    <span style="float: right; color: #6ba5b3; font-weight: 600;">{{ $item->points_awarded }} {{ $item->points_awarded == 1 ? 'pt' : 'pts' }}</span>
                                </div>

                                @if($item->item_type === 'mcq')
                                    @php
                                        $options = json_decode($item->options, true) ?? [];
                                        $answerData = json_decode($item->answer, true) ?? [];
                                        
                                        // Handle correct answers
                                        $correctAnswers = [];
                                        if (is_array($answerData)) {
                                            if (isset($answerData['correct'])) {
                                                $correctAnswers = is_array($answerData['correct']) 
                                                    ? $answerData['correct'] 
                                                    : [$answerData['correct']];
                                            } else {
                                                $correctAnswers = $answerData;
                                            }
                                        }
                                    @endphp

                                    @if(is_array($options) && count($options) > 0)
                                        @foreach($options as $key => $option)
                                            <div class="answer-option {{ in_array($key, $correctAnswers) ? 'correct' : '' }}">
                                                {{ chr(65 + $key) }}. {{ $option }}
                                                @if(in_array($key, $correctAnswers))
                                                    <i class="bi bi-check-circle-fill float-end"></i>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="answer-option">
                                            <em>No options available</em>
                                        </div>
                                    @endif

                                @elseif($item->item_type === 'torf')
                                    @php
                                        $answerData = json_decode($item->answer, true) ?? [];
                                        $correctAnswer = isset($answerData['correct']) ? strtolower($answerData['correct']) : null;
                                    @endphp

                                    <div class="answer-option {{ $correctAnswer === 'true' ? 'correct' : '' }}">
                                        A. True
                                        @if($correctAnswer === 'true')
                                            <i class="bi bi-check-circle-fill float-end"></i>
                                        @endif
                                    </div>
                                    <div class="answer-option {{ $correctAnswer === 'false' ? 'correct' : '' }}">
                                        B. False
                                        @if($correctAnswer === 'false')
                                            <i class="bi bi-check-circle-fill float-end"></i>
                                        @endif
                                    </div>

                                @elseif($item->item_type === 'iden')
                                    <div class="answer-option correct">
                                        Expected Answer: {{ $item->expected_answer ?? 'N/A' }}
                                    </div>

                                @elseif($item->item_type === 'enum')
                                    @php
                                        $answers = json_decode($item->answer, true) ?? [];
                                    @endphp
                                    <div class="answer-option correct">
                                        <strong>Expected Answers ({{ $item->enum_type === 'ordered' ? 'Ordered' : 'Any Order' }}):</strong>
                                        <ol style="margin: 10px 0 0 0; padding-left: 20px;">
                                            @foreach($answers as $answer)
                                                <li>{{ $answer }}</li>
                                            @endforeach
                                        </ol>
                                    </div>

                                @elseif($item->item_type === 'essay')
                                    <div class="answer-option">
                                        <i class="bi bi-textarea-t me-2"></i>
                                        <em>Essay question - To be graded manually</em>
                                    </div>

                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="no-questions">
                            <i class="bi bi-question-circle" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                            <p>No questions in this section yet</p>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="no-questions">
                    <i class="bi bi-clipboard-list" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p>No sections or questions added to this exam yet</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background-color: #28a745; color: white; border-radius: 12px 12px 0 0; padding: 20px 24px;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="approvalModalLabel">
                    <i class="bi bi-check-circle-fill"></i>
                    <span id="approvalModalTitle">Approve Exam</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="approvalForm" method="POST">
                    @csrf
                    <input type="hidden" id="approval_exam_id" name="exam_id">
                    
                    <!-- Exam Details Section -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary mb-3">Exam Details</h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Exam Title</label>
                                <p class="form-control-plaintext" id="approval_exam_title">{{ $exam->exam_title }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Subject</label>
                                <p class="form-control-plaintext" id="approval_subject">{{ $exam->subject->subject_name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Description</label>
                            <p class="form-control-plaintext" id="approval_description">{{ $exam->exam_desc ?? 'No description provided' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Assigned Classes</label>
                            <p class="form-control-plaintext" id="approval_classes">
                                @if($exam->assignments && $exam->assignments->count() > 0)
                                    {{ $exam->assignments->pluck('class.class_name')->join(', ') }}
                                @else
                                    Not assigned to any class yet
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>

                    <!-- Editable Fields Section -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary mb-3">Exam Configuration</h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Duration (minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="approval_duration" name="duration" 
                                       min="1" value="{{ $exam->duration ?? '' }}" required style="border-radius: 8px; border: 1px solid #d1d5db;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Schedule Start <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="approval_schedule_start" 
                                       name="schedule_start" value="{{ $exam->schedule_start ? date('Y-m-d\TH:i', strtotime($exam->schedule_start)) : '' }}" 
                                       required style="border-radius: 8px; border: 1px solid #d1d5db;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Schedule End <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="approval_schedule_end" 
                                       name="schedule_end" value="{{ $exam->schedule_end ? date('Y-m-d\TH:i', strtotime($exam->schedule_end)) : '' }}" 
                                       required style="border-radius: 8px; border: 1px solid #d1d5db;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Exam Password (Optional)</label>
                            <input type="text" class="form-control" id="approval_password" name="exam_password" 
                                   placeholder="Leave empty if no password required" 
                                   style="border-radius: 8px; border: 1px solid #d1d5db;">
                            <small class="text-muted">Students will need this password to access the exam</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" 
                                style="border-radius: 8px; padding: 10px 20px;">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success" 
                                style="border-radius: 8px; padding: 10px 28px; font-weight: 500;">
                            <i class="bi bi-check-circle me-1"></i> Approve Exam
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Revision Modal -->
<div class="modal fade" id="revisionModal" tabindex="-1" aria-labelledby="revisionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background-color: #6ba5b3; color: white; border-radius: 12px 12px 0 0; padding: 20px 24px;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="revisionModalLabel">
                    <i class="bi bi-pencil-square"></i>
                    <span>Send for Revision</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="revisionForm" method="POST" action="{{ route('programchair.manage-approval.revise', $exam->exam_id) }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Add Notes</label>
                        <textarea class="form-control" name="notes" rows="4" 
                                  placeholder="Enter revision notes for the instructor..."
                                  required style="border-radius: 8px; border: 1px solid #d1d5db;"></textarea>
                        <small class="text-muted">Minimum 10 characters required</small>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" 
                                style="background-color: #6ba5b3; border: none; border-radius: 8px; padding: 10px 28px; font-weight: 500;">
                            Send for Revision
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openRevisionModal(examId) {
        const modal = new bootstrap.Modal(document.getElementById('revisionModal'));
        modal.show();
    }

    function approveExam(examId) {
        // Set the modal title
        document.getElementById('approvalModalTitle').textContent = 
            'Approve: {{ $exam->exam_title }} by {{ $exam->teacher ? $exam->teacher->first_name . " " . $exam->teacher->last_name : "Unknown" }}';
        
        // Set form action
        document.getElementById('approvalForm').action = `/programchair/manage-approval/${examId}/approve`;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
        modal.show();
    }
</script>
@endpush
@endsection