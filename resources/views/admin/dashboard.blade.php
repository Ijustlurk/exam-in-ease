@extends('layouts.Admin.app')

@section('content')

    @can('admin-access')
        <style>
            body { 
                margin: 0; 
                padding: 0; 
                font-family: 'Segoe UI', sans-serif; 
                background-color: #f8f9fa; 
            }
            
           

            /* Dashboard Header */
            .dashboard-header {
                background-color: #5a7a8f;
                color: white;
                padding: 1.5rem 2rem;
                border-radius: 8px;
                margin-bottom: 2rem;
            }

            .dashboard-header h4 {
                margin: 0;
                font-weight: 600;
                font-size: 1.5rem;
            }

            /* Dashboard Cards - Matching Image Design */
            .dashboard-cards {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 1.5rem;
                margin-bottom: 2rem;
            }

            .dashboard-cards .stat-card {
                background: white;
                border-radius: 12px;
                padding: 2rem 1.5rem;
                text-align: center;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                border: 1px solid #e9ecef;
            }

            .dashboard-cards .stat-card h2 {
                font-size: 2.5rem;
                font-weight: 700;
                margin: 0 0 0.5rem 0;
                color: #2c3e50;
            }

            .dashboard-cards .stat-card p {
                margin: 0;
                font-size: 1rem;
                font-weight: 600;
                color: #6c757d;
            }

            /* Search Bar */
            .search-container {
                margin-bottom: 2rem;
            }

            .search-bar {
                position: relative;
                max-width: 600px;
            }

            .search-bar input {
                width: 100%;
                padding: 0.75rem 3rem 0.75rem 1rem;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                font-size: 0.95rem;
            }

            .search-bar input:focus {
                outline: none;
                border-color: #5a7a8f;
                box-shadow: 0 0 0 0.2rem rgba(90, 122, 143, 0.15);
            }

            .search-bar .search-icon {
                position: absolute;
                right: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: #6c757d;
                font-size: 1.2rem;
            }

            /* Ongoing Exams Section */
            .section-header {
                color: #5a7a8f;
                font-size: 1.1rem;
                font-weight: 600;
                margin-bottom: 1.5rem;
            }

            .exam-list {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .exam-card {
                background: white;
                border-radius: 12px;
                padding: 1.5rem;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                border: 1px solid #e9ecef;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .exam-card-content {
                display: flex;
                align-items: center;
                gap: 1.5rem;
                flex: 1;
            }

            .exam-icon {
                width: 60px;
                height: 60px;
                background-color: #f8f9fa;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .exam-icon i {
                font-size: 2rem;
                color: #6c757d;
            }

            .exam-details h5 {
                margin: 0 0 0.5rem 0;
                font-size: 1.1rem;
                font-weight: 600;
                color: #2c3e50;
            }

            .exam-details p {
                margin: 0.15rem 0;
                font-size: 0.85rem;
                color: #6c757d;
                line-height: 1.4;
            }

            .exam-details p strong {
                color: #495057;
            }
            
            .status-badge {
                display: inline-block;
                padding: 0.25rem 0.75rem;
                border-radius: 12px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                margin-left: 0.5rem;
            }
            
            .status-draft {
                background-color: #e9ecef;
                color: #495057;
            }
            
            .status-for-approval {
                background-color: #fff3cd;
                color: #856404;
            }
            
            .status-approved {
                background-color: #d1ecf1;
                color: #0c5460;
            }
            
            .status-ongoing {
                background-color: #d4edda;
                color: #155724;
            }
            
            .status-archived {
                background-color: #f8d7da;
                color: #721c24;
            }

            .monitor-btn {
                background-color: #28a745;
                color: white;
                border: none;
                padding: 0.6rem 1.5rem;
                border-radius: 8px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            .monitor-btn:hover {
                background-color: #218838;
            }

            .monitor-btn i {
                font-size: 1rem;
            }

            /* Empty State */
            .empty-state {
                text-align: center;
                padding: 3rem 2rem;
                background: white;
                border-radius: 12px;
                border: 1px solid #e9ecef;
            }

            .empty-state i {
                font-size: 3rem;
                color: #dee2e6;
                margin-bottom: 1rem;
            }

            .empty-state p {
                color: #6c757d;
                margin: 0;
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .dashboard-cards {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 768px) {
                .dashboard-cards {
                    grid-template-columns: 1fr;
                }

                .exam-card {
                    flex-direction: column;
                    gap: 1rem;
                }

                .exam-card-content {
                    flex-direction: column;
                    text-align: center;
                }

                .monitor-btn {
                    width: 100%;
                    justify-content: center;
                }
            }
        </style>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div id="mainContent" class="main">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <h4>Dashboard</h4>
            </div>
            
            <!-- Dashboard Statistics Cards -->
            <div class="dashboard-cards">
                <div class="stat-card">
                    <h2>{{ number_format($totalExams ?? 0) }}</h2>
                    <p>Exams</p>
                </div>
                <div class="stat-card">
                    <h2>{{ number_format($totalStudents ?? 0) }}</h2>
                    <p>Students</p>
                </div>
                <div class="stat-card">
                    <h2>{{ number_format($totalSubjects ?? 0) }}</h2>
                    <p>Subjects</p>
                </div>
                <div class="stat-card">
                    <h2>{{ number_format($totalActiveUsers ?? 0) }}</h2>
                    <p>Active Users</p>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="search-container">
                <div class="search-bar">
                    <input type="text" class="form-control" placeholder="Search for exams">
                    <i class="bi bi-search search-icon"></i>
                </div>
            </div>

            <!--Exams Section -->
            <h5 class="section-header">Exams</h5>

            <div class="exam-list">
                @forelse ($recentExams ?? [] as $exam)
                    @php
                        // Get teacher name
                        $teacherName = 'N/A';
                        if ($exam->user) {
                            $teacherName = $exam->user->name ?? 'N/A';
                        }
                        
                        // Get subject name
                        $subjectName = $exam->subject->subject_name ?? 'N/A';
                        
                        // Get assigned classes
                        $classesList = 'No classes assigned';
                        if (isset($exam->examAssignments) && $exam->examAssignments->count() > 0) {
                            $classes = $exam->examAssignments
                                ->filter(function($assignment) {
                                    return $assignment->class !== null;
                                })
                                ->map(function($assignment) {
                                    $class = $assignment->class;
                                    return ($class->year_level ?? '') . '-' . $class->section . ' ' . $class->title;
                                })
                                ->unique()
                                ->values();
                            
                            if ($classes->count() > 0) {
                                $classesList = $classes->take(3)->implode(', ');
                                if ($classes->count() > 3) {
                                    $classesList .= ', and others';
                                }
                            }
                        }
                        
                        // Status badge
                        $statusMap = [
                            'draft' => 'Draft',
                            'for approval' => 'For Approval',
                            'approved' => 'Approved',
                            'ongoing' => 'Ongoing',
                            'archived' => 'Archived'
                        ];
                        $statusLabel = $statusMap[$exam->status] ?? ucfirst($exam->status);
                        $statusClass = 'status-' . str_replace(' ', '-', $exam->status);
                    @endphp
                    <div class="exam-card">
                        <div class="exam-card-content">
                            <div class="exam-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="exam-details">
                                <h5>
                                    {{ $exam->exam_title }}
                                    <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                </h5>
                                <p><strong>Instructor:</strong> {{ $teacherName }}</p>
                                <p><strong>Subject:</strong> {{ $subjectName }}</p>
                                <p><strong>Classes:</strong> {{ $classesList }}</p>
                                <p><strong>Semester:</strong> {{ $exam->semester ?? 'N/A' }}</p>
                                <p><strong>Created:</strong> {{ $exam->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <button class="monitor-btn" onclick="window.location.href='{{ route('admin.exam-statistics.show', $exam->exam_id) }}'">
                            <i class="bi bi-eye"></i>
                            View
                        </button>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No exams found.</p>
                    </div>
                @endforelse
            </div>

            <!-- Spacing for bottom -->
            <div style="height: 60px;"></div>
        </div>

    @endcan
@endsection