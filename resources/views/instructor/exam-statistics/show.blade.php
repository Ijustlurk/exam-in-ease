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
                    <div class="chart-placeholder">
                        Chart will be rendered here (use Chart.js or similar library)
                    </div>
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

            <!-- Questions Tab Content (Hidden by default) -->
            <div class="tab-content-panel" id="questionsContent">
                <h2>Questions Analysis</h2>
                <p style="color: #6c757d;">Detailed question-by-question analysis will be displayed here.</p>
            </div>

            <!-- Individual Tab Content (Hidden by default) -->
            <div class="tab-content-panel" id="individualContent">
                <h2>Individual Student Performance</h2>
                <p style="color: #6c757d;">Individual student performance data will be displayed here.</p>
            </div>
        </div>
    </div>

    <script>
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
    </script>
@endsection