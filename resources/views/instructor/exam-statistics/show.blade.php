{{-- This shows exam statistic per exam --}}
@extends('layouts.Instructor.app')

@section('content')
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

        /* ==================== INDIVIDUAL TAB STYLES START ==================== */
        /* Students Navigation */
        .students-nav-container {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .students-nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }

        .student-nav-item {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: left;
        }

        .student-nav-item:hover {
            border-color: #6ba5b3;
            background-color: #f0f9fa;
        }

        .student-nav-item.active {
            border-color: #6ba5b3;
            background-color: #e0f2f7;
        }

        .student-nav-name {
            font-weight: 600;
            font-size: 15px;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .student-nav-score {
            font-size: 13px;
            color: #6c757d;
        }

        /* Student Performance Header */
        .student-performance-header {
            background: linear-gradient(135deg, #e0f2f7 0%, #f0f9fa 100%);
            border: 1px solid #6ba5b3;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .student-performance-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .student-performance-stats {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .student-stat-item {
            display: flex;
            flex-direction: column;
        }

        .student-stat-label {
            font-size: 13px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .student-stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
        }

        /* Student Answer Styles */
        .choice-item.student-answer {
            font-weight: 600;
        }

        .choice-item.student-correct {
            background-color: #4caf50;
            border-color: #4caf50;
            color: white;
        }

        .choice-item.student-wrong {
            background-color: #f44336;
            border-color: #f44336;
            color: white;
        }

        .choice-item.correct-highlight {
            border: 2px solid #4caf50;
            background-color: #e8f5e9;
        }

        .answer-indicator {
            margin-left: auto;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .answer-indicator.correct {
            color: white;
        }

        .answer-indicator.wrong {
            color: white;
        }

        .choice-item.correct-highlight .choice-label,
        .choice-item.correct-highlight .choice-text {
            font-weight: 600;
            color: #2e7d32;
        }

        .choice-item.student-correct .choice-label,
        .choice-item.student-correct .choice-text,
        .choice-item.student-wrong .choice-label,
        .choice-item.student-wrong .choice-text {
            font-weight: 700;
        }
        /* ==================== INDIVIDUAL TAB STYLES END ==================== */

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

            .students-nav-grid {
                grid-template-columns: 1fr;
            }

            .student-performance-stats {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>

    <div class="exam-statistics-container" id="examStatsContainer">
        <!-- Header Section -->
        <div class="exam-header">
            <div class="exam-header-left">
                <h1>{{ $exam->exam_title }}</h1>
                <div class="exam-schedule">
                    Scheduled: {{ $schedule }}
                </div>
                <div class="exam-schedule">
                    {{ $exam->subject->subject_name }} | {{ $totalItems }} items | {{ $totalPoints }} points
                </div>
            </div>
            <div class="exam-header-right">
                <select class="class-filter-dropdown" id="classFilter" onchange="handleClassFilterChange()">
                    <option value="all">All Classes ({{ $assignedClasses->count() }})</option>
                    @foreach($assignedClasses as $class)
                        <option value="{{ $class['class_id'] }}">{{ $class['display_name'] }}</option>
                    @endforeach
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
                        <div class="stat-card-value">{{ $submittedCount }}/{{ $totalStudents }}</div>
                        <div class="stat-card-subtext">{{ $completionRate }}% completion rate</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">Highest Score</div>
                        <div class="stat-card-value">{{ $highestScore }}</div>
                        <div class="stat-card-subtext">Out of {{ $totalPoints }} points</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">Lowest Score</div>
                        <div class="stat-card-value">{{ $lowestScore }}</div>
                        <div class="stat-card-subtext">Out of {{ $totalPoints }} points</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">Average Time</div>
                        <div class="stat-card-value">{{ $averageTime }}</div>
                        <div class="stat-card-subtext">Completion time</div>
                    </div>
                </div>

                <!-- Score Distribution Chart -->
                <div class="chart-container">
                    <div class="chart-title">Score Distribution</div>
                    <div style="position: relative; height: 350px;">
                        <canvas id="scoreDistributionChart"></canvas>
                    </div>
                </div>

                <!-- Statistics Display Grid -->
                <div id="chartStatistics" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 12px; border: 1px solid #e5e7eb;">
                    <!-- Statistics will be dynamically rendered here -->
                </div>

                <!-- Highest Scoring Students -->
                <div class="chart-container">
                    <div class="chart-title">Highest Scoring Students</div>
                    <div class="student-list">
                        @forelse($highestScoringStudents as $student)
                        <div class="student-item">
                            <span class="student-name">{{ $student['name'] }}</span>
                            <span class="student-class">({{ $student['class'] }})</span>
                            <span style="float: right; font-weight: 600; color: #059669;">{{ $student['score'] }}</span>
                        </div>
                        @empty
                        <div style="text-align: center; color: #6c757d; padding: 20px;">
                            No submissions yet
                        </div>
                        @endforelse
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
                            @if($hardestQuestion)
                                <span class="question-number">Question #{{ $hardestQuestion['number'] }}:</span> 
                                {{ Str::limit($hardestQuestion['question'], 100) }}
                                <div class="success-rate {{ $hardestQuestion['success_rate'] < 50 ? 'low' : ($hardestQuestion['success_rate'] < 75 ? 'medium' : 'high') }}">
                                    Success Rate: {{ $hardestQuestion['success_rate'] }}%
                                </div>
                            @else
                                <div style="color: #6c757d;">No data available yet</div>
                            @endif
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
                            @if($easiestQuestion)
                                <span class="question-number">Question #{{ $easiestQuestion['number'] }}:</span> 
                                {{ Str::limit($easiestQuestion['question'], 100) }}
                                <div class="success-rate {{ $easiestQuestion['success_rate'] < 50 ? 'low' : ($easiestQuestion['success_rate'] < 75 ? 'medium' : 'high') }}">
                                    Success Rate: {{ $easiestQuestion['success_rate'] }}%
                                </div>
                            @else
                                <div style="color: #6c757d;">No data available yet</div>
                            @endif
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

            <!-- ==================== INDIVIDUAL TAB CONTENT START ==================== -->
            <!-- Individual Tab Content (Hidden by default) -->
            <div class="tab-content-panel" id="individualContent">
                <div id="individualLoading" style="text-align: center; padding: 40px; color: #6c757d;">
                    <i class="bi bi-hourglass-split" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p>Loading student data...</p>
                </div>
                
                <!-- Students Navigation -->
                <div id="studentsNav" style="display: none;">
                    <div class="students-nav-container">
                        <div class="student-nav-controls">
                            <button id="prevStudentBtn" class="student-nav-btn" onclick="navigateStudent(-1)">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <div class="student-nav-info" id="currentStudentInfo">
                                <!-- Current student info will be displayed here -->
                            </div>
                            <button id="nextStudentBtn" class="student-nav-btn" onclick="navigateStudent(1)">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Student Performance Content -->
                <div id="studentPerformanceContainer" style="display: none;">
                    <!-- Will be populated when student is selected -->
                </div>
            </div>
            <!-- ==================== INDIVIDUAL TAB CONTENT END ==================== -->
        </div>
    </div>
    
    <!-- Override Modal -->
    <div id="overrideModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Override Answer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="overrideForm">
                        <input type="hidden" id="override_attempt_id">
                        <input type="hidden" id="override_answer_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Mark as:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_correct" id="markCorrect" value="1">
                                <label class="form-check-label" for="markCorrect">
                                    <i class="bi bi-check-circle-fill text-success"></i> Correct
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_correct" id="markIncorrect" value="0">
                                <label class="form-check-label" for="markIncorrect">
                                    <i class="bi bi-x-circle-fill text-danger"></i> Incorrect
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="points_earned" class="form-label">Points Earned:</label>
                            <input type="number" class="form-control" id="points_earned" name="points_earned" 
                                   min="0" step="0.01" required>
                            <small class="text-muted">Max: <span id="max_points"></span> points</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitOverride()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        // Store exam ID for AJAX requests
        const examId = {{ $exam->exam_id }};
        
        // Chart instance variable
        let scoreDistributionChart = null;
        
        // Initialize the sliding indicator position
        document.addEventListener('DOMContentLoaded', function() {
            // Restore previous filter and tab selection from localStorage
            restoreUserSelection();
            
            updateIndicatorPosition();
            
            // Load score distribution histogram
            fetchScoreDistribution();
            
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
        
        // Restore user's previous selection from localStorage
        function restoreUserSelection() {
            const storageKey = `exam_${examId}_selection`;
            const savedSelection = localStorage.getItem(storageKey);
            
            if (savedSelection) {
                try {
                    const selection = JSON.parse(savedSelection);
                    
                    // Restore class filter
                    const classFilter = document.getElementById('classFilter');
                    if (classFilter && selection.classFilter) {
                        classFilter.value = selection.classFilter;
                        handleClassFilterChange(false); // Don't save to localStorage again
                    }
                    
                    // Restore active tab
                    if (selection.activeTab) {
                        switchTab(selection.activeTab, false); // Don't save to localStorage again
                    }
                } catch (e) {
                    console.error('Error restoring selection:', e);
                }
            }
        }
        
        // Save user's selection to localStorage
        function saveUserSelection() {
            const storageKey = `exam_${examId}_selection`;
            const classFilter = document.getElementById('classFilter');
            const activeTab = document.querySelector('.nav-tab-item.active')?.getAttribute('data-tab') || 'summary';
            
            const selection = {
                classFilter: classFilter?.value || 'all',
                activeTab: activeTab
            };
            
            localStorage.setItem(storageKey, JSON.stringify(selection));
        }

        // Handle class filter change
        function handleClassFilterChange(shouldSave = true) {
            const filterValue = document.getElementById('classFilter').value;
            const questionsTab = document.querySelector('[data-tab="questions"]');
            const individualTab = document.querySelector('[data-tab="individual"]');

            console.log('Filter changed to:', filterValue);
            
            // Reset loaded flags - this forces reload when tabs are switched or data is requested
            questionsLoaded = false;
            studentsLoaded = false;
            
            // Clear data
            studentsData = [];
            selectedStudentIndex = 0;

            if (filterValue === 'all') {
                // Show only Summary tab
                questionsTab.style.display = 'none';
                individualTab.style.display = 'none';
            } else {
                // Show all tabs
                questionsTab.style.display = 'block';
                individualTab.style.display = 'block';
            }
            
            // Always switch to Summary tab when class filter changes
            switchTab('summary', false);

            // Force indicator update after DOM changes
            setTimeout(() => {
                updateIndicatorPosition();
            }, 10);
            
            // Fetch filtered statistics for Summary tab
            fetchFilteredStats(filterValue);
            
            // Reload histogram with new filter
            fetchScoreDistribution();
            
            // Save filter selection to localStorage (but not tab selection)
            if (shouldSave) {
                localStorage.setItem('exam_filter_' + examId, filterValue);
            }
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

        // Fetch score distribution data for histogram
        function fetchScoreDistribution() {
            const classId = document.getElementById('classFilter').value;
            
            // Validate class_id on client side
            if (classId && classId !== 'all' && !/^[a-zA-Z0-9_-]+$/.test(classId)) {
                console.error('Invalid class ID format');
                return;
            }
            
            fetch(`/instructor/exams-statistics/${examId}/score-distribution?class_id=${encodeURIComponent(classId)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 403) {
                        throw new Error('Unauthorized access to class data');
                    } else if (response.status === 404) {
                        throw new Error('Exam not found');
                    } else if (response.status === 429) {
                        throw new Error('Too many requests. Please wait a moment.');
                    } else if (response.status === 500) {
                        throw new Error('Server error occurred');
                    }
                    throw new Error('Failed to load score distribution');
                }
                return response.json();
            })
            .then(data => {
                // Validate response data structure
                if (!data || typeof data !== 'object') {
                    throw new Error('Invalid response format');
                }
                
                if (data.error) {
                    console.error('Server error:', data.error);
                    showChartError(data.error);
                    return;
                }
                
                if (!data.distribution || !data.statistics) {
                    throw new Error('Incomplete data received');
                }
                
                renderScoreDistributionChart(data);
                renderChartStatistics(data.statistics, data.totalPoints);
            })
            .catch(error => {
                console.error('Error fetching score distribution:', error);
                showChartError(error.message || 'Failed to load chart data. Please try again.');
            });
        }

        // Show error message in chart area
        function showChartError(message) {
            const ctx = document.getElementById('scoreDistributionChart');
            const container = ctx.parentElement;
            
            if (scoreDistributionChart) {
                scoreDistributionChart.destroy();
                scoreDistributionChart = null;
            }
            
            ctx.style.display = 'none';
            
            const existingError = container.querySelector('.chart-error-message');
            if (existingError) {
                existingError.remove();
            }
            
            container.insertAdjacentHTML('beforeend',
                `<div class="chart-error-message" style="text-align: center; color: #dc3545; padding: 40px; margin: 0; background: #fff3f3; border: 1px dashed #dc3545; border-radius: 8px;">
                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                    <p style="margin: 0; font-weight: 500;">${message}</p>
                </div>`
            );
            
            // Clear statistics
            document.getElementById('chartStatistics').innerHTML = '';
        }

        // Render the score distribution histogram
        function renderScoreDistributionChart(data) {
            const ctx = document.getElementById('scoreDistributionChart').getContext('2d');
            
            // Destroy existing chart if it exists
            if (scoreDistributionChart) {
                scoreDistributionChart.destroy();
            }
            
            const labels = Object.keys(data.distribution);
            const values = Object.values(data.distribution);
            const totalPoints = data.totalPoints;
            
            // Check if there's any data
            if (data.statistics.totalStudents === 0) {
                ctx.canvas.style.display = 'none';
                const chartContainer = ctx.canvas.parentElement;
                const noDataMsg = chartContainer.querySelector('.no-data-message');
                if (!noDataMsg) {
                    chartContainer.insertAdjacentHTML('beforeend',
                        '<p class="no-data-message" style="text-align: center; color: #6c757d; padding: 40px; margin: 0;">No submissions yet. Chart will appear once students submit.</p>');
                }
                return;
            } else {
                ctx.canvas.style.display = 'block';
                const noDataMsg = ctx.canvas.parentElement.querySelector('.no-data-message');
                if (noDataMsg) {
                    noDataMsg.remove();
                }
            }
            
            scoreDistributionChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Number of Students',
                        data: values,
                        backgroundColor: labels.map((label) => {
                            // Extract the end score from the range label
                            const endScore = parseInt(label.split('-')[1]);
                            const percentage = (endScore / totalPoints) * 100;
                            
                            // Color based on percentage of total points
                            if (percentage < 60) return '#f44336';      // Red - Failing (<60%)
                            if (percentage < 70) return '#ff9800';      // Orange - Below Average (60-69%)
                            if (percentage < 80) return '#ffc107';      // Yellow - Average (70-79%)
                            if (percentage < 90) return '#4caf50';      // Green - Good (80-89%)
                            return '#2196f3';                           // Blue - Excellent (90-100%)
                        }),
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        borderRadius: 6,
                        barPercentage: 0.9,
                        categoryPercentage: 0.95
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: false
                        },
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                title: function(context) {
                                    const label = context[0].label;
                                    const endScore = parseInt(label.split('-')[1]);
                                    const percentage = ((endScore / totalPoints) * 100).toFixed(1);
                                    return `Score Range: ${label} (${percentage}% of total)`;
                                },
                                label: function(context) {
                                    const count = context.parsed.y;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((count / total) * 100).toFixed(1) : 0;
                                    
                                    return [
                                        `Students: ${count}`,
                                        `Percentage: ${percentage}%`
                                    ];
                                },
                                afterLabel: function(context) {
                                    const label = context.label;
                                    const endScore = parseInt(label.split('-')[1]);
                                    const percentage = (endScore / totalPoints) * 100;
                                    
                                    if (percentage < 60) return '(Failing)';
                                    if (percentage < 70) return '(Below Average)';
                                    if (percentage < 80) return '(Average)';
                                    if (percentage < 90) return '(Good)';
                                    return '(Excellent)';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 13 },
                                color: '#495057'
                            },
                            title: {
                                display: true,
                                text: 'Number of Students',
                                font: { size: 14, weight: '600' },
                                color: '#1a1a1a'
                            },
                            grid: {
                                color: '#e5e7eb',
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 13 },
                                color: '#495057',
                                maxRotation: 45,
                                minRotation: 0
                            },
                            title: {
                                display: true,
                                text: `Score Range (out of ${totalPoints} points)`,
                                font: { size: 14, weight: '600' },
                                color: '#1a1a1a'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }

        // Render histogram statistics
        function renderChartStatistics(stats, totalPoints) {
            const container = document.getElementById('chartStatistics');
            
            const html = `
                <div style="text-align: center;">
                    <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;">Average</div>
                    <div style="font-size: 20px; font-weight: 700; color: #1a1a1a;">${stats.average} / ${totalPoints}</div>
                    <div style="font-size: 11px; color: #9ca3af;">(${((stats.average / totalPoints) * 100).toFixed(1)}%)</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;">Median</div>
                    <div style="font-size: 20px; font-weight: 700; color: #1a1a1a;">${stats.median} / ${totalPoints}</div>
                    <div style="font-size: 11px; color: #9ca3af;">(${((stats.median / totalPoints) * 100).toFixed(1)}%)</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;">Pass Rate</div>
                    <div style="font-size: 20px; font-weight: 700; color: ${stats.passRate >= 75 ? '#4caf50' : stats.passRate >= 60 ? '#ff9800' : '#f44336'};">${stats.passRate}%</div>
                    <div style="font-size: 11px; color: #9ca3af;">(≥${stats.passingScore} pts)</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;">Score Range</div>
                    <div style="font-size: 20px; font-weight: 700; color: #1a1a1a;">${stats.lowestScore} - ${stats.highestScore}</div>
                    <div style="font-size: 11px; color: #9ca3af;">out of ${totalPoints}</div>
                </div>
            `;
            
            container.innerHTML = html;
        }

        // Switch between tabs
        function switchTab(tabName, shouldSave = true) {
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
            
            // Load students data when Individual tab is clicked
            if (tabName === 'individual') {
                loadStudentsData();
            }
            
            // Save selection to localStorage
            if (shouldSave) {
                saveUserSelection();
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
                console.log('Question statistics received:', data);
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
            
            // Check if there are any responses across all questions
            const totalResponses = questions.reduce((sum, q) => sum + (q.total_responses || 0), 0);
            if (totalResponses === 0) {
                container.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 40px;">No responses have been submitted yet.</p>';
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
        function renderChoices(question, studentAnswer = null) {
            console.log('Rendering choices for question:', question);
            console.log('Student answer:', studentAnswer);
            
            // Ensure we have all options, even if no responses
            let choices = [];
            
            if (question.response_breakdown && question.response_breakdown.length > 0) {
                choices = question.response_breakdown;
            } else if (question.options && question.options.length > 0) {
                // If no response_breakdown but options exist, create structure with 0 responses
                choices = question.options.map((opt, index) => ({
                    option: opt.option || String.fromCharCode(65 + index), // A, B, C, D
                    text: opt.text || opt.option_text || opt.content || '',
                    count: 0,
                    percentage: 0,
                    is_correct: opt.is_correct || opt.correct || false
                }));
            }
            
            if (choices.length === 0) {
                return '<div style="color: #6c757d; padding: 15px;">No options available</div>';
            }
            
            const choicesHtml = choices.map(choice => {
                const isCorrect = choice.is_correct;
                const isStudentAnswer = studentAnswer && choice.option === studentAnswer;
                const studentCorrect = isStudentAnswer && isCorrect;
                const studentWrong = isStudentAnswer && !isCorrect;
                
                let choiceClasses = 'choice-item';
                if (studentCorrect) {
                    choiceClasses += ' student-correct student-answer';
                } else if (studentWrong) {
                    choiceClasses += ' student-wrong student-answer';
                } else if (studentAnswer && isCorrect && !isStudentAnswer) {
                    // Highlight correct answer when student got it wrong
                    choiceClasses += ' correct-highlight';
                } else if (!studentAnswer && isCorrect) {
                    // No student answer context - show as correct choice
                    choiceClasses += ' correct-choice';
                }
                
                const answerIndicator = isStudentAnswer 
                    ? (studentCorrect 
                        ? '<span class="answer-indicator correct"><i class="bi bi-check-circle-fill"></i></span>'
                        : '<span class="answer-indicator wrong"><i class="bi bi-x-circle-fill"></i></span>')
                    : '';
                
                return `
                    <div class="${choiceClasses}">
                        <div class="choice-label">${escapeHtml(choice.option)}</div>
                        <div class="choice-text">${escapeHtml(choice.text || choice.option_text || 'No text provided')}</div>
                        ${studentAnswer ? '' : `
                        <div class="choice-responses">
                            <span>${choice.count} responses</span>
                            <span class="choice-percentage">(${choice.percentage}%)</span>
                        </div>
                        `}
                        ${answerIndicator}
                    </div>
                `;
            }).join('');
            
            return `<div class="choices-container">${choicesHtml}</div>`;
        }
        
        // Render text response statistics (for identification, enumeration, essay)
        function renderTextResponse(question) {
            let expectedAnswerHtml = '';
            
            // Show expected answer for IDEN and ENUM
            if (question.item_type === 'iden' && question.expected_answer) {
                expectedAnswerHtml = `
                    <div style="margin-bottom: 10px; padding: 10px; background: #e3f2fd; border-left: 3px solid #2196f3; border-radius: 4px;">
                        <strong>Expected Answer:</strong> ${escapeHtml(question.expected_answer)}
                    </div>
                `;
            } else if (question.item_type === 'enum' && question.expected_answer) {
                const answers = Array.isArray(question.expected_answer) 
                    ? question.expected_answer 
                    : [question.expected_answer];
                const answersList = answers.map(ans => `<li>${escapeHtml(ans)}</li>`).join('');
                const enumTypeLabel = question.enum_type === 'unordered' ? ' (any order)' : ' (in order)';
                
                expectedAnswerHtml = `
                    <div style="margin-bottom: 10px; padding: 10px; background: #e3f2fd; border-left: 3px solid #2196f3; border-radius: 4px;">
                        <strong>Expected Answers${enumTypeLabel}:</strong>
                        <ol style="margin: 5px 0 0 20px; padding: 0;">
                            ${answersList}
                        </ol>
                    </div>
                `;
            } else if (question.item_type === 'essay' && question.rubric) {
                // Show rubric for essay questions
                let rubricContent = '';
                
                try {
                    // Try to parse as JSON (structured rubric)
                    const rubricData = JSON.parse(question.rubric);
                    
                    if (Array.isArray(rubricData)) {
                        // Array format: [{"talking_point": "...", "weight": 5}, ...]
                        rubricContent = `
                            <div style="margin-top: 10px;">
                                ${rubricData.map((item, index) => `
                                    <div style="display: flex; align-items: start; gap: 12px; padding: 10px; background: rgba(255, 255, 255, 0.7); border-radius: 6px; margin-bottom: 8px; border-left: 3px solid #9c27b0;">
                                        <div style="flex-shrink: 0; width: 24px; height: 24px; background: #9c27b0; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                                            ${index + 1}
                                        </div>
                                        <div style="flex: 1; color: #4a148c;">
                                            ${escapeHtml(item.talking_point || item.description || item.criterion || '')}
                                        </div>
                                        <div style="flex-shrink: 0; background: #6a1b9a; color: white; padding: 4px 12px; border-radius: 12px; font-weight: 600; font-size: 13px;">
                                            ${item.weight || item.points || 0} pts
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        `;
                    } else {
                        // Object format or other JSON structure - display as formatted text
                        rubricContent = `
                            <div style="white-space: pre-wrap; line-height: 1.6; color: #4a148c; background: rgba(255, 255, 255, 0.6); padding: 12px; border-radius: 6px;">
                                ${escapeHtml(JSON.stringify(rubricData, null, 2))}
                            </div>
                        `;
                    }
                } catch (e) {
                    // Not JSON, display as plain text
                    rubricContent = `
                        <div style="white-space: pre-wrap; line-height: 1.6; color: #4a148c; background: rgba(255, 255, 255, 0.6); padding: 12px; border-radius: 6px;">
                            ${escapeHtml(question.rubric)}
                        </div>
                    `;
                }
                
                expectedAnswerHtml = `
                    <div style="margin-bottom: 15px; padding: 15px; background: linear-gradient(135deg, #f3e5f5 0%, #fce4ec 100%); border-left: 4px solid #9c27b0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <i class="bi bi-clipboard-check" style="font-size: 20px; color: #9c27b0;"></i>
                            <strong style="font-size: 16px; color: #6a1b9a;">Grading Rubric (${question.points_awarded} points)</strong>
                        </div>
                        ${rubricContent}
                    </div>
                `;
            }
            
            return `
                ${expectedAnswerHtml}
                <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; margin-top: 15px;">
                    ${question.item_type === 'essay' 
                        ? `<div style="text-align: center;">
                            <div style="font-size: 14px; color: #6c757d; margin-bottom: 5px;">Average Score</div>
                            <div style="font-size: 28px; font-weight: 700; color: #1a1a1a;">
                                ${question.total_responses > 0 
                                    ? ((question.correct_count * question.points_awarded / question.total_responses) || 0).toFixed(1)
                                    : '0.0'
                                } 
                                <span style="font-size: 16px; color: #6c757d;">/ ${question.points_awarded}</span>
                            </div>
                            <div style="font-size: 13px; color: #6c757d; margin-top: 5px;">
                                Based on ${question.total_responses} ${question.total_responses === 1 ? 'response' : 'responses'}
                            </div>
                          </div>`
                        : `<div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>Correct Responses:</strong> ${question.correct_count} / ${question.total_responses}
                            </div>
                            <div>
                                <strong>Wrong Responses:</strong> ${question.wrong_count} / ${question.total_responses}
                            </div>
                            <div>
                                <strong>Success Rate:</strong> ${question.success_rate}%
                            </div>
                          </div>`
                    }
                </div>
            `;
        }
        
        // ==================== INDIVIDUAL TAB FUNCTIONS ====================
        let studentsData = [];
        let studentsLoaded = false;
        let selectedStudentIndex = 0;
        
        // Load students data
        function loadStudentsData() {
            console.log('loadStudentsData called, studentsLoaded:', studentsLoaded);
            
            const filterValue = document.getElementById('classFilter').value;
            const individualLoading = document.getElementById('individualLoading');
            const studentsNav = document.getElementById('studentsNav');
            const studentPerformanceContainer = document.getElementById('studentPerformanceContainer');
            
            // If already loaded with current filter, just show the UI
            if (studentsLoaded && studentsData.length > 0) {
                console.log('Data already loaded, showing UI');
                individualLoading.style.display = 'none';
                studentsNav.style.display = 'block';
                studentPerformanceContainer.style.display = 'block';
                updateStudentNavigation();
                return;
            }
            
            // Show loading state
            individualLoading.style.display = 'block';
            studentsNav.style.display = 'none';
            studentPerformanceContainer.style.display = 'none';
            
            console.log('Fetching student data for class:', filterValue);
            
            fetch(`/instructor/exams-statistics/${examId}/individual?class_id=${filterValue}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Student data received:', data);
                studentsData = data.students;
                
                // Sort students alphabetically by surname (last name first)
                studentsData.sort((a, b) => {
                    const nameA = a.name.split(' ').reverse().join(' ').toLowerCase();
                    const nameB = b.name.split(' ').reverse().join(' ').toLowerCase();
                    return nameA.localeCompare(nameB);
                });
                
                console.log('Students sorted:', studentsData.length, 'students');
                studentsLoaded = true;
                
                if (studentsData.length === 0) {
                    individualLoading.innerHTML = '<p style="color: #6c757d;">No student submissions yet.</p>';
                    return;
                }
                
                selectedStudentIndex = 0;
                console.log('Updating student navigation...');
                updateStudentNavigation();
                console.log('Rendering student performance...');
                renderStudentPerformance(0); // Show first student by default
                
                individualLoading.style.display = 'none';
                studentsNav.style.display = 'block';
                studentPerformanceContainer.style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching student data:', error);
                individualLoading.innerHTML = '<p style="color: #dc2626;">Failed to load student data. Please try again.</p>';
            });
        }
        
        // Update student navigation display
        function updateStudentNavigation() {
            console.log('updateStudentNavigation called, selectedStudentIndex:', selectedStudentIndex, 'total students:', studentsData.length);
            
            const currentStudentInfo = document.getElementById('currentStudentInfo');
            const prevBtn = document.getElementById('prevStudentBtn');
            const nextBtn = document.getElementById('nextStudentBtn');
            
            if (!currentStudentInfo || !prevBtn || !nextBtn) {
                console.error('Navigation elements not found!', {currentStudentInfo, prevBtn, nextBtn});
                return;
            }
            
            if (studentsData.length === 0) {
                console.log('No students data available');
                return;
            }
            
            const student = studentsData[selectedStudentIndex];
            console.log('Current student:', student);
            
            const percentage = Math.round((student.score / {{ $totalPoints }}) * 100);
            const currentPosition = selectedStudentIndex + 1;
            const totalStudents = studentsData.length;
            
            currentStudentInfo.innerHTML = `
                <div style="text-align: center;">
                    <div style="font-size: 20px; font-weight: 600; color: #1a1a1a; margin-bottom: 5px;">
                        ${escapeHtml(student.name)}
                    </div>
                    <div style="font-size: 14px; color: #6c757d; margin-bottom: 10px;">
                        ${student.id_number} | ${student.class}
                    </div>
                    <div style="display: flex; justify-content: center; gap: 20px; align-items: center; flex-wrap: wrap;">
                        <span style="font-size: 15px; color: #495057;">
                            Score: <strong>${student.score}/{{ $totalPoints }}</strong>
                        </span>
                        <span style="font-size: 15px; font-weight: 600; color: ${percentage >= 75 ? '#059669' : percentage >= 50 ? '#f59e0b' : '#dc2626'};">
                            ${percentage}%
                        </span>
                        <span style="font-size: 13px; color: #6c757d;">
                            Student ${currentPosition} of ${totalStudents}
                        </span>
                    </div>
                </div>
            `;
            
            // Disable/enable navigation buttons
            prevBtn.disabled = selectedStudentIndex === 0;
            nextBtn.disabled = selectedStudentIndex === studentsData.length - 1;
        }
        
        // Navigate to next/previous student
        function navigateStudent(direction) {
            const newIndex = selectedStudentIndex + direction;
            
            if (newIndex >= 0 && newIndex < studentsData.length) {
                selectedStudentIndex = newIndex;
                updateStudentNavigation();
                renderStudentPerformance(newIndex);
            }
        }
        
        // Render student performance
        function renderStudentPerformance(index) {
            const student = studentsData[index];
            const container = document.getElementById('studentPerformanceContainer');
            const percentage = Math.round((student.score / {{ $totalPoints }}) * 100);
            
            const questionsHtml = student.answers.map(answer => {
                const statusColor = answer.is_correct ? '#059669' : '#dc2626';
                const statusBg = answer.is_correct ? '#d1fae5' : '#fee2e2';
                const statusIcon = answer.is_correct ? 'check-circle-fill' : 'x-circle-fill';
                const typeLabel = getTypeLabel(answer.item_type);
                
                // Build options display for MCQ/T/F
                let optionsHtml = '';
                if ((answer.item_type === 'mcq' || answer.item_type === 'torf') && answer.options) {
                    const correctIndices = answer.correct_indices;
                    const studentAnswerRaw = answer.student_answer_raw;
                    
                    // Parse student answer if it's JSON
                    let studentIndices = [];
                    if (studentAnswerRaw) {
                        try {
                            const parsed = JSON.parse(studentAnswerRaw);
                            studentIndices = Array.isArray(parsed) ? parsed : [parsed];
                        } catch {
                            // If not JSON, might be direct index
                            studentIndices = [parseInt(studentAnswerRaw)];
                        }
                    }
                    
                    // Determine correct indices
                    let correctIndexArray = [];
                    if (answer.item_type === 'torf' && correctIndices) {
                        correctIndexArray = correctIndices.correct === 'true' ? [0] : [1];
                    } else if (Array.isArray(correctIndices)) {
                        correctIndexArray = correctIndices;
                    } else if (typeof correctIndices === 'number') {
                        correctIndexArray = [correctIndices];
                    }
                    
                    optionsHtml = '<div style="margin: 15px 0;">';
                    answer.options.forEach((option, idx) => {
                        const isCorrect = correctIndexArray.includes(idx);
                        const isSelected = studentIndices.includes(idx);
                        const label = String.fromCharCode(65 + idx);
                        
                        let optionStyle = 'padding: 10px; margin: 5px 0; border-radius: 6px; border: 2px solid ';
                        let icon = '';
                        
                        if (isCorrect && isSelected) {
                            optionStyle += '#059669; background: #d1fae5;';
                            icon = '<i class="bi bi-check-circle-fill" style="color: #059669; margin-right: 8px;"></i>';
                        } else if (isCorrect) {
                            optionStyle += '#6ba5b3; background: #e3f2fd;';
                            icon = '<i class="bi bi-check-circle" style="color: #6ba5b3; margin-right: 8px;"></i>';
                        } else if (isSelected) {
                            optionStyle += '#dc2626; background: #fee2e2;';
                            icon = '<i class="bi bi-x-circle-fill" style="color: #dc2626; margin-right: 8px;"></i>';
                        } else {
                            optionStyle += '#e5e7eb; background: #f9fafb;';
                        }
                        
                        optionsHtml += `
                            <div style="${optionStyle}">
                                ${icon}<strong>${label}.</strong> ${escapeHtml(option)}
                            </div>
                        `;
                    });
                    optionsHtml += '</div>';
                    
                    // Add student answer summary
                    optionsHtml += `
                        <div style="padding: 12px; background: ${statusBg}; border-left: 3px solid ${statusColor}; border-radius: 4px; margin-top: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-${statusIcon}" style="color: ${statusColor}; font-size: 18px;"></i>
                                <strong style="color: ${statusColor};">Student answered: ${escapeHtml(answer.student_answer)}</strong>
                            </div>
                        </div>
                    `;
                } else {
                    // For text-based questions, show expected and student answer
                    if (answer.correct_answer) {
                        optionsHtml += `
                            <div style="padding: 10px; background: #f8f9fa; border-left: 3px solid #6ba5b3; border-radius: 4px; margin: 10px 0;">
                                <strong>Expected Answer:</strong> ${escapeHtml(answer.correct_answer)}
                            </div>
                        `;
                    }
                    
                    optionsHtml += `
                        <div style="padding: 12px; background: ${statusBg}; border-left: 3px solid ${statusColor}; border-radius: 4px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <i class="bi bi-${statusIcon}" style="color: ${statusColor}; font-size: 18px;"></i>
                                <strong style="color: ${statusColor};">${answer.is_correct ? 'Correct' : 'Incorrect'}</strong>
                            </div>
                            <div style="color: #1a1a1a;">
                                <strong>Student's Answer:</strong> ${escapeHtml(answer.student_answer)}
                            </div>
                        </div>
                    `;
                    
                    // Add AI feedback section for essay questions
                    if (answer.item_type === 'essay') {
                        if (answer.ai_feedback) {
                            const confidenceColor = answer.ai_confidence >= 80 ? '#059669' : 
                                                   answer.ai_confidence >= 60 ? '#f59e0b' : '#dc2626';
                            const requiresReview = answer.requires_manual_review || answer.ai_confidence < 70;
                            
                            optionsHtml += `
                                <div style="margin-top: 15px; padding: 15px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #7dd3fc; border-radius: 8px;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                                        <i class="bi bi-robot" style="color: #0284c7; font-size: 18px;"></i>
                                        <strong style="color: #0369a1; font-size: 15px;">AI Grading Feedback</strong>
                                        ${requiresReview ? '<span style="background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; margin-left: auto;">MANUAL REVIEW NEEDED</span>' : ''}
                                    </div>
                                    <div style="color: #0c4a6e; line-height: 1.6; margin-bottom: 10px;">
                                        ${escapeHtml(answer.ai_feedback)}
                                    </div>
                                    ${answer.ai_confidence ? `
                                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px; padding-top: 10px; border-top: 1px solid #bae6fd;">
                                        <span style="font-size: 13px; color: #475569;">AI Confidence:</span>
                                        <div style="flex: 1; background: #e2e8f0; border-radius: 10px; height: 8px; overflow: hidden;">
                                            <div style="background: ${confidenceColor}; height: 100%; width: ${answer.ai_confidence}%; border-radius: 10px; transition: width 0.3s;"></div>
                                        </div>
                                        <span style="font-weight: 600; color: ${confidenceColor}; font-size: 13px;">${answer.ai_confidence}%</span>
                                    </div>
                                    ` : ''}
                                </div>
                            `;
                        } else {
                            // No AI feedback available
                            optionsHtml += `
                                <div style="margin-top: 15px; padding: 12px; background: #f8f9fa; border: 1px dashed #d1d5db; border-radius: 8px; text-align: center;">
                                    <i class="bi bi-info-circle" style="color: #6c757d; font-size: 16px; margin-right: 6px;"></i>
                                    <span style="color: #6c757d; font-size: 14px; font-style: italic;">No AI feedback available - Manual grading required</span>
                                </div>
                            `;
                        }
                    }
                }
                
                return `
                    <div class="question-analysis-card" data-answer-id="${answer.answer_id}">
                        <div class="question-header-row">
                            <div class="question-title-text">
                                <strong>Question ${answer.question_number}.</strong> ${escapeHtml(answer.question)}
                            </div>
                            <div class="question-type-badge">${typeLabel}</div>
                        </div>
                        
                        ${optionsHtml}
                        
                        <div class="question-footer">
                            <div class="question-stats">
                                <strong>Points:</strong> 
                                <span class="points-display">${answer.points_earned} / ${answer.points_possible}</span>
                            </div>
                            <button onclick="openOverrideModal(${student.attempt_id}, ${answer.answer_id}, ${answer.is_correct}, ${answer.points_earned}, ${answer.points_possible})" 
                                    class="btn btn-sm" 
                                    style="background: #6ba5b3; color: white; border: none; padding: 6px 16px; border-radius: 6px; cursor: pointer; font-size: 13px;">
                                <i class="bi bi-pencil-square"></i> Override
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = questionsHtml;
        }
        
        // ==================== HELPER FUNCTIONS ====================
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
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // ==================== OVERRIDE FUNCTIONS ====================
        let currentOverrideAttemptId = null;
        
        function openOverrideModal(attemptId, answerId, isCorrect, pointsEarned, maxPoints) {
            currentOverrideAttemptId = attemptId;
            document.getElementById('override_attempt_id').value = attemptId;
            document.getElementById('override_answer_id').value = answerId;
            document.getElementById('points_earned').value = pointsEarned;
            document.getElementById('max_points').textContent = maxPoints;
            document.getElementById('points_earned').max = maxPoints;
            
            // Set correct/incorrect radio
            if (isCorrect) {
                document.getElementById('markCorrect').checked = true;
            } else {
                document.getElementById('markIncorrect').checked = true;
            }
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('overrideModal'));
            modal.show();
        }
        
        function submitOverride() {
            const answerId = document.getElementById('override_answer_id').value;
            const isCorrect = document.querySelector('input[name="is_correct"]:checked').value;
            const pointsEarned = parseFloat(document.getElementById('points_earned').value);
            const maxPoints = parseFloat(document.getElementById('max_points').textContent);
            
            if (pointsEarned > maxPoints) {
                alert('Points earned cannot exceed maximum points!');
                return;
            }
            
            if (pointsEarned < 0) {
                alert('Points earned cannot be negative!');
                return;
            }
            
            // Send update request
            fetch(`/instructor/exams-statistics/${examId}/answer/${answerId}/override`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    is_correct: isCorrect === '1',
                    points_earned: pointsEarned
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('overrideModal')).hide();
                    
                    // Update the question card
                    const questionCard = document.querySelector(`.question-analysis-card[data-answer-id="${answerId}"]`);
                    if (questionCard) {
                        const pointsDisplay = questionCard.querySelector('.points-display');
                        if (pointsDisplay) {
                            pointsDisplay.textContent = `${pointsEarned} / ${maxPoints}`;
                        }
                    }
                    
                    // Update student data in memory
                    const studentIndex = selectedStudentIndex;
                    studentsData[studentIndex].score = data.new_score;
                    
                    // Update navigation to reflect new score
                    updateStudentNavigation();
                    
                    // Show success message
                    alert('Answer updated successfully!');
                } else {
                    alert('Failed to update answer. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error updating answer:', error);
                alert('An error occurred while updating the answer.');
            });
        }
        
        // Add CSS for students navigation grid
        const style = document.createElement('style');
        style.textContent = `
            .students-nav-container {
                background: white;
                padding: 25px;
                border-radius: 12px;
                margin-bottom: 20px;
                border: 1px solid #e5e7eb;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }
            
            .student-nav-controls {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
            }
            
            .student-nav-btn {
                background: linear-gradient(135deg, #6ba5b3 0%, #5a8f9c 100%);
                color: white;
                border: none;
                border-radius: 50%;
                width: 45px;
                height: 45px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 20px;
                transition: all 0.3s ease;
                flex-shrink: 0;
            }
            
            .student-nav-btn:hover:not(:disabled) {
                background: linear-gradient(135deg, #5a8f9c 0%, #4a7f8c 100%);
                transform: scale(1.1);
                box-shadow: 0 4px 8px rgba(107, 165, 179, 0.3);
            }
            
            .student-nav-btn:disabled {
                background: #d1d5db;
                cursor: not-allowed;
                opacity: 0.5;
            }
            
            .student-nav-info {
                flex: 1;
                padding: 0 20px;
            }
            
            @media (max-width: 768px) {
                .student-nav-controls {
                    flex-direction: column;
                    gap: 15px;
                }
                
                .student-nav-btn {
                    width: 40px;
                    height: 40px;
                    font-size: 18px;
                }
                
                .student-nav-info {
                    padding: 0;
                    width: 100%;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
@endsection