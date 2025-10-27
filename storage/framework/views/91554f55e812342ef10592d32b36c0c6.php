


<?php $__env->startSection('content'); ?>
    <style>
        /* Main Container */
        .exam-statistics-container {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 0;
            min-height: 85vh;
            margin: 20px;
            margin-left: 90px;
            transition: margin-left 0.3s;
        }

        .exam-statistics-container.expanded {
            margin-left: 240px;
        }

        /* Header Section */
        .exam-header {
            background: linear-gradient(135deg, #6ba5b3 0%, #5a8f9c 100%);
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .exam-header-left h1 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 8px 0;
        }

        .exam-schedule {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 400;
        }

        .class-filter-dropdown {
            background-color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 15px;
            font-weight: 500;
            color: #333;
            cursor: pointer;
            min-width: 200px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .class-filter-dropdown:focus {
            outline: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        /* Navigation Tabs */
        .nav-tabs-container {
            background-color: #f8f9fa;
            padding: 0 30px;
            border-bottom: 2px solid #e5e7eb;
            position: relative;
        }

        .nav-tabs-custom {
            display: flex;
            gap: 50px;
            border: none;
            position: relative;
        }

        .nav-tab-item {
            background: none;
            border: none;
            padding: 18px 0;
            font-size: 16px;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
            position: relative;
            transition: color 0.3s;
        }

        .nav-tab-item:hover {
            color: #495057;
        }

        .nav-tab-item.active {
            color: #6ba5b3;
        }

        /* Sliding indicator bar */
        .nav-indicator {
            position: absolute;
            bottom: -2px;
            height: 3px;
            background-color: #6ba5b3;
            transition: all 0.3s ease;
            border-radius: 3px 3px 0 0;
        }

        /* Content Section */
        .content-section {
            padding: 30px;
        }

        /* Summary Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-card-label {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card-value {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }

        .stat-card-subtext {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 5px;
        }

        /* Chart Container */
        .chart-container {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
        }

        .chart-placeholder {
            background: #f8f9fa;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 14px;
        }

        /* Question Analysis Cards */
        .question-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .question-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .question-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .question-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .question-icon.hardest {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .question-icon.easiest {
            background-color: #d1fae5;
            color: #059669;
        }

        .question-card-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }

        .question-card-content {
            font-size: 14px;
            color: #6c757d;
            line-height: 1.6;
        }

        .question-number {
            font-weight: 600;
            color: #1a1a1a;
        }

        .success-rate {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 12px;
            background-color: #f3f4f6;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }

        .success-rate.low {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .success-rate.high {
            background-color: #d1fae5;
            color: #059669;
        }

        /* Student List */
        .student-list {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }

        .student-item {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            color: #495057;
        }

        .student-item:last-child {
            border-bottom: none;
        }

        .student-name {
            font-weight: 500;
            color: #1a1a1a;
        }

        .student-class {
            color: #6c757d;
            font-size: 13px;
            margin-left: 8px;
        }

        /* Hidden class for tab content */
        .tab-content-panel {
            display: none;
        }

        .tab-content-panel.active {
            display: block;
        }

        /* ==================== QUESTIONS TAB STYLES START ==================== */
        /* Part Section Styles */
        .part-section {
            margin-bottom: 20px;
        }

        .section-header {
            background-color: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px 20px;
        }

        .part-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 5px 0;
        }

        .part-directions {
            font-size: 14px;
            color: #6c757d;
            margin: 0;
        }

        /* Question Analysis Card Styles */
        .question-analysis-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .question-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            gap: 20px;
        }

        .question-title-text {
            flex: 1;
            font-size: 15px;
            color: #1a1a1a;
            line-height: 1.5;
        }

        .question-type-badge {
            background-color: #e0f2f7;
            color: #0277bd;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .total-responses {
            font-size: 14px;
            color: #6c757d;
            white-space: nowrap;
        }

        /* Choices Container */
        .choices-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px;
        }

        .choice-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 10px 18px;
            transition: all 0.2s;
        }

        .choice-item.correct-choice {
            background-color: #4caf50;
            border-color: #4caf50;
            color: white;
        }

        .choice-label {
            font-weight: 600;
            font-size: 14px;
            min-width: 20px;
        }

        .choice-text {
            flex: 1;
            font-size: 14px;
            line-height: 1.4;
        }

        .choice-responses {
            font-size: 13px;
            font-style: italic;
            color: #6c757d;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .choice-percentage {
            font-weight: 600;
            font-style: normal;
        }

        .choice-item.correct-choice .choice-responses {
            color: rgba(255, 255, 255, 0.9);
        }

        /* Question Footer */
        .question-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .question-stats {
            font-size: 14px;
            color: #495057;
        }

        .question-avatar {
            flex-shrink: 0;
        }

        .avatar-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #e5e7eb;
        }

        /* Responsive for Questions Tab */
        @media (max-width: 768px) {
            .question-header-row {
                flex-direction: column;
                gap: 10px;
            }

            .choice-item {
                flex-wrap: wrap;
            }

            .question-footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
        /* ==================== QUESTIONS TAB STYLES END ==================== */

        /* Responsive */
        @media (max-width: 768px) {
            .exam-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .class-filter-dropdown {
                width: 100%;
            }

            .nav-tabs-custom {
                gap: 30px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="exam-statistics-container" id="examStatsContainer">
        <!-- Header Section -->
        <div class="exam-header">
            <div class="exam-header-left">
                <h1><?php echo e($exam->exam_title); ?></h1>
                <div class="exam-schedule">
                    Scheduled: <?php echo e($schedule); ?>

                </div>
                <div class="exam-schedule">
                    <?php echo e($exam->subject->subject_name); ?> | <?php echo e($totalItems); ?> items | <?php echo e($totalPoints); ?> points
                </div>
            </div>
            <div class="exam-header-right">
                <select class="class-filter-dropdown" id="classFilter" onchange="handleClassFilterChange()">
                    <option value="all">All Classes (<?php echo e($assignedClasses->count()); ?>)</option>
                    <?php $__currentLoopData = $assignedClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($class['class_id']); ?>"><?php echo e($class['display_name']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="nav-tabs-container">
            <div class="nav-tabs-custom" id="navTabs">
                <button class="nav-tab-item active" data-tab="summary" onclick="switchTab('summary')">
                    Summary
                </button>
                <button class="nav-tab-item" data-tab="questions" onclick="switchTab('questions')" style="display: none;">
                    Questions
                </button>
                <button class="nav-tab-item" data-tab="individual" onclick="switchTab('individual')" style="display: none;">
                    Individual
                </button>
                <div class="nav-indicator" id="navIndicator"></div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <!-- Summary Tab Content -->
            <div class="tab-content-panel active" id="summaryContent">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-label">Submissions</div>
                        <div class="stat-card-value"><?php echo e($submittedCount); ?>/<?php echo e($totalStudents); ?></div>
                        <div class="stat-card-subtext"><?php echo e($completionRate); ?>% completion rate</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">Highest Score</div>
                        <div class="stat-card-value"><?php echo e($highestScore); ?></div>
                        <div class="stat-card-subtext">Out of <?php echo e($totalPoints); ?> points</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">Lowest Score</div>
                        <div class="stat-card-value"><?php echo e($lowestScore); ?></div>
                        <div class="stat-card-subtext">Out of <?php echo e($totalPoints); ?> points</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">Average Time</div>
                        <div class="stat-card-value"><?php echo e($averageTime); ?></div>
                        <div class="stat-card-subtext">Completion time</div>
                    </div>
                </div>

                <!-- Score Distribution Chart -->
                <div class="chart-container">
                    <div class="chart-title">Score Distribution</div>
                    <div class="chart-placeholder">
                        Chart will be rendered here (use Chart.js or similar library)
                    </div>
                </div>

                <!-- Highest Scoring Students -->
                <div class="chart-container">
                    <div class="chart-title">Highest Scoring Students</div>
                    <div class="student-list">
                        <?php $__empty_1 = true; $__currentLoopData = $highestScoringStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="student-item">
                            <span class="student-name"><?php echo e($student['name']); ?></span>
                            <span class="student-class">(<?php echo e($student['class']); ?>)</span>
                            <span style="float: right; font-weight: 600; color: #059669;"><?php echo e($student['score']); ?></span>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div style="text-align: center; color: #6c757d; padding: 20px;">
                            No submissions yet
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Question Analysis -->
                <div class="question-cards-grid">
                    <div class="question-card">
                        <div class="question-card-header">
                            <div class="question-icon hardest">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <h3 class="question-card-title">Hardest Question</h3>
                        </div>
                        <div class="question-card-content">
                            <?php if($hardestQuestion): ?>
                                <span class="question-number">Question #<?php echo e($hardestQuestion['number']); ?>:</span> 
                                <?php echo e(Str::limit($hardestQuestion['question'], 100)); ?>

                                <div class="success-rate <?php echo e($hardestQuestion['success_rate'] < 50 ? 'low' : ($hardestQuestion['success_rate'] < 75 ? 'medium' : 'high')); ?>">
                                    Success Rate: <?php echo e($hardestQuestion['success_rate']); ?>%
                                </div>
                            <?php else: ?>
                                <div style="color: #6c757d;">No data available yet</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="question-card">
                        <div class="question-card-header">
                            <div class="question-icon easiest">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <h3 class="question-card-title">Easiest Question</h3>
                        </div>
                        <div class="question-card-content">
                            <?php if($easiestQuestion): ?>
                                <span class="question-number">Question #<?php echo e($easiestQuestion['number']); ?>:</span> 
                                <?php echo e(Str::limit($easiestQuestion['question'], 100)); ?>

                                <div class="success-rate <?php echo e($easiestQuestion['success_rate'] < 50 ? 'low' : ($easiestQuestion['success_rate'] < 75 ? 'medium' : 'high')); ?>">
                                    Success Rate: <?php echo e($easiestQuestion['success_rate']); ?>%
                                </div>
                            <?php else: ?>
                                <div style="color: #6c757d;">No data available yet</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== QUESTIONS TAB CONTENT START ==================== -->
            <!-- Questions Tab Content (Hidden by default) -->
            <div class="tab-content-panel" id="questionsContent">
                <div id="questionsLoading" style="text-align: center; padding: 40px; color: #6c757d;">
                    <i class="bi bi-hourglass-split" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p>Loading question statistics...</p>
                </div>
                <div id="questionsContainer" style="display: none;">
                    <!-- Questions will be dynamically loaded here -->
                </div>
            </div>
            <!-- ==================== QUESTIONS TAB CONTENT END ==================== -->

            <!-- Individual Tab Content (Hidden by default) -->
            <div class="tab-content-panel" id="individualContent">
                <h2>Individual Student Performance</h2>
                <p style="color: #6c757d;">Individual student performance data will be displayed here.</p>
            </div>
        </div>
    </div>

    <script>
        // Store exam ID for AJAX requests
        const examId = <?php echo e($exam->exam_id); ?>;
        
        // Initialize the sliding indicator position
        document.addEventListener('DOMContentLoaded', function() {
            updateIndicatorPosition();
            
            // Handle sidebar expansion
            const sidebar = document.querySelector('.sidebar');
            const statsContainer = document.getElementById('examStatsContainer');
            
            if (sidebar) {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === 'class') {
                            if (sidebar.classList.contains('expanded')) {
                                statsContainer.classList.add('expanded');
                            } else {
                                statsContainer.classList.remove('expanded');
                            }
                        }
                    });
                });
                
                observer.observe(sidebar, { attributes: true });
                
                // Initial check
                if (sidebar.classList.contains('expanded')) {
                    statsContainer.classList.add('expanded');
                }
            }
        });

        // Handle class filter change
        function handleClassFilterChange() {
            const filterValue = document.getElementById('classFilter').value;
            const questionsTab = document.querySelector('[data-tab="questions"]');
            const individualTab = document.querySelector('[data-tab="individual"]');

            if (filterValue === 'all') {
                // Show only Summary tab
                questionsTab.style.display = 'none';
                individualTab.style.display = 'none';
                switchTab('summary');
            } else {
                // Show all tabs
                questionsTab.style.display = 'block';
                individualTab.style.display = 'block';
            }

            updateIndicatorPosition();
            
            // Reset questions loaded flag to reload with new filter
            questionsLoaded = false;
            
            // Fetch filtered statistics
            fetchFilteredStats(filterValue);
        }

        // Fetch filtered statistics via AJAX
        function fetchFilteredStats(classId) {
            // Show loading state
            showLoadingState();
            
            fetch(`/instructor/exams-statistics/${examId}/filter?class_id=${classId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                updateStatistics(data);
            })
            .catch(error => {
                console.error('Error fetching statistics:', error);
                alert('Failed to load statistics. Please try again.');
            });
        }

        // Show loading state
        function showLoadingState() {
            const statsCards = document.querySelectorAll('.stat-card-value');
            statsCards.forEach(card => {
                card.style.opacity = '0.5';
            });
        }

        // Update statistics on the page
        function updateStatistics(data) {
            // Update stat cards
            const submissionsValue = document.querySelector('.stats-grid .stat-card:nth-child(1) .stat-card-value');
            const submissionsSubtext = document.querySelector('.stats-grid .stat-card:nth-child(1) .stat-card-subtext');
            submissionsValue.textContent = `${data.submittedCount}/${data.totalStudents}`;
            submissionsSubtext.textContent = `${data.completionRate}% completion rate`;
            
            const highestScoreValue = document.querySelector('.stats-grid .stat-card:nth-child(2) .stat-card-value');
            const highestScoreSubtext = document.querySelector('.stats-grid .stat-card:nth-child(2) .stat-card-subtext');
            highestScoreValue.textContent = data.highestScore;
            highestScoreSubtext.textContent = `Out of ${data.totalPoints} points`;
            
            const lowestScoreValue = document.querySelector('.stats-grid .stat-card:nth-child(3) .stat-card-value');
            const lowestScoreSubtext = document.querySelector('.stats-grid .stat-card:nth-child(3) .stat-card-subtext');
            lowestScoreValue.textContent = data.lowestScore;
            lowestScoreSubtext.textContent = `Out of ${data.totalPoints} points`;
            
            const averageTimeValue = document.querySelector('.stats-grid .stat-card:nth-child(4) .stat-card-value');
            averageTimeValue.textContent = data.averageTime;
            
            // Remove opacity
            document.querySelectorAll('.stat-card-value').forEach(card => {
                card.style.opacity = '1';
            });
            
            // Update highest scoring students
            updateHighestScoringStudents(data.highestScoringStudents);
            
            // Update hardest question
            updateQuestionCard(data.hardestQuestion, 'hardest');
            
            // Update easiest question
            updateQuestionCard(data.easiestQuestion, 'easiest');
        }

        // Update highest scoring students list
        function updateHighestScoringStudents(students) {
            const studentList = document.querySelector('.student-list');
            
            if (students.length === 0) {
                studentList.innerHTML = `
                    <div style="text-align: center; color: #6c757d; padding: 20px;">
                        No submissions yet
                    </div>
                `;
                return;
            }
            
            studentList.innerHTML = students.map(student => `
                <div class="student-item">
                    <span class="student-name">${student.name}</span>
                    <span class="student-class">(${student.class})</span>
                    <span style="float: right; font-weight: 600; color: #059669;">${student.score}</span>
                </div>
            `).join('');
        }

        // Update question card (hardest or easiest)
        function updateQuestionCard(question, type) {
            const cardIndex = type === 'hardest' ? 0 : 1;
            const questionCard = document.querySelectorAll('.question-card')[cardIndex];
            const contentDiv = questionCard.querySelector('.question-card-content');
            
            if (!question) {
                contentDiv.innerHTML = '<div style="color: #6c757d;">No data available yet</div>';
                return;
            }
            
            const successRateClass = question.success_rate < 50 ? 'low' : (question.success_rate < 75 ? 'medium' : 'high');
            const questionText = question.question.length > 100 
                ? question.question.substring(0, 100) + '...' 
                : question.question;
            
            contentDiv.innerHTML = `
                <span class="question-number">Question #${question.number}:</span> 
                ${questionText}
                <div class="success-rate ${successRateClass}">
                    Success Rate: ${question.success_rate}%
                </div>
            `;
        }

        // Switch between tabs
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content-panel').forEach(panel => {
                panel.classList.remove('active');
            });

            // Remove active class from all tabs
            document.querySelectorAll('.nav-tab-item').forEach(tab => {
                tab.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById(tabName + 'Content').classList.add('active');

            // Add active class to selected tab
            document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

            // Update indicator position
            updateIndicatorPosition();
            
            // Load questions data when Questions tab is clicked
            if (tabName === 'questions') {
                loadQuestionStats();
            }
        }

        // Update the position and width of the sliding indicator
        function updateIndicatorPosition() {
            const activeTab = document.querySelector('.nav-tab-item.active');
            const indicator = document.getElementById('navIndicator');

            if (activeTab && activeTab.offsetParent !== null) {
                const tabRect = activeTab.getBoundingClientRect();
                const containerRect = activeTab.parentElement.getBoundingClientRect();
                
                indicator.style.left = (activeTab.offsetLeft) + 'px';
                indicator.style.width = activeTab.offsetWidth + 'px';
            }
        }

        // Update indicator on window resize
        window.addEventListener('resize', updateIndicatorPosition);
        
        // Load question statistics
        let questionsLoaded = false;
        function loadQuestionStats() {
            if (questionsLoaded) return; // Only load once
            
            const filterValue = document.getElementById('classFilter').value;
            const questionsLoading = document.getElementById('questionsLoading');
            const questionsContainer = document.getElementById('questionsContainer');
            
            questionsLoading.style.display = 'block';
            questionsContainer.style.display = 'none';
            
            fetch(`/instructor/exams-statistics/${examId}/questions?class_id=${filterValue}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                renderQuestions(data.questions);
                questionsLoaded = true;
                questionsLoading.style.display = 'none';
                questionsContainer.style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching question statistics:', error);
                questionsLoading.innerHTML = '<p style="color: #dc2626;">Failed to load question statistics. Please try again.</p>';
            });
        }
        
        // Render questions
        function renderQuestions(questions) {
            const container = document.getElementById('questionsContainer');
            
            if (questions.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 40px;">No questions available.</p>';
                return;
            }
            
            const html = questions.map(q => {
                const typeLabel = getTypeLabel(q.item_type);
                const difficultyText = getDifficultyText(q.success_rate);
                
                return `
                    <div class="question-analysis-card">
                        <div class="question-header-row">
                            <div class="question-title-text">
                                <strong>Question ${q.order}.</strong> ${escapeHtml(q.question)}
                            </div>
                            <div class="question-type-badge">${typeLabel}</div>
                            <div class="total-responses">
                                ${q.total_responses} responses
                            </div>
                        </div>
                        
                        ${renderQuestionBody(q)}
                        
                        <div class="question-footer">
                            <div class="question-stats">
                                <strong>${q.success_rate}% (${q.correct_count})</strong> got the correct answer. ${difficultyText}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = html;
        }
        
        // Render question body based on type
        function renderQuestionBody(question) {
            if (question.item_type === 'mcq' || question.item_type === 'torf') {
                return renderChoices(question);
            } else if (question.item_type === 'iden' || question.item_type === 'enum' || question.item_type === 'essay') {
                return renderTextResponse(question);
            }
            return '';
        }
        
        // Render choices for MCQ and True/False
        function renderChoices(question) {
            if (!question.response_breakdown || question.response_breakdown.length === 0) {
                return '<div style="color: #6c757d; padding: 15px;">No responses yet</div>';
            }
            
            const choicesHtml = question.response_breakdown.map(choice => {
                const isCorrect = choice.is_correct;
                const correctClass = isCorrect ? 'correct-choice' : '';
                
                return `
                    <div class="choice-item ${correctClass}">
                        <div class="choice-label">${escapeHtml(choice.option)}</div>
                        <div class="choice-responses">
                            <span>${choice.count} responses</span>
                            <span class="choice-percentage">(${choice.percentage}%)</span>
                        </div>
                    </div>
                `;
            }).join('');
            
            return `<div class="choices-container">${choicesHtml}</div>`;
        }
        
        // Render text response statistics (for identification, enumeration, essay)
        function renderTextResponse(question) {
            return `
                <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; margin-top: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>Correct Responses:</strong> ${question.correct_count} / ${question.total_responses}
                        </div>
                        <div>
                            <strong>Success Rate:</strong> ${question.success_rate}%
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Get type label
        function getTypeLabel(type) {
            const labels = {
                'mcq': 'Multiple Choice',
                'torf': 'True or False',
                'iden': 'Identification',
                'enum': 'Enumeration',
                'essay': 'Essay'
            };
            return labels[type] || type.toUpperCase();
        }
        
        // Get difficulty text based on success rate
        function getDifficultyText(successRate) {
            if (successRate >= 80) return 'Students found it easy.';
            if (successRate >= 60) return 'Students found it moderate.';
            if (successRate >= 40) return 'Students found it challenging.';
            return 'Students found it difficult.';
        }
        
        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.Instructor.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\exam1\resources\views/instructor/exam-statistics/show.blade.php ENDPATH**/ ?>