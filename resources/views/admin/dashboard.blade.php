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
                        /** @var object $exam */
                        $examTitle = property_exists($exam, 'exam_title') ? : 'N/A';
                        $examId = property_exists($exam, 'exam_id') ? $exam->exam_id : 0;
                        
                        // Get collaborator name
                        $collaboratorName = 'N/A';
                        if (property_exists($exam, 'user') && $exam->user) {
                            if (property_exists($exam->user, 'teacher') && $exam->user->teacher) {
                                $firstName = property_exists($exam->user->teacher, 'first_name') ? $exam->user->teacher->first_name : '';
                                $lastName = property_exists($exam->user->teacher, 'last_name') ? $exam->user->teacher->last_name : '';
                                $collaboratorName = trim($firstName . ' ' . $lastName);
                            }
                        }
                        
                        // Get subject name
                        $subjectName = 'N/A';
                        if (property_exists($exam, 'subject') && $exam->subject) {
                            $subjectName = property_exists($exam->subject, 'subject_name') ? : 'N/A';
                        }
                        
                        // Get semester
                        $semester = property_exists($exam, 'semester') ?  : '1st';
                    @endphp
                    <div class="exam-card">
                        <div class="exam-card-content">
                            <div class="exam-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="exam-details">
                                <h5>{{ $exam->exam_title }}</h5>
                                <p><strong>Instructor:</strong> {{ $exam->user ? $exam->user->first_name . ' ' . $exam->user->last_name : 'N/A' }}</p>
                                <p><strong>Subject:</strong> {{ $exam->subject ? $exam->subject->subject_name : 'N/A' }}</p>
                                <p><strong>Department:</strong> Computer Science</p>
                                <p><strong>Classes Undertaking:</strong> 1A Comp Prog, 1B Comp Prog, and others</p>
                                <p><strong>Semester:</strong> {{ $exam->term ?? 'N/A' }}</p>
                                <p><strong>Status:</strong> {{ ucfirst($exam->status) }}</p>
                            </div>
                        </div>
                        <button class="monitor-btn" onclick="window.location.href='{{ route('admin.exams.show', $exam->exam_id) }}'">
                            <i class="bi bi-eye"></i>
                            View
                        </button>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No ongoing exams found.</p>
                    </div>
                @endforelse
            </div>

            <!-- Spacing for bottom -->
            <div style="height: 60px;"></div>
        </div>

    @endcan
@endsection