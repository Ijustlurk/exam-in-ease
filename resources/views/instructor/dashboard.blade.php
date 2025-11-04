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
        min-height: 100vh;
        margin-left: 60px;
        transition: margin-left 0.3s;
    }
    
    .exam-content.expanded {
        margin-left: 220px;
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
        user-select: none;
    }
    .exam-card-wrapper:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
        border-color: #cbd5e1;
        z-index: 1;
    }
    .exam-card-wrapper:hover::after {
        content: 'Double-click to open editor';
        position: absolute;
        bottom: 8px;
        right: 12px;
        font-size: 0.65rem;
        color: #9ca3af;
        font-style: italic;
        opacity: 0.8;
    }
    .exam-card-wrapper.active {
        border-color: #7dd3fc;
        box-shadow: 0 4px 12px rgba(125,211,252,0.3);
        background-color: #f0f9ff;
        z-index: 1;
    }
    .exam-card-wrapper.menu-open {
        z-index: 10000;
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
    .ownership-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 5;
    }
    .badge-owner {
        background-color: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
    }
    .badge-collaborator {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
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
        z-index: 9999;
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
        padding: 6px 16px;
        border: 1px solid #212529;
        border-radius: 20px;
        background-color: white;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
        color: #212529;
        font-weight: 500;
    }
    .add-collab-btn:hover {
        background-color: #212529;
        color: white;
    }
    .collaborator-display {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .collaborator-avatars {
        display: flex;
        align-items: center;
        gap: 0;
    }
    .collab-avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        margin-left: -10px;
        background: #374151;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        position: relative;
    }
    .collab-avatar-circle:first-child {
        margin-left: 0;
    }
    .collab-avatar-circle i {
        font-size: 1.2rem;
    }
    .collaborator-text {
        font-size: 0.875rem;
        color: #212529;
        font-weight: 400;
    }
    .collaborator-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 12px;
    }
    .collaborator-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px;
        background-color: #f9fafb;
        border-radius: 6px;
    }
    .collaborator-item-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #374151;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.75rem;
        flex-shrink: 0;
    }
    .collaborator-item-info {
        flex: 1;
        min-width: 0;
    }
    .collaborator-item-name {
        font-size: 0.8rem;
        color: #212529;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .collaborator-item-role {
        font-size: 0.7rem;
        color: #6b7280;
    }
    .role-badge-owner {
        background-color: #dbeafe;
        color: #1e40af;
        padding: 2px 8px;
        border-radius: 3px;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .detail-section-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 16px;
        margin-top: 20px;
    }
    .detail-row {
        margin-bottom: 16px;
    }
    .detail-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #212529;
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
    .status-for-approval {
        background-color: #fef08a;
        color: #854d0e;
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
        width: 80px;
        height: 80px;
        background-color: #2c748aff;
        border-radius: 80%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(14, 64, 80, 0.4);
        transition: all 0.3s ease;
        z-index: 50;
    }
    .add-button:hover {
        background-color: #7ec8dd;
        transform: scale(1.08);
        box-shadow: 0 6px 16px rgba(14, 64, 80, 0.8);
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

    /* Collaborator Modal Styles */
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

    /* Class Dropdown Styles */
    .class-dropdown {
        position: relative;
    }

    .class-dropdown-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        max-height: 300px;
        margin-top: 4px;
    }

    .class-search {
        padding: 8px;
        border-bottom: 1px solid #e5e7eb;
    }

    .class-search input {
        width: 100%;
        padding: 8px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 0.875rem;
    }

    .class-list {
        max-height: 250px;
        overflow-y: auto;
        padding: 8px;
    }

    .class-item {
        display: flex;
        align-items: center;
        padding: 8px;
        cursor: pointer;
        border-radius: 4px;
    }

    .class-item:hover {
        background-color: #f3f4f6;
    }

    .class-item input[type="checkbox"] {
        margin-right: 8px;
    }

    .selected-classes-counter {
        display: inline-block;
        background: #e5e7eb;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
        margin-left: 6px;
    }

    /* Manage Collaborators Styles */
    .existing-collab-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    .existing-collab-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }
    .existing-collab-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .existing-collab-details {
        flex: 1;
    }
    .existing-collab-name {
        font-size: 0.9rem;
        font-weight: 500;
        color: #212529;
        margin-bottom: 2px;
    }
    .existing-collab-email {
        font-size: 0.8rem;
        color: #6b7280;
    }
    .existing-collab-role {
        font-size: 0.75rem;
        padding: 3px 8px;
        border-radius: 4px;
        background-color: #e0f2fe;
        color: #0369a1;
        margin-right: 8px;
    }
    .existing-collab-role.owner {
        background-color: #fef3c7;
        color: #92400e;
    }
    .remove-existing-collab-btn {
        background: none;
        border: 1px solid #dc3545;
        color: #dc3545;
        cursor: pointer;
        padding: 6px 12px;
        font-size: 0.8rem;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .remove-existing-collab-btn:hover {
        background-color: #dc3545;
        color: white;
    }
    .remove-existing-collab-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .manage-collabs-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
        border-bottom: 2px solid #e5e7eb;
    }
    .manage-tab {
        padding: 10px 20px;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 500;
        color: #6b7280;
        transition: all 0.2s;
    }
    .manage-tab.active {
        color: #7ca5b8;
        border-bottom-color: #7ca5b8;
    }
    .manage-tab:hover {
        color: #5f8a9a;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }

    @media (max-width: 768px) {
        .exam-content {
            padding: 16px;
            margin-left: 0;
        }
        .exam-content.expanded {
            margin-left: 0;
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
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search Bar -->
        <div class="search-bar">
            <i class="bi bi-search" style="color: #9ca3af;"></i>
            <input type="text" id="examSearchInput" placeholder="Search for exams" value="{{ request('search') }}">
        </div>
         <!-- Main Content Row -->
        <div class="row">
            <!-- Left Section - Exam Cards -->
            <div class="col-lg-8 col-md-7 mb-4">
                <div class="recents-label">Recents</div>
                
                @if($exams->count() > 0)
                    <div class="row">
                        @foreach($exams as $exam)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="exam-card-wrapper {{ $loop->first ? 'active' : '' }}" 
                                 data-exam-id="{{ $exam->exam_id }}" 
                                 onclick="handleCardClick({{ $exam->exam_id }}, this)"
                                 ondblclick="openExamEditor({{ $exam->exam_id }})">
                                
                                <div class="menu-dots" onclick="event.stopPropagation(); toggleCardMenu(event, 'menu{{ $exam->exam_id }}')">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </div>

                                <div class="exam-icon-wrapper">
                                    <i class="bi bi-file-text"></i>
                                </div>
                                
                                <!-- Ownership Badge -->
                                @if($exam->is_owner)
                                    <span class="ownership-badge badge-owner">Owner</span>
                                @elseif($exam->is_collaborator)
                                    <span class="ownership-badge badge-collaborator">Collaborator</span>
                                @endif
                                
                                <div class="exam-footer">
                                    <div class="exam-info">
                                        <div class="exam-title-text">
                                            {{ $exam->exam_title }}
                                        </div>
                                        <div class="exam-date-text">
                                            Last opened {{ $exam->updated_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Menu Dropdown -->
                                <div class="menu-dropdown" id="menu{{ $exam->exam_id }}" onclick="event.stopPropagation();">
                                    <a href="{{ route('instructor.exams.create', $exam->exam_id) }}" target="_blank" class="menu-item text-decoration-none" onclick="closeAllMenus()">
                                        <i class="bi bi-box-arrow-up-right"></i> Open in new tab
                                    </a>
                                    <div class="menu-item" onclick="downloadExam({{ $exam->exam_id }}); closeAllMenus();">
                                        <i class="bi bi-download"></i> Download
                                    </div>
                                    @if($exam->status === 'draft')
                                    <div class="menu-item" onclick="openAddCollaboratorModal({{ $exam->exam_id }}); closeAllMenus();">
                                        <i class="bi bi-person-plus"></i> Add Collaborator
                                    </div>
                                    @endif
                                    @if($exam->status !== 'for approval' && $exam->status !== 'approved')
                                    <div class="menu-item" onclick="editExam({{ $exam->exam_id }}); closeAllMenus();">
                                        <i class="bi bi-pencil"></i> Edit Exam Details
                                    </div>
                                    @endif
                                    @if($exam->status === 'draft')
                                    <form action="{{ route('instructor.exams.duplicate', $exam->exam_id) }}" method="POST" class="m-0" onsubmit="closeAllMenus()">
                                        @csrf
                                        <button type="submit" class="menu-item">
                                            <i class="bi bi-files"></i> Create a Copy
                                        </button>
                                    </form>
                                    @endif
                                    @if($exam->status === 'draft')
                                    <div class="menu-item text-danger" onclick="deleteExam({{ $exam->exam_id }}, '{{ $exam->exam_title }}'); closeAllMenus();" 
                                         style="border-top: 1px solid #e5e7eb;">
                                        <i class="bi bi-trash"></i> Delete
                                    </div>
                                    @endif
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
            <div class="col-lg-4 col-md-5" id="exam-details-panel" >
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
                                <div class="collaborator-label">People</div>
                                @if($selectedExam->status === 'draft')
                                <button class="add-collab-btn" onclick="openAddCollaboratorModal({{ $selectedExam->exam_id }})">
                                    <i class="bi bi-people"></i> Manage Collaborators
                                </button>
                                @endif
                            </div>
                            
                            <div class="collaborator-list">
                                {{-- Owner --}}
                                <div class="collaborator-item">
                                    <div class="collaborator-item-avatar">
                                        {{ strtoupper(substr($selectedExam->user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($selectedExam->user->last_name ?? 'N', 0, 1)) }}
                                    </div>
                                    <div class="collaborator-item-info">
                                        <div class="collaborator-item-name">
                                            {{ $selectedExam->user->first_name ?? 'Unknown' }} {{ $selectedExam->user->last_name ?? 'User' }}
                                        </div>
                                        <div class="collaborator-item-role">
                                            <span class="role-badge-owner">Owner</span>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Collaborators --}}
                                @foreach($selectedExam->collaborations->filter(fn($collab) => $collab->teacher_id !== $selectedExam->teacher_id) as $collaboration)
                                    <div class="collaborator-item">
                                        <div class="collaborator-item-avatar">
                                            {{ strtoupper(substr($collaboration->teacher->first_name ?? 'C', 0, 1)) }}{{ strtoupper(substr($collaboration->teacher->last_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="collaborator-item-info">
                                            <div class="collaborator-item-name">
                                                {{ $collaboration->teacher->first_name ?? 'Unknown' }} {{ $collaboration->teacher->last_name ?? 'User' }}
                                            </div>
                                            <div class="collaborator-item-role">
                                                Collaborator
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
                                <span class="status-badge status-{{ str_replace(' ', '-', $selectedExam->status) }}" id="detail-status">
                                    {{ ucwords($selectedExam->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Revision Notes</div>
                            <div class="detail-value" id="detail-notes">
                                {{ $selectedExam->revision_notes ?? 'N/A' }}
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
                    
                    <div class="mb-3">
                        <input type="text" class="form-control" name="exam_title" placeholder="Exam Title" required style="border-radius: 8px; padding: 12px 16px; border: 1px solid #d1d5db; font-size: 0.95rem;">
                    </div>

                    <div class="mb-3">
                        <input type="text" class="form-control" name="exam_desc" placeholder="Exam Description" style="border-radius: 8px; padding: 12px 16px; border: 1px solid #d1d5db; font-size: 0.95rem;">
                    </div>
                    <!--Select Subject Drop Down-->
                    <div class="mb-3">
                        <label style="font-size: 0.75rem; color: #6b7280; font-weight: 600; margin-bottom: 12px; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; display: block;">Settings</label>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Subject</label>
                                <select class="form-select" name="subject_id" id="subjectSelect" required onchange="loadClassesBySubject()" style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                                <option value="" disabled selected>Select Subject</option>
                                @foreach($subjects as $subject)
                                        <option value="{{ $subject->subject_id }}">{{ $subject->subject_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!--Select Class Drop Down-->

                            <div class="col-md-6 mb-3">
                                <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Class Assignment</label>
                                <div class="class-dropdown">
                                    <button type="button" class="form-select" onclick="toggleClassDropdown(event)" style="border-radius: 8px; padding: 12px 16px; border: 1px solid #d1d5db; font-size: 0.95rem; text-align: left; width: 100%;">
                                        <span id="selectedClassesText">Select Classes</span>
                                        <span class="selected-classes-counter" id="selectedClassesCounter" style="display: none;"></span>
                                        
                                    </button>
                                    <div id="classDropdownList" class="class-dropdown-list" style="display: none;">
                                        <div class="class-search">
                                            <input type="text" id="classSearchInput" placeholder="Search classes..." onkeyup="filterClasses(this)">
                                        </div>
                                        <div class="class-list" id="classCheckboxList">
                                            <div class="p-3 text-muted">Select subject first</div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="selected_classes" id="selectedClassesInput" value="">
                                </div>
                            </div>
                        </div>
                        <!--Select Term Drop Down-->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Term</label>
                                <select class="form-select" name="term" style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                                    <option value="" disabled selected>Select Term</option>
                                    <option value="Preliminaries">Preliminaries</option>
                                    <option value="Midterm">Midterm</option>
                                    <option value="Finals">Finals</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Duration</label>
                                <div class="input-group" style="border-radius: 8px; overflow: hidden;">
                                    <input type="number" class="form-control" name="duration" value="0" min="0" required style="border: 1px solid #d1d5db; font-size: 0.875rem; padding: 10px 14px;">
                                    <span class="input-group-text" style="background-color: #f9fafb; border: 1px solid #d1d5db; border-left: none; color: #6b7280; font-size: 0.875rem;">mins</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Schedule Start</label>
                                <input type="datetime-local" class="form-control" name="schedule_start" id="scheduleStart" required 
                                       style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                                <small class="text-danger" id="startDateError" style="display: none;"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Schedule End</label>
                                <input type="datetime-local" class="form-control" name="schedule_end" id="scheduleEnd" required 
                                       style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                                <small class="text-danger" id="endDateError" style="display: none;"></small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">
                                    <i class="bi bi-key-fill me-1"></i>Exam Password <span class="text-muted">(Optional)</span>
                                </label>
                                <input type="text" class="form-control" name="exam_password" placeholder="Enter password for exam access" 
                                       style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                                <small class="text-muted" style="font-size: 0.75rem;">Leave empty if no password is required</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn" style="background-color: #5f8a9a; color: white; border-radius: 8px; padding: 10px 28px; font-size: 0.95rem; font-weight: 500; border: none;">
                            Create New Exam
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Manage Collaborator Modal -->
<div class="modal fade" id="addCollaboratorModal" tabindex="-1" aria-labelledby="addCollaboratorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="collab-modal-header">
                <div class="collab-modal-title">
                    <i class="bi bi-people-fill"></i>
                    <span id="collabModalTitle">Manage Collaborators</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <!-- Tabs -->
                <div class="manage-collabs-tabs">
                    <button class="manage-tab active" onclick="switchTab('existing', event)">
                        <i class="bi bi-people"></i> Current Collaborators
                    </button>
                    <button class="manage-tab" onclick="switchTab('add', event)">
                        <i class="bi bi-person-plus"></i> Add New
                    </button>
                </div>

                <!-- Existing Collaborators Tab -->
                <div id="existingTab" class="tab-content active">
                    <div id="existingCollaboratorsList">
                        <p class="text-muted text-center p-3">Loading collaborators...</p>
                    </div>
                </div>

                <!-- Add New Collaborators Tab -->
                <div id="addTab" class="tab-content">
                    <div class="collab-search-bar">
                        <i class="bi bi-search" style="color: #9ca3af;"></i>
                        <input type="text" id="collabSearchInput" placeholder="Search for teachers" oninput="searchCollaborators()">
                    </div>

                    <div class="collab-search-results" id="collabSearchResults" style="display: none;">
                    </div>

                    <div class="selected-collabs-section" id="selectedCollabsSection">
                        <div id="selectedCollabsList">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="add-collab-submit-btn" id="addCollabSubmitBtn" disabled onclick="submitCollaborators()">
                            Add as Collaborator
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteExamModal" tabindex="-1" aria-labelledby="deleteExamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background-color: #dc3545; color: white; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="deleteExamModalLabel">Delete Exam</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p class="mb-0">Are you sure you want to delete the exam:</p>
                <p class="fw-bold mb-3" id="deleteExamTitle"></p>
                <p class="text-danger mb-4">This action cannot be undone.</p>
                
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDeleteExam()" style="border-radius: 8px;">Delete Exam</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Exam Modal -->
<div class="modal fade" id="editExamModal" tabindex="-1" aria-labelledby="editExamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background-color: #5f8a9a; color: white; border-radius: 12px 12px 0 0; padding: 16px 24px;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="editExamModalLabel">
                    <i class="bi bi-pencil-square"></i>
                    <span>Edit Exam</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="editExamForm">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="editExamId" name="exam_id">
                    
                    <div class="mb-3">
                        <input type="text" class="form-control" name="exam_title" id="editExamTitle" placeholder="Exam Title" required 
                               style="border-radius: 8px; padding: 12px 16px; border: 1px solid #d1d5db; font-size: 0.95rem;">
                    </div>

                    <div class="mb-3">
                        <input type="text" class="form-control" name="exam_desc" id="editExamDesc" placeholder="Exam Description" 
                               style="border-radius: 8px; padding: 12px 16px; border: 1px solid #d1d5db; font-size: 0.95rem;">
                    </div>

                    <label style="font-size: 0.75rem; color: #6b7280; font-weight: 600; margin-bottom: 12px; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; display: block;">Settings</label>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Subject</label>
                            <select class="form-select" name="subject_id" id="editSubjectSelect" required onchange="loadClassesBySubject('edit')" 
                                    style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                                <option value="" disabled selected>Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->subject_id }}">{{ $subject->subject_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Class Assignment</label>
                            <div class="class-dropdown">
                                <button type="button" class="form-select" onclick="toggleClassDropdown(event, 'edit')" 
                                        style="border-radius: 8px; padding: 12px 16px; border: 1px solid #d1d5db; font-size: 0.95rem; text-align: left; width: 100%;">
                                    <span id="editSelectedClassesText">Select Classes</span>
                                    <span class="selected-classes-counter" id="editSelectedClassesCounter" style="display: none;"></span>
                                </button>
                                <div id="editClassDropdownList" class="class-dropdown-list" style="display: none;">
                                    <div class="class-search">
                                        <input type="text" id="editClassSearchInput" placeholder="Search classes..." onkeyup="filterClasses(this, 'edit')">
                                    </div>
                                    <div class="class-list" id="editClassCheckboxList">
                                        <div class="p-3 text-muted">Select subject first</div>
                                    </div>
                                </div>
                                <input type="hidden" name="selected_classes" id="editSelectedClassesInput" value="">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Term</label>
                            <select class="form-select" name="term" id="editTermSelect" 
                                    style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                                <option value="" disabled selected>Select Term</option>
                                <option value="Preliminaries">Preliminaries</option>
                                <option value="Midterm">Midterm</option>
                                <option value="Finals">Finals</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Duration</label>
                            <div class="input-group" style="border-radius: 8px; overflow: hidden;">
                                <input type="number" class="form-control" name="duration" id="editDuration" value="0" min="0" required 
                                       style="border: 1px solid #d1d5db; font-size: 0.875rem; padding: 10px 14px;">
                                <span class="input-group-text" style="background-color: #f9fafb; border: 1px solid #d1d5db; border-left: none; color: #6b7280; font-size: 0.875rem;">mins</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Schedule Start</label>
                            <input type="datetime-local" class="form-control" name="schedule_start" id="editScheduleStart" required 
                                   style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                            <small class="text-danger" id="editStartDateError" style="display: none;"></small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">Schedule End</label>
                            <input type="datetime-local" class="form-control" name="schedule_end" id="editScheduleEnd" required 
                                   style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                            <small class="text-danger" id="editEndDateError" style="display: none;"></small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label style="font-size: 0.875rem; color: #374151; font-weight: 500; margin-bottom: 6px; display: block;">
                                <i class="bi bi-key-fill me-1"></i>Exam Password <span class="text-muted">(Optional)</span>
                            </label>
                            <input type="text" class="form-control" name="exam_password" id="editExamPassword" placeholder="Enter password for exam access" 
                                   style="border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; font-size: 0.875rem;">
                            <small class="text-muted" style="font-size: 0.75rem;">Leave empty if no password is required</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn" style="background-color: #5f8a9a; color: white; border-radius: 8px; padding: 10px 28px; font-size: 0.95rem; font-weight: 500; border: none;">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let selectedCollaborators = [];
    let currentExamId = null;
    let clickTimer = null;
    let preventSingleClick = false;

    // Handle single click vs double click on exam cards
    function handleCardClick(examId, cardElement) {
        clearTimeout(clickTimer);
        if (preventSingleClick) {
            preventSingleClick = false;
            return;
        }
        
        clickTimer = setTimeout(() => {
            if (!preventSingleClick) {
                loadExamDetails(examId, cardElement);
            }
        }, 250);
    }

    // Open exam editor/builder on double click
    function openExamEditor(examId) {
        preventSingleClick = true;
        clearTimeout(clickTimer);
        // Redirect to exam edit/builder page
        window.location.href = `/instructor/exams/create/${examId}`;
    }

    function openNewExamModal() {
        document.getElementById('newExamForm').reset();
        document.getElementById('selectedClassesText').textContent = 'Select Classes';
        if (document.getElementById('classCheckboxList')) {
            document.getElementById('classCheckboxList').innerHTML = '<div class="p-3 text-muted">Select subject first</div>';
        }
        const modal = new bootstrap.Modal(document.getElementById('newExamModal'));
        modal.show();
    }

    function loadClassesBySubject() {
        const subjectId = document.getElementById('subjectSelect').value;
        const classCheckboxList = document.getElementById('classCheckboxList');
        const selectedClassesText = document.getElementById('selectedClassesText');
        const selectedClassesCounter = document.getElementById('selectedClassesCounter');
        
        // Reset selection when subject changes
        selectedClassesText.textContent = 'Select Classes';
        selectedClassesCounter.style.display = 'none';
        document.getElementById('selectedClassesInput').value = '';
        
        if (!subjectId) {
            classCheckboxList.innerHTML = '<div class="p-3 text-muted">Select subject first</div>';
            return;
        }
        
        classCheckboxList.innerHTML = '<div class="p-3 text-muted">Loading classes...</div>';
        
        fetch(`{{ route('instructor.classes.get') }}?subject_id=${subjectId}`)
            .then(response => response.json())
            .then(classes => {
                if (classes.length === 0) {
                    classCheckboxList.innerHTML = '<div class="p-3 text-muted">No classes available for this subject</div>';
                } else {
                    classCheckboxList.innerHTML = classes.map(cls => `
                        <div class="class-item">
                            <input type="checkbox" id="class_${cls.class_id}" name="classes[]" 
                                   value="${cls.class_id}" onchange="updateSelectedClasses()"
                                   class="form-check-input me-2">
                            <label for="class_${cls.class_id}" class="form-check-label">${cls.display}</label>
                        </div>
                    `).join('');
                }
            })
            .catch(error => {
                console.error('Error loading classes:', error);
                classCheckboxList.innerHTML = '<div class="p-3 text-danger">Error loading classes. Please try again.</div>';
            });
    }

    function toggleClassDropdown(event, mode = 'new') {
        event.preventDefault();
        
        // Get element IDs based on mode
        const dropdownId = mode === 'edit' ? 'editClassDropdownList' : 'classDropdownList';
        const subjectSelectId = mode === 'edit' ? 'editSubjectSelect' : 'subjectSelect';
        const searchInputId = mode === 'edit' ? 'editClassSearchInput' : 'classSearchInput';
        
        const dropdown = document.getElementById(dropdownId);
        const subjectSelect = document.getElementById(subjectSelectId);
        
        if (!dropdown) {
            console.error('Dropdown not found:', dropdownId);
            return;
        }
        
        if (!subjectSelect) {
            console.error('Subject select not found:', subjectSelectId);
            return;
        }
        
        const subjectId = subjectSelect.value;
        
        if (!subjectId) {
            alert('Please select a subject first');
            return;
        }
        
        if (dropdown.style.display === 'none') {
            // Close any other open dropdowns first
            document.querySelectorAll('.class-dropdown-list').forEach(d => {
                if (d !== dropdown) d.style.display = 'none';
            });
            dropdown.style.display = 'block';
            // Focus the search input when opening
            const searchInput = document.getElementById(searchInputId);
            if (searchInput) searchInput.focus();
        } else {
            dropdown.style.display = 'none';
        }
    }

    function updateSelectedClasses(mode = 'new') {
        // Get element IDs based on mode
        const classCheckboxListId = mode === 'edit' ? 'editClassCheckboxList' : 'classCheckboxList';
        const selectedClassesTextId = mode === 'edit' ? 'editSelectedClassesText' : 'selectedClassesText';
        const selectedClassesCounterId = mode === 'edit' ? 'editSelectedClassesCounter' : 'selectedClassesCounter';
        const selectedClassesInputId = mode === 'edit' ? 'editSelectedClassesInput' : 'selectedClassesInput';
        
        const checkboxes = document.querySelectorAll(`#${classCheckboxListId} input[type="checkbox"]:checked`);
        const selectedClassesText = document.getElementById(selectedClassesTextId);
        const selectedClassesCounter = document.getElementById(selectedClassesCounterId);
        const selectedClassesInput = document.getElementById(selectedClassesInputId);
        
        if (!selectedClassesText || !selectedClassesCounter || !selectedClassesInput) {
            console.error('Required elements not found in updateSelectedClasses');
            return;
        }
        
        const selectedClasses = Array.from(checkboxes).map(cb => cb.value);
        const count = selectedClasses.length;
        
        // Update hidden input value
        selectedClassesInput.value = selectedClasses.join(',');
        
        // Update display text and counter
        if (count === 0) {
            selectedClassesText.textContent = 'Select Classes';
            selectedClassesCounter.style.display = 'none';
        } else {
            selectedClassesText.textContent = 'Classes Selected';
            selectedClassesCounter.textContent = count;
            selectedClassesCounter.style.display = 'inline-block';
        }
    }

    function filterClasses(input, mode = 'new') {
        const filter = input.value.toLowerCase();
        const items = input.closest('.class-dropdown-list').querySelectorAll('.class-item');
        let hasMatches = false;
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            const matches = text.includes(filter);
            item.style.display = matches ? '' : 'none';
            if (matches) hasMatches = true;
        });
        
        // Show no results message if no matches found
        const classList = input.closest('.class-dropdown-list').querySelector('.class-list');
        const noResults = classList.querySelector('.no-results');
        if (!hasMatches) {
            if (!noResults) {
                const message = document.createElement('div');
                message.className = 'no-results p-3 text-muted text-center';
                message.textContent = 'No matching classes found';
                classList.appendChild(message);
            }
        } else if (noResults) {
            noResults.remove();
        }
    }

    function loadClassesBySubject(mode = 'new') {
        const prefix = mode === 'edit' ? 'edit' : '';
        const subjectSelectId = mode === 'edit' ? 'editSubjectSelect' : 'subjectSelect';
        const subjectSelect = document.getElementById(subjectSelectId);
        
        if (!subjectSelect) {
            console.error('Subject select not found:', subjectSelectId);
            return Promise.reject();
        }
        
        const subjectId = subjectSelect.value;
        
        // Get element IDs based on mode
        const classCheckboxListId = mode === 'edit' ? 'editClassCheckboxList' : 'classCheckboxList';
        const selectedClassesTextId = mode === 'edit' ? 'editSelectedClassesText' : 'selectedClassesText';
        const selectedClassesCounterId = mode === 'edit' ? 'editSelectedClassesCounter' : 'selectedClassesCounter';
        const selectedClassesInputId = mode === 'edit' ? 'editSelectedClassesInput' : 'selectedClassesInput';
        
        const classCheckboxList = document.getElementById(classCheckboxListId);
        const selectedClassesText = document.getElementById(selectedClassesTextId);
        const selectedClassesCounter = document.getElementById(selectedClassesCounterId);
        const selectedClassesInput = document.getElementById(selectedClassesInputId);
        
        if (!classCheckboxList || !selectedClassesText || !selectedClassesCounter || !selectedClassesInput) {
            console.error('Required elements not found:', {
                classCheckboxList: !!classCheckboxList,
                selectedClassesText: !!selectedClassesText,
                selectedClassesCounter: !!selectedClassesCounter,
                selectedClassesInput: !!selectedClassesInput
            });
            return Promise.reject();
        }
        
        // Reset selection when subject changes in new mode
        if (mode === 'new') {
            selectedClassesText.textContent = 'Select Classes';
            selectedClassesCounter.style.display = 'none';
            selectedClassesInput.value = '';
        }
        
        if (!subjectId) {
            classCheckboxList.innerHTML = '<div class="p-3 text-muted">Select subject first</div>';
            return Promise.reject();
        }
        
        classCheckboxList.innerHTML = '<div class="p-3 text-muted">Loading classes...</div>';
        
        console.log('Loading classes for subject:', subjectId);
        
        return fetch(`{{ route('instructor.classes.get') }}?subject_id=${subjectId}`)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(classes => {
                console.log('Classes received:', classes);
                if (classes.length === 0) {
                    classCheckboxList.innerHTML = '<div class="p-3 text-muted">No classes available for this subject</div>';
                } else {
                    classCheckboxList.innerHTML = classes.map(cls => `
                        <div class="class-item">
                            <input type="checkbox" id="${classCheckboxListId.replace('CheckboxList', '')}_${cls.class_id}" name="classes[]" 
                                   value="${cls.class_id}" onchange="updateSelectedClasses('${mode}')"
                                   class="form-check-input me-2">
                            <label for="${classCheckboxListId.replace('CheckboxList', '')}_${cls.class_id}" class="form-check-label">${cls.display}</label>
                        </div>
                    `).join('');
                }
                return classes;
            })
            .catch(error => {
                console.error('Error loading classes:', error);
                classCheckboxList.innerHTML = '<div class="p-3 text-danger">Error loading classes. Please try again.</div>';
                throw error;
            });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.class-dropdown')) {
            document.getElementById('classDropdownList').style.display = 'none';
        }
    });

    function openAddCollaboratorModal(examId) {
        currentExamId = examId;
        selectedCollaborators = [];
        document.getElementById('selectedCollabsList').innerHTML = '';
        document.getElementById('collabSearchInput').value = '';
        document.getElementById('collabSearchResults').style.display = 'none';
        document.getElementById('addCollabSubmitBtn').disabled = true;
        
        // Switch to existing tab by default
        switchTab('existing');
        
        // Load existing collaborators
        loadExistingCollaborators(examId);
        
        const modal = new bootstrap.Modal(document.getElementById('addCollaboratorModal'));
        modal.show();
    }

    function switchTab(tabName, event) {
        // Update tab buttons
        document.querySelectorAll('.manage-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // If event is provided, set the clicked tab as active
        // Otherwise, set the first tab (existing) as active
        if (event && event.target) {
            event.target.closest('.manage-tab').classList.add('active');
        } else {
            // Default to first tab (existing collaborators)
            const tabs = document.querySelectorAll('.manage-tab');
            if (tabs.length > 0) {
                if (tabName === 'existing') {
                    tabs[0].classList.add('active');
                } else if (tabName === 'add') {
                    tabs[1].classList.add('active');
                }
            }
        }
        
        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        if (tabName === 'existing') {
            document.getElementById('existingTab').classList.add('active');
        } else if (tabName === 'add') {
            document.getElementById('addTab').classList.add('active');
        }
    }

    function loadExistingCollaborators(examId) {
        const container = document.getElementById('existingCollaboratorsList');
        container.innerHTML = '<p class="text-muted text-center p-3">Loading collaborators...</p>';
        
        fetch(`/instructor/exams/${examId}/collaborators`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const collaborators = data.collaborators;
                    const isOwner = data.is_owner;
                    
                    if (collaborators.length === 0) {
                        container.innerHTML = '<p class="text-muted text-center p-3">No collaborators yet</p>';
                        return;
                    }
                    
                    container.innerHTML = collaborators.map(collab => {
                        const initials = collab.name.split(' ').map(n => n[0]).join('').toUpperCase();
                        const isCollaboratorRole = collab.role === 'collaborator';
                        const canRemove = isOwner && isCollaboratorRole;
                        
                        return `
                            <div class="existing-collab-item">
                                <div class="existing-collab-info">
                                    <div class="existing-collab-avatar">
                                        ${initials}
                                    </div>
                                    <div class="existing-collab-details">
                                        <div class="existing-collab-name">${collab.name}</div>
                                        <div class="existing-collab-email">${collab.email}</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="existing-collab-role ${collab.role}">${collab.role === 'owner' ? 'Owner' : 'Collaborator'}</span>
                                    ${canRemove ? `
                                        <button class="remove-existing-collab-btn" onclick="removeExistingCollaborator(${collab.id}, '${collab.name}')">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    container.innerHTML = '<p class="text-danger text-center p-3">Error loading collaborators</p>';
                }
            })
            .catch(error => {
                console.error('Error loading collaborators:', error);
                container.innerHTML = '<p class="text-danger text-center p-3">Error loading collaborators</p>';
            });
    }

    function removeExistingCollaborator(teacherId, teacherName) {
        if (!confirm(`Are you sure you want to remove ${teacherName} as a collaborator?`)) {
            return;
        }
        
        fetch(`/instructor/exams/${currentExamId}/collaborators/${teacherId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the collaborators list
                loadExistingCollaborators(currentExamId);
                
                // Reload exam details to update the sidebar
                const activeCard = document.querySelector('.exam-card-wrapper.active');
                if (activeCard) {
                    loadExamDetails(currentExamId, activeCard);
                }
                
                // Show success message
                showAlert('success', data.message);
            } else {
                alert('Error: ' + (data.error || 'Failed to remove collaborator'));
            }
        })
        .catch(error => {
            console.error('Error removing collaborator:', error);
            alert('Failed to remove collaborator. Please try again.');
        });
    }

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.exam-content .container-fluid').insertBefore(
            alertDiv,
            document.querySelector('.search-bar')
        );
        setTimeout(() => alertDiv.remove(), 5000);
    }

    let searchTimeout;
    function searchCollaborators() {
        const searchTerm = document.getElementById('collabSearchInput').value;
        const resultsContainer = document.getElementById('collabSearchResults');
        
        if (searchTerm.length === 0) {
            resultsContainer.style.display = 'none';
            return;
        }
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetch(`{{ route('instructor.teachers.search') }}?search=${encodeURIComponent(searchTerm)}&exam_id=${currentExamId}`)
                .then(response => response.json())
                .then(teachers => {
                    if (teachers.length > 0) {
                        resultsContainer.style.display = 'block';
                        resultsContainer.innerHTML = teachers.map(teacher => `
                            <div class="collab-user-item" onclick="selectCollaborator(${teacher.id}, '${teacher.name}', '${teacher.email}')">
                                <div class="collab-user-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                                    ${teacher.name.charAt(0).toUpperCase()}
                                </div>
                                <div class="collab-user-info">
                                    <div class="collab-user-name">${teacher.name}</div>
                                    <div class="collab-user-email">${teacher.email}</div>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        resultsContainer.style.display = 'block';
                        resultsContainer.innerHTML = '<p class="text-muted text-center p-3">No teachers found</p>';
                    }
                })
                .catch(error => {
                    console.error('Error searching teachers:', error);
                    resultsContainer.innerHTML = '<p class="text-danger text-center p-3">Error searching teachers</p>';
                });
        }, 300);
    }

    function selectCollaborator(teacherId, name, email) {
        const teacher = { id: teacherId, name: name, email: email };
        
        if (!selectedCollaborators.find(c => c.id === teacherId)) {
            selectedCollaborators.push(teacher);
            renderSelectedCollaborators();
            document.getElementById('addCollabSubmitBtn').disabled = false;
            
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
            container.innerHTML = '<p class="text-muted text-center" style="padding: 20px 0;">No collaborators selected</p>';
            return;
        }

        container.innerHTML = selectedCollaborators.map(collab => `
            <div class="selected-collab-item">
                <div class="selected-collab-info">
                    <div class="selected-collab-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                        ${collab.name.charAt(0).toUpperCase()}
                    </div>
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
        if (selectedCollaborators.length === 0 || !currentExamId) return;
        
        const submitBtn = document.getElementById('addCollabSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Adding...';
        
        fetch(`/instructor/exams/${currentExamId}/collaborators`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                collaborators: selectedCollaborators.map(c => c.id)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear the selected collaborators and reset the form
                selectedCollaborators = [];
                document.getElementById('selectedCollabsList').innerHTML = '';
                document.getElementById('collabSearchInput').value = '';
                document.getElementById('collabSearchResults').style.display = 'none';
                submitBtn.disabled = true;
                submitBtn.textContent = 'Add as Collaborator';
                
                // Reload existing collaborators
                loadExistingCollaborators(currentExamId);
                
                // Switch to existing tab
                document.querySelectorAll('.manage-tab').forEach(tab => tab.classList.remove('active'));
                document.querySelectorAll('.manage-tab')[0].classList.add('active');
                document.getElementById('existingTab').classList.add('active');
                document.getElementById('addTab').classList.remove('active');
                
                // Reload exam details to show new collaborators in sidebar
                const activeCard = document.querySelector('.exam-card-wrapper.active');
                if (activeCard) {
                    loadExamDetails(currentExamId, activeCard);
                }
                
                // Show success message
                showAlert('success', data.message);
            } else {
                alert('Error: ' + (data.error || 'Failed to add collaborators'));
                submitBtn.disabled = false;
                submitBtn.textContent = 'Add as Collaborator';
            }
        })
        .catch(error => {
            console.error('Error adding collaborators:', error);
            alert('Failed to add collaborators. Please try again.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Add as Collaborator';
        });
    }

    function toggleCardMenu(event, menuId) {
        event.stopPropagation();
        event.preventDefault();
        
        const menu = document.getElementById(menuId);
        const card = event.target.closest('.exam-card-wrapper');
        
        // Close all other menus and remove menu-open class from all cards
        document.querySelectorAll('.menu-dropdown').forEach(m => {
            if (m.id !== menuId) {
                m.classList.remove('show');
            }
        });
        document.querySelectorAll('.exam-card-wrapper').forEach(c => {
            if (c !== card) {
                c.classList.remove('menu-open');
            }
        });
        
        // Toggle current menu and card class
        const isShowing = menu.classList.toggle('show');
        if (isShowing) {
            card.classList.add('menu-open');
        } else {
            card.classList.remove('menu-open');
        }
    }

    function closeAllMenus() {
        document.querySelectorAll('.menu-dropdown').forEach(m => {
            m.classList.remove('show');
        });
        document.querySelectorAll('.exam-card-wrapper').forEach(card => {
            card.classList.remove('menu-open');
        });
    }

    function loadExamDetails(examId, cardElement) {
        document.querySelectorAll('.exam-card-wrapper').forEach(card => {
            card.classList.remove('active');
        });
        
        cardElement.classList.add('active');

        fetch(`/instructor/api/exams/${examId}/details`)
            .then(response => response.json())
            .then(data => {
                const exam = data.exam;
                
                document.getElementById('detail-title').textContent = exam.exam_title;
                document.getElementById('detail-subject').textContent = data.subject_name;
                document.getElementById('detail-created').textContent = data.formatted_created_at;
                
                const modifiedElement = document.getElementById('detail-modified');
                if (modifiedElement) {
                    modifiedElement.textContent = data.formatted_updated_at || data.formatted_created_at || 'N/A';
                }
                
                const statusBadge = document.getElementById('detail-status');
                // Format status properly: "for approval" → "For Approval", "draft" → "Draft"
                const formattedStatus = exam.status.split(' ').map(word => 
                    word.charAt(0).toUpperCase() + word.slice(1)
                ).join(' ');
                statusBadge.textContent = formattedStatus;
                // Convert status to CSS class: "for approval" → "for-approval"
                statusBadge.className = 'status-badge status-' + exam.status.replace(/ /g, '-');
                
                if (document.getElementById('detail-notes')) {
                    document.getElementById('detail-notes').textContent = exam.revision_notes || 'N/A';
                }
                
                // Update collaborator display
                updateCollaboratorDisplay(data.collaborators || [], data.creator_name, exam.status, exam.exam_id);
            })
            .catch(error => {
                console.error('Error loading exam details:', error);
                alert('Failed to load exam details');
            });
    }

    function updateCollaboratorDisplay(collaborators, creatorName, examStatus, examId) {
        const listContainer = document.querySelector('.collaborator-list');
        
        if (!listContainer) return;
        
        // Clear existing list
        listContainer.innerHTML = '';
        
        // Update or create the "Manage Collaborators" button based on status
        const collaboratorHeader = document.querySelector('.collaborator-header');
        let addCollabBtn = collaboratorHeader.querySelector('.add-collab-btn');
        
        if (examStatus === 'draft') {
            if (!addCollabBtn) {
                // Create button if it doesn't exist
                addCollabBtn = document.createElement('button');
                addCollabBtn.className = 'add-collab-btn';
                addCollabBtn.innerHTML = '<i class="bi bi-people"></i> Manage Collaborators';
                collaboratorHeader.appendChild(addCollabBtn);
            }
            addCollabBtn.onclick = () => openAddCollaboratorModal(examId);
        } else {
            // Remove button if status is not draft
            if (addCollabBtn) {
                addCollabBtn.remove();
            }
        }
        
        // Get creator initials
        const nameParts = creatorName.split(' ');
        const creatorInitials = nameParts.map(part => part.charAt(0).toUpperCase()).join('');
        
        // Add owner item
        const ownerItem = document.createElement('div');
        ownerItem.className = 'collaborator-item';
        ownerItem.innerHTML = `
            <div class="collaborator-item-avatar">
                ${creatorInitials}
            </div>
            <div class="collaborator-item-info">
                <div class="collaborator-item-name">
                    ${creatorName}
                </div>
                <div class="collaborator-item-role">
                    <span class="role-badge-owner">Owner</span>
                </div>
            </div>
        `;
        listContainer.appendChild(ownerItem);
        
        // Add collaborator items
        collaborators.forEach(collab => {
            const collabNameParts = collab.name.split(' ');
            const collabInitials = collabNameParts.map(part => part.charAt(0).toUpperCase()).join('');
            
            const collabItem = document.createElement('div');
            collabItem.className = 'collaborator-item';
            collabItem.innerHTML = `
                <div class="collaborator-item-avatar">
                    ${collabInitials}
                </div>
                <div class="collaborator-item-info">
                    <div class="collaborator-item-name">
                        ${collab.name}
                    </div>
                    <div class="collaborator-item-role">
                        Collaborator
                    </div>
                </div>
            `;
            listContainer.appendChild(collabItem);
        });
    }

    function downloadExam(examId) {
        // Implement download functionality
        alert('Download functionality for exam ' + examId);
    }

    function editExam(examId) {
        // Fetch exam details
        fetch(`/instructor/api/exams/${examId}/details`)
            .then(response => response.json())
            .then(data => {
                const exam = data.exam;
                
                // Populate form fields
                document.getElementById('editExamId').value = exam.exam_id;
                document.getElementById('editExamTitle').value = exam.exam_title;
                document.getElementById('editExamDesc').value = exam.exam_desc || '';
                document.getElementById('editSubjectSelect').value = exam.subject_id;
                document.getElementById('editTermSelect').value = exam.term;
                document.getElementById('editDuration').value = exam.duration;
                document.getElementById('editExamPassword').value = exam.exam_password || '';
                
                // Format and set dates
                if (exam.schedule_start) {
                    document.getElementById('editScheduleStart').value = 
                        new Date(exam.schedule_start).toISOString().slice(0, 16);
                }
                if (exam.schedule_end) {
                    document.getElementById('editScheduleEnd').value = 
                        new Date(exam.schedule_end).toISOString().slice(0, 16);
                }

                // Load classes for the selected subject
                loadClassesBySubject('edit').then(() => {
                    // Set selected classes
                    const selectedClasses = exam.class_assignments || [];
                    selectedClasses.forEach(classId => {
                        const checkbox = document.querySelector(`#editClassCheckboxList input[value="${classId}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                    updateSelectedClasses('edit');
                });
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('editExamModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching exam details:', error);
                alert('Failed to load exam details');
            });
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.menu-dots') && !event.target.closest('.menu-dropdown')) {
            document.querySelectorAll('.menu-dropdown').forEach(m => {
                m.classList.remove('show');
            });
            document.querySelectorAll('.exam-card-wrapper').forEach(card => {
                card.classList.remove('menu-open');
            });
        }
    });

    document.querySelectorAll('.menu-dropdown').forEach(dropdown => {
        dropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });

    // Delete exam functionality
    let examToDelete = null;

    function deleteExam(examId, examTitle) {
        examToDelete = examId;
        document.getElementById('deleteExamTitle').textContent = examTitle;
        const modal = new bootstrap.Modal(document.getElementById('deleteExamModal'));
        modal.show();
    }

    function confirmDeleteExam() {
        if (!examToDelete) return;

        const deleteButton = document.querySelector('#deleteExamModal .btn-danger');
        const originalText = deleteButton.textContent;
        deleteButton.disabled = true;
        deleteButton.textContent = 'Deleting...';

        // Create form data with CSRF token and method spoofing
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('_method', 'DELETE');

        fetch(`/instructor/exams/${examToDelete}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteExamModal'));
                modal.hide();

                // Remove the exam card from the UI
                const examCard = document.querySelector(`[data-exam-id="${examToDelete}"]`);
                if (examCard) {
                    const parentCol = examCard.closest('.col-lg-4');
                    if (parentCol) {
                        parentCol.remove();
                    }
                }

                // Clear the details panel if this was the selected exam
                const detailsPanel = document.getElementById('exam-details-panel');
                if (detailsPanel) {
                    detailsPanel.innerHTML = `
                        <div class="details-panel">
                            <div class="empty-details">
                                <i class="bi bi-file-text"></i>
                                <p class="text-muted">Select an exam to view details</p>
                            </div>
                        </div>
                    `;
                }

                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = `
                    Exam deleted successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.exam-content .container-fluid').insertBefore(
                    alertDiv,
                    document.querySelector('.search-bar')
                );
                setTimeout(() => alertDiv.remove(), 5000);

                // Show "No exams" message if this was the last exam
                const examCards = document.querySelectorAll('.exam-card-wrapper');
                if (examCards.length === 0) {
                    const examContainer = document.querySelector('.col-lg-7');
                    if (examContainer) {
                        examContainer.innerHTML = `
                            <div class="recents-label">Recents</div>
                            <div class="no-exams">
                                <i class="bi bi-folder2-open"></i>
                                <p class="mt-3">No exams found. Create your first exam!</p>
                            </div>
                        `;
                    }
                }
            } else {
                alert('Error: ' + (data.error || 'Failed to delete exam'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete exam. Please try again.');
        })
        .finally(() => {
            deleteButton.disabled = false;
            deleteButton.textContent = originalText;
            examToDelete = null;
        });
    }

    // Date validation functions
    document.addEventListener('DOMContentLoaded', function() {
        // Live search functionality
        const searchInput = document.getElementById('examSearchInput');
        let searchTimeout;
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = this.value.trim();
                
                // Debounce search by 300ms
                searchTimeout = setTimeout(() => {
                    performSearch(searchTerm);
                }, 300);
            });
        }
        
        function performSearch(searchTerm) {
            const url = new URL('{{ route('instructor.exams.index') }}', window.location.origin);
            if (searchTerm) {
                url.searchParams.set('search', searchTerm);
            }
            
            // Show loading state
            const examCardsContainer = document.querySelector('.col-lg-8 .row');
            if (examCardsContainer) {
                examCardsContainer.innerHTML = '<div class="col-12 text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            }
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Parse the HTML response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Extract the exam cards section
                const newExamCards = doc.querySelector('.col-lg-8 .row');
                if (newExamCards && examCardsContainer) {
                    examCardsContainer.innerHTML = newExamCards.innerHTML;
                }
                
                // Update URL without reload
                const newUrl = new URL(window.location.href);
                if (searchTerm) {
                    newUrl.searchParams.set('search', searchTerm);
                } else {
                    newUrl.searchParams.delete('search');
                }
                window.history.pushState({}, '', newUrl);
            })
            .catch(error => {
                console.error('Search error:', error);
                if (examCardsContainer) {
                    examCardsContainer.innerHTML = '<div class="col-12"><div class="alert alert-danger">Error loading exams. Please try again.</div></div>';
                }
            });
        }
        
        const submitButton = document.querySelector('#newExamForm button[type="submit"]');
        const startDateInput = document.getElementById('scheduleStart');
        const endDateInput = document.getElementById('scheduleEnd');

        // Set initial min dates
        updateMinDates();

        // Update min dates every minute
        setInterval(updateMinDates, 60000);

        // Add input event listeners
        startDateInput.addEventListener('input', validateDates);
        endDateInput.addEventListener('input', validateDates);

        function updateMinDates() {
            const now = new Date();
            const tzOffset = now.getTimezoneOffset() * 60000;
            const localISOTime = (new Date(Date.now() - tzOffset)).toISOString().slice(0, 16);
            startDateInput.min = localISOTime;
            
            if (startDateInput.value) {
                endDateInput.min = startDateInput.value;
            } else {
                endDateInput.min = localISOTime;
            }
        }

        function validateDates() {
            const startDate = startDateInput.value ? new Date(startDateInput.value) : null;
            const endDate = endDateInput.value ? new Date(endDateInput.value) : null;
            const now = new Date();
            let isValid = true;
            
            // Validate start date
            const startError = document.getElementById('startDateError');
            startError.style.display = 'none';
            
            if (startDate) {
                if (startDate < now) {
                    startError.textContent = 'Start date/time cannot be in the past';
                    startError.style.display = 'block';
                    startDateInput.value = '';
                    isValid = false;
                }
            }

            // Validate end date
            const endError = document.getElementById('endDateError');
            endError.style.display = 'none';

            if (startDate && endDate) {
                if (endDate <= startDate) {
                    endError.textContent = 'End date/time must be after start date/time';
                    endError.style.display = 'block';
                    endDateInput.value = '';
                    isValid = false;
                }
            }

            // Enable/disable submit button based on validation and required fields
            submitButton.disabled = !isValid || !startDate || !endDate;

            return isValid;
        }

        // Add form submit validation
        document.getElementById('newExamForm').addEventListener('submit', function(e) {
            if (!validateDates()) {
                e.preventDefault();
            }
        });

        // Edit Exam Form Submission Handler
        const editExamForm = document.getElementById('editExamForm');
        if (editExamForm) {
            console.log('Edit exam form found, attaching event listener');
            editExamForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Edit form submitted!');
                
                const formData = new FormData(this);
                const examId = formData.get('exam_id');
                
                console.log('Submitting edit for exam ID:', examId);
                console.log('Form data entries:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, ':', value);
                }
                
                fetch(`/instructor/exams/${examId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editExamModal'));
                        modal.hide();
                        
                        // Show success message
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show';
                        alertDiv.innerHTML = `
                            Exam updated successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.querySelector('.exam-content .container-fluid').insertBefore(
                            alertDiv,
                            document.querySelector('.search-bar')
                        );
                        
                        // Reload the page to show updated data
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        alert('Error updating exam: ' + (data.message || data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Failed to update exam: ' + error.message);
                });
            });
        } else {
            console.log('Edit exam form NOT found');
        }
    });
</script>
   