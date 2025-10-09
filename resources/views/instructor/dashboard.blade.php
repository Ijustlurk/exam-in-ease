{{-- dashboard.blade.php --}}
@extends('layouts.Instructor.app')

@section('content')
<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background-color: #e8eef2;
    }
    .exam-content {
        padding: 24px;
        background-color: #e8eef2;
        min-height: 100vh;
    }
    .search-bar {
        background-color: #fff;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 16px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    }
    .search-bar input {
        border: none;
        outline: none;
        width: 100%;
        margin-left: 8px;
        font-size: 14px;
        color: #212529;
    }
    .search-bar input::placeholder {
        color: #9ca3af;
    }
    .recents-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 16px;
        font-weight: 500;
    }
    .exam-card-wrapper {
        background-color: #fff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #e5e7eb;
        position: relative;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        height: 200px;
        display: flex;
        flex-direction: column;
    }
    .exam-card-wrapper:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
        border-color: #cbd5e1;
    }
    .exam-card-wrapper.active {
        border-color: #7dd3fc;
        box-shadow: 0 4px 12px rgba(125,211,252,0.3);
        background-color: #f0f9ff;
    }
    .exam-icon-wrapper {
        font-size: 3rem;
        color: #374151;
        margin-bottom: auto;
        flex: 1;
        display: flex;
        align-items: center;
    }
    .exam-footer {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: auto;
    }
    .exam-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .exam-info {
        flex: 1;
        min-width: 0;
    }
    .exam-title-text {
        font-size: 0.875rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .exam-date-text {
        font-size: 0.7rem;
        color: #9ca3af;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .menu-dots {
        position: absolute;
        top: 16px;
        right: 16px;
        cursor: pointer;
        padding: 6px;
        border-radius: 4px;
        color: #6b7280;
        transition: all 0.2s;
        z-index: 50;
        background: transparent;
    }
    .menu-dots:hover {
        background-color: #f3f4f6;
        color: #374151;
    }
    .menu-dropdown {
        position: absolute;
        top: 45px;
        right: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        min-width: 180px;
        display: none;
        z-index: 200;
        overflow: hidden;
    }
    .menu-dropdown.show {
        display: block;
    }
    .menu-item {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #374151;
        transition: background-color 0.15s;
        background: transparent;
        border: none;
        width: 100%;
        text-align: left;
    }
    .menu-item:hover {
        background-color: #f9fafb;
    }
    .menu-item i {
        color: #6b7280;
        font-size: 0.95rem;
    }
    .menu-item.text-danger:hover {
        background-color: #fef2f2;
    }
    .details-panel {
        background-color: #fff;
        border-radius: 12px;
        padding: 28px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        position: sticky;
        top: 90px;
    }
    .detail-header {
        text-align: center;
        padding-bottom: 20px;
        border-bottom: 1px solid #f1f3f5;
        margin-bottom: 20px;
    }
    .detail-icon {
        font-size: 4rem;
        color: #374151;
        margin-bottom: 12px;
    }
    .detail-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #212529;
    }
    .collaborator-section {
        padding: 16px 0;
        border-bottom: 1px solid #f1f3f5;
        margin-bottom: 20px;
    }
    .collaborator-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .collaborator-label {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 600;
    }
    .add-collab-btn {
        padding: 4px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background-color: white;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
        color: #374151;
    }
    .add-collab-btn:hover {
        background-color: #f9fafb;
        border-color: #9ca3af;
    }
    .collaborator-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .creator-avatar-large {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .creator-name {
        font-size: 0.875rem;
        color: #212529;
        font-weight: 500;
    }
    .detail-section-title {
        font-size: 0.75rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 16px;
        margin-top: 20px;
    }
    .detail-row {
        margin-bottom: 16px;
    }
    .detail-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px;
    }
    .detail-value {
        font-size: 0.875rem;
        color: #6b7280;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: capitalize;
    }
    .status-draft {
        background-color: #fef3c7;
        color: #92400e;
    }
    .status-approved {
        background-color: #d1fae5;
        color: #065f46;
    }
    .status-ongoing {
        background-color: #dbeafe;
        color: #1e40af;
    }
    .status-archived {
        background-color: #f3f4f6;
        color: #374151;
    }
    .add-button {
        position: fixed;
        bottom: 32px;
        right: 32px;
        width: 56px;
        height: 56px;
        background-color: #a5d8e8;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(165,216,232,0.4);
        transition: all 0.3s ease;
        z-index: 50;
    }
    .add-button:hover {
        background-color: #7ec8dd;
        transform: scale(1.08);
        box-shadow: 0 6px 16px rgba(165,216,232,0.5);
    }
    .no-exams {
        text-align: center;
        padding: 80px 20px;
        color: #9ca3af;
    }
    .no-exams i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 16px;
    }
    .empty-details {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-details i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 16px;
    }

    /* Add Collaborator Modal Styles */
    .collab-modal-header {
        background-color: #7ca5b8;
        color: white;
        padding: 16px 24px;
        border-radius: 12px 12px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .collab-modal-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
        font-weight: 600;
    }
    .collab-search-bar {
        background-color: #fff;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        margin-bottom: 16px;
    }
    .collab-search-bar input {
        border: none;
        outline: none;
        width: 100%;
        margin-left: 8px;
        font-size: 14px;
        color: #212529;
    }
    .collab-search-bar input::placeholder {
        color: #9ca3af;
    }
    .collab-search-results {
        max-height: 200px;
        overflow-y: auto;
        margin-bottom: 20px;
    }
    .collab-user-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s;
        margin-bottom: 8px;
    }
    .collab-user-item:hover {
        background-color: #f9fafb;
    }
    .collab-user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .collab-user-info {
        flex: 1;
    }
    .collab-user-name {
        font-size: 0.9rem;
        font-weight: 500;
        color: #212529;
        margin-bottom: 2px;
    }
    .collab-user-email {
        font-size: 0.8rem;
        color: #6b7280;
    }
    .selected-collabs-section {
        padding: 16px;
        background-color: #f9fafb;
        border-radius: 8px;
        margin-bottom: 20px;
        min-height: 80px;
    }
    .selected-collab-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 8px;
    }
    .selected-collab-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .selected-collab-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }
    .selected-collab-details {
        flex: 1;
    }
    .selected-collab-name {
        font-size: 0.875rem;
        font-weight: 500;
        color: #212529;
    }
    .selected-collab-email {
        font-size: 0.75rem;
        color: #6b7280;
    }
    .remove-collab-btn {
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        padding: 4px;
        font-size: 1.2rem;
        line-height: 1;
    }
    .remove-collab-btn:hover {
        color: #374151;
    }
    .add-collab-submit-btn {
        background-color: #7ca5b8;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 24px;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    .add-collab-submit-btn:hover {
        background-color: #6a94a6;
    }
    .add-collab-submit-btn:disabled {
        background-color: #d1d5db;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .exam-content {
            padding: 16px;
        }
        .details-panel {
            margin-top: 20px;
            position: relative;
            top: 0;
        }
        .add-button {
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
        }
    }
</style>

<div class="exam-content">
     <a href="{{ route ('instructor.exams.create')}}">
                            <button type="submit" class="btn" style="background-color: #5f8a9a; color: white; border-radius: 8px; padding: 10px 28px; font-size: 0.95rem; font-weight: 500; border: none;">
                            Create New Exam
                        </button>
                        </a>
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search Bar -->
        <form action="{{ route('instructor.exams.index') }}" method="GET" class="search-bar">
            <i class="bi bi-search" style="color: #9ca3af;"></i>
            <input type="text" name="search" placeholder="Search for exams" value="{{ request('search') }}">
        </form>

        <div class="row">
            <!-- Left Section - Exam Cards (3 columns max) -->
            <div class="col-lg-7 col-md-6 mb-4">
                <div class="recents-label">Recents</div>
                
                @if($exams->count() > 0)
                    <div class="row">
                        @foreach($exams as $exam)
                        <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                            <div class="exam-card-wrapper {{ $loop->first ? 'active' : '' }}" 
                                 data-exam-id="{{ $exam->exam_id }}" 
                                 onclick="loadExamDetails({{ $exam->exam_id }}, this)">
                                
                                <div class="menu-dots" onclick="event.stopPropagation(); toggleCardMenu(event, 'menu{{ $exam->exam_id }}')">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </div>

                                <div class="exam-icon-wrapper">
                                    <i class="bi bi-file-text"></i>
                                </div>
                                
                                <div class="exam-footer">
                                    @if($exam->user && $exam->user->profile_picture)
                                        <img src="{{ asset('storage/' . $exam->user->profile_picture) }}" alt="Creator" class="exam-avatar">
                                    @else
                                        <div class="exam-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.7rem; font-weight: 600;">
                                            @if($exam->user)
                                                {{ strtoupper(substr($exam->user->name, 0, 1)) }}
                                            @else
                                                ?
                                            @endif
                                        </div>
                                    @endif
                                    <div class="exam-info">
                                        <div class="exam-title-text">
                                            {{ $exam->exam_title }}
                                        </div>
                                        <div class="exam-date-text">
                                            Last edited {{ $exam->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="menu-dropdown" id="menu{{ $exam->exam_id }}" onclick="event.stopPropagation();">
                                    <a href="{{ route('instructor.exams.show', $exam->exam_id) }}" target="_blank" class="menu-item text-decoration-none">
                                        <i class="bi bi-box-arrow-up-right"></i> Open in new tab
                                    </a>
                                    {{-- <a href="{{ route('instructor.exams.download', $exam->exam_id) }}" class="menu-item text-decoration-none">
                                        <i class="bi bi-download"></i> Download
                                    </a> --}}
                                    <div class="menu-item">
                                        <i class="bi bi-person-plus"></i> Add Collaborator
                                    </div>
                                    <div class="menu-item">
                                        <i class="bi bi-pencil"></i> Rename
                                    </div>
                                    <form action="{{ route('instructor.exams.duplicate', $exam->exam_id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="menu-item">
                                            <i class="bi bi-files"></i> Create a Copy
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-exams">
                        <i class="bi bi-folder2-open"></i>
                        <p class="mt-3">No exams found. Create your first exam!</p>
                    </div>
                @endif
            </div>

            <!-- Right Section - Details Panel -->
            <div class="col-lg-5 col-md-6" id="exam-details-panel">
                @if($selectedExam)
                    <div class="details-panel">
                        <div class="detail-header">
                            <div class="detail-icon">
                                <i class="bi bi-file-text"></i>
                            </div>
                            <div class="detail-title" id="detail-title">
                                {{ $selectedExam->exam_title }}
                            </div>
                        </div>

                        <div class="collaborator-section">
                            <div class="collaborator-header">
                                <div class="collaborator-label">Collaborator</div>
                                <button class="add-collab-btn" onclick="openAddCollaboratorModal()">+ Add a collaborator</button>
                            </div>
                            <div class="collaborator-info">
                                @if($selectedExam->user && $selectedExam->user->profile_picture)
                                    <img src="{{ asset('storage/' . $selectedExam->user->profile_picture) }}" alt="Creator" class="creator-avatar-large" id="detail-avatar-img">
                                @else
                                    <div class="creator-avatar-large" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;" id="detail-avatar">
                                        @if($selectedExam->user)
                                            {{ strtoupper(substr($selectedExam->user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $selectedExam->user->name)[1] ?? $selectedExam->user->name, 0, 1)) }}
                                        @else
                                            NA
                                        @endif
                                    </div>
                                @endif
                                <div class="creator-name" id="detail-creator">
                                    {{ $selectedExam->user->name ?? 'Unknown' }}
                                </div>
                            </div>
                        </div>

                        <div class="detail-section-title">Exam Details</div>

                        <div class="detail-row">
                            <div class="detail-label">Subject</div>
                            <div class="detail-value" id="detail-subject">
                                {{ $selectedExam->subject->subject_name ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Date Created</div>
                            <div class="detail-value" id="detail-created">
                                {{ $selectedExam->formatted_created_at }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Last Modified</div>
                            <div class="detail-value" id="detail-modified">
                                {{ $selectedExam->updated_at->format('F j, Y') }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <span class="status-badge status-{{ $selectedExam->status }}" id="detail-status">
                                    {{ ucfirst($selectedExam->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Revision Notes</div>
                            <div class="detail-value" id="detail-notes">
                                {{ $selectedExam->exam_desc ?? 'None' }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="details-panel">
                        <div class="empty-details">
                            <i class="bi bi-file-text"></i>
                            <p class="text-muted">Select an exam to view details</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Button -->
<div class="add-button" onclick="openNewExamModal()">
    <i class="bi bi-plus"></i>
</div>

<!-- New Exam Modal -->
<div class="modal fade" id="newExamModal" tabindex="-1" aria-labelledby="newExamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background-color: #5f8a9a; color: white; border-radius: 12px 12px 0 0; padding: 16px 24px;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="newExamModalLabel">
                    <i class="bi bi-pencil-square"></i>
                    <span>New Exam</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form action="{{ route('instructor.exams.store') }}" method="POST" id="newExamForm">
                    @csrf
                    
                    <!-- Exam Title -->
                    <div class="mb-3">
                        <input type="text" class="form-control" name="exam_title" placeholder="Computer Programming 1 Prelim" required style="border-radius: 8px; padding: 12px 16px; border: 1px solid #d1d5db; font-size: 0.95rem;">
                    </div>

                    <!-- Exam Description -->
                    <div class="mb-3">
                        <input type="text" class="form-control" name="exam_desc" placeholder="Exam on programming" style="border-radius: 8px; padding: 12px 16px; border: 1px solid #d1d5db; font-size: 0.95rem;">
                    </div>

                    <!-- Settings Section -->
                    <div class="mb-3">
                        <label style="font-size: 0.75rem; color: #6b7280; font-weight: 600; margin-bottom: 12px; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; display: block;">Settings</label>
                        
                        <div class="row">
                            <!-- Subject -->
                            <div class="col-md-6 mb-3">
                                <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Subject</label>
                                <select class="form-select" name="subject_id" required style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                                    <option value="">Computer...</option>
                                    @if(isset($subjects))
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- Class Assignment -->
                            <div class="col-md-6 mb-3">
                                <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Class Assignment</label>
                                <select class="form-select" name="class_assignment" required style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                                    <option value="">1A, 1B, 1C, 1G, 1F</option>
                                    <option value="1A">1A</option>
                                    <option value="1B">1B</option>
                                    <option value="1C">1C</option>
                                    <option value="1G">1G</option>
                                    <option value="1F">1F</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Term -->
                            <div class="col-md-6 mb-3">
                                <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Term</label>
                                <select class="form-select" name="term" required style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                                    <option value="">Preliminaries</option>
                                    <option value="Preliminaries">Preliminaries</option>
                                    <option value="Midterm">Midterm</option>
                                    <option value="Finals">Finals</option>
                                </select>
                            </div>

                            <!-- Duration -->
                            <div class="col-md-6 mb-3">
                                <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Duration</label>
                                <div class="input-group" style="border-radius: 8px; overflow: hidden;">
                                    <input type="number" class="form-control" name="duration" value="0" min="0" required style="border: 1px solid #d1d5db; font-size: 0.875rem; padding: 10px 14px;">
                                    <span class="input-group-text" style="background-color: #f9fafb; border: 1px solid #d1d5db; border-left: none; color: #6b7280; font-size: 0.875rem;">mins</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Schedule Start -->
                            <div class="col-md-6 mb-3">
                                <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Schedule Start</label>
                                <input type="datetime-local" class="form-control" name="schedule_start" required style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                            </div>

                            <!-- Schedule End -->
                            <div class="col-md-6 mb-3">
                                <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Schedule End</label>
                                <input type="datetime-local" class="form-control" name="schedule_end" required style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Create Button -->
                    <div class="d-flex justify-content-end">
                        <a href="{{ route ('instructor.exams.create')}}">
                            <button type="submit" class="btn" style="background-color: #5f8a9a; color: white; border-radius: 8px; padding: 10px 28px; font-size: 0.95rem; font-weight: 500; border: none;">
                            Create New Exam
                        </button>
                        </a>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Collaborator Modal -->
<div class="modal fade" id="addCollaboratorModal" tabindex="-1" aria-labelledby="addCollaboratorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="collab-modal-header">
                <div class="collab-modal-title">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Add a Collaborator</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <!-- Search Bar -->
                <div class="collab-search-bar">
                    <i class="bi bi-search" style="color: #9ca3af;"></i>
                    <input type="text" id="collabSearchInput" placeholder="Search for users" oninput="searchCollaborators()">
                </div>

                <!-- Search Results -->
                <div class="collab-search-results" id="collabSearchResults" style="display: none;">
                    <!-- Results will be populated by JavaScript -->
                </div>

                <!-- Selected Collaborators -->
                <div class="selected-collabs-section" id="selectedCollabsSection">
                    <div id="selectedCollabsList">
                        <!-- Selected collaborators will appear here -->
                    </div>
                </div>

                <!-- Add Button -->
                <div class="d-flex justify-content-end">
                    <button type="button" class="add-collab-submit-btn" id="addCollabSubmitBtn" disabled onclick="submitCollaborators()">
                        Add as Collaborator
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Sample teachers data (replace with actual API call)
    const teachers = [
        { id: 1, name: 'Teacher Name 2', email: 'something@mail.com', avatar: null },
        { id: 2, name: 'Teacher Name 3', email: 'something@mail.com', avatar: null },
        { id: 3, name: 'Teacher Name 4', email: 'teacher4@mail.com', avatar: null },
        { id: 4, name: 'Instructor Name 1', email: 'instructor1@mail.com', avatar: null }
    ];

    let selectedCollaborators = [];

    function openAddCollaboratorModal() {
        selectedCollaborators = [];
        document.getElementById('selectedCollabsList').innerHTML = '';
        document.getElementById('collabSearchInput').value = '';
        document.getElementById('collabSearchResults').style.display = 'none';
        document.getElementById('addCollabSubmitBtn').disabled = true;
        
        const modal = new bootstrap.Modal(document.getElementById('addCollaboratorModal'));
        modal.show();
    }

    function searchCollaborators() {
        const searchTerm = document.getElementById('collabSearchInput').value.toLowerCase();
        const resultsContainer = document.getElementById('collabSearchResults');
        
        if (searchTerm.length === 0) {
            resultsContainer.style.display = 'none';
            return;
        }

        const filteredTeachers = teachers.filter(teacher => 
            teacher.name.toLowerCase().includes(searchTerm) || 
            teacher.email.toLowerCase().includes(searchTerm)
        );

        if (filteredTeachers.length > 0) {
            resultsContainer.style.display = 'block';
            resultsContainer.innerHTML = filteredTeachers.map(teacher => `
                <div class="collab-user-item" onclick="selectCollaborator(${teacher.id})">
                    ${teacher.avatar ? 
                        `<img src="${teacher.avatar}" alt="${teacher.name}" class="collab-user-avatar">` :
                        `<div class="collab-user-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                            ${teacher.name.charAt(0).toUpperCase()}
                        </div>`
                    }
                    <div class="collab-user-info">
                        <div class="collab-user-name">${teacher.name}</div>
                    </div>
                </div>
            `).join('');
        } else {
            resultsContainer.style.display = 'block';
            resultsContainer.innerHTML = '<p class="text-muted text-center p-3">No users found</p>';
        }
    }

    function selectCollaborator(teacherId) {
        const teacher = teachers.find(t => t.id === teacherId);
        
        if (teacher && !selectedCollaborators.find(c => c.id === teacherId)) {
            selectedCollaborators.push(teacher);
            renderSelectedCollaborators();
            document.getElementById('addCollabSubmitBtn').disabled = false;
            
            // Clear search
            document.getElementById('collabSearchInput').value = '';
            document.getElementById('collabSearchResults').style.display = 'none';
        }
    }

    function removeCollaborator(teacherId) {
        selectedCollaborators = selectedCollaborators.filter(c => c.id !== teacherId);
        renderSelectedCollaborators();
        
        if (selectedCollaborators.length === 0) {
            document.getElementById('addCollabSubmitBtn').disabled = true;
        }
    }

    function renderSelectedCollaborators() {
        const container = document.getElementById('selectedCollabsList');
        
        if (selectedCollaborators.length === 0) {
            container.innerHTML = '';
            return;
        }

        container.innerHTML = selectedCollaborators.map(collab => `
            <div class="selected-collab-item">
                <div class="selected-collab-info">
                    ${collab.avatar ? 
                        `<img src="${collab.avatar}" alt="${collab.name}" class="selected-collab-avatar">` :
                        `<div class="selected-collab-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                            ${collab.name.charAt(0).toUpperCase()}
                        </div>`
                    }
                    <div class="selected-collab-details">
                        <div class="selected-collab-name">${collab.name}</div>
                        <div class="selected-collab-email">${collab.email}</div>
                    </div>
                </div>
                <button class="remove-collab-btn" onclick="removeCollaborator(${collab.id})">
                    ×
                </button>
            </div>
        `).join('');
    }

    function submitCollaborators() {
        if (selectedCollaborators.length === 0) return;
        
        // Here you would send the selected collaborators to your backend
        console.log('Adding collaborators:', selectedCollaborators);
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('addCollaboratorModal'));
        modal.hide();
        
        // Show success message
        alert(`Added ${selectedCollaborators.length} collaborator(s) successfully!`);
        
        // Reset
        selectedCollaborators = [];
    }

    function openNewExamModal() {
        const modal = new bootstrap.Modal(document.getElementById('newExamModal'));
        modal.show();
    }

    function toggleCardMenu(event, menuId) {
        event.stopPropagation();
        event.preventDefault();
        
        const menu = document.getElementById(menuId);
        
        // Close all other menus
        document.querySelectorAll('.menu-dropdown').forEach(m => {
            if (m.id !== menuId) {
                m.classList.remove('show');
            }
        });
        
        // Toggle current menu
        menu.classList.toggle('show');
    }

    function loadExamDetails(examId, cardElement) {
        // Remove active class from all cards
        document.querySelectorAll('.exam-card-wrapper').forEach(card => {
            card.classList.remove('active');
        });
        
        // Add active class to clicked card
        cardElement.classList.add('active');

        // Fetch exam details via AJAX
        fetch(`/exams/${examId}`)
            .then(response => response.json())
            .then(data => {
                const exam = data.exam;
                
                // Update details panel
                document.getElementById('detail-title').textContent = exam.exam_title;
                document.getElementById('detail-avatar').textContent = data.creator_initials;
                document.getElementById('detail-creator').textContent = data.creator_name;
                document.getElementById('detail-subject').textContent = data.subject_name;
                document.getElementById('detail-created').textContent = data.formatted_created_at;
                
                // Handle updated_at safely
                const modifiedElement = document.getElementById('detail-modified');
                if (modifiedElement) {
                    modifiedElement.textContent = data.formatted_updated_at || data.formatted_created_at || 'N/A';
                }
                
                const statusBadge = document.getElementById('detail-status');
                statusBadge.textContent = exam.status.charAt(0).toUpperCase() + exam.status.slice(1);
                statusBadge.className = 'status-badge status-' + exam.status;
                
                if (document.getElementById('detail-notes')) {
                    document.getElementById('detail-notes').textContent = exam.exam_desc || 'None';
                }
            })
            .catch(error => {
                console.error('Error loading exam details:', error);
                alert('Failed to load exam details');
            });
    }

    // Close menus when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.menu-dots') && !event.target.closest('.menu-dropdown')) {
            document.querySelectorAll('.menu-dropdown').forEach(m => {
                m.classList.remove('show');
            });
        }
    });

    // Prevent menu from closing when clicking inside it
    document.querySelectorAll('.menu-dropdown').forEach(dropdown => {
        dropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });
</script>
@endpush