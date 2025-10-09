@extends('layouts.ProgramChair.app')

@section('content')
<style>
    .exams-container {
        background-color: #f5f5f5;
        min-height: 100vh;
        padding: 2rem;
    }
    
    .exams-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .exams-title {
        font-size: 2rem;
        font-weight: bold;
        color: #1a1a1a;
        margin: 0;
    }
    
    .search-box {
        position: relative;
        width: 400px;
    }
    
    .search-box input {
        width: 100%;
        padding: 0.65rem 2.5rem 0.65rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.95rem;
        color: #6b7280;
    }
    
    .search-box input:focus {
        outline: none;
        border-color: #9ca3af;
        box-shadow: 0 0 0 1px #9ca3af;
    }
    
    .search-icon {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }
    
    .exams-table-wrapper {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }
    
    .exams-table {
        width: 100%;
        margin-bottom: 0;
    }
    
    .exams-table thead {
        background-color: #dbeafe;
    }
    
    .exams-table thead th {
        padding: 1rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .exams-table thead th.sortable {
        cursor: pointer;
        position: relative;
    }
    
    .exams-table thead th.sortable::after {
        content: '▼';
        font-size: 0.7rem;
        margin-left: 0.25rem;
        color: #6b7280;
    }
    
    .exams-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.15s;
    }
    
    .exams-table tbody tr:hover {
        background-color: #f9fafb;
    }
    
    .exams-table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
    }
    
    .exam-name {
        font-size: 0.875rem;
        font-weight: bold;
        color: #111827;
        margin-bottom: 0.25rem;
    }
    
    .teacher-name {
        font-size: 0.875rem;
        color: #6b7280;
        font-style: italic;
        margin: 0;
    }
    
    .subject-text {
        font-size: 0.875rem;
        color: #111827;
    }
    
    .classes-text {
        font-size: 0.875rem;
        color: #111827;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        font-size: 0.875rem;
        font-weight: bold;
        border-radius: 0.25rem;
        background: white;
    }
    
    .status-ongoing {
        color: #f97316;
    }
    
    .status-archived {
        color: #16a34a;
    }
    
    .status-draft {
        color: #06b6d4;
    }
    
    .status-pending {
        color: #f97316;
    }
    
    .action-buttons {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    .btn-view {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0;
        border: none;
        background: none;
        color: #9ca3af;
        font-size: 0.875rem;
        cursor: pointer;
        transition: color 0.15s;
    }
    
    .btn-view:hover {
        color: #4b5563;
    }
    
    .btn-stats {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0;
        border: none;
        background: none;
        color: #9ca3af;
        font-size: 0.875rem;
        cursor: pointer;
        transition: color 0.15s;
    }
    
    .btn-stats:hover {
        color: #4b5563;
    }
    
    .pagination-wrapper {
        display: flex;
        gap: 0.375rem;
        margin-top: 1.5rem;
    }
    
    .pagination-wrapper .page-btn {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background: white;
        color: #374151;
        cursor: pointer;
        transition: all 0.15s;
    }
    
    .pagination-wrapper .page-btn:hover {
        background-color: #f9fafb;
    }
    
    .pagination-wrapper .page-btn.active {
        background-color: #2563eb;
        border-color: #2563eb;
        color: white;
    }
    
    .form-check-input {
        width: 1rem;
        height: 1rem;
        border-radius: 0.25rem;
        border: 1px solid #d1d5db;
        cursor: pointer;
    }
    
    .form-check-input:checked {
        background-color: #2563eb;
        border-color: #2563eb;
    }
</style>

<div class="exams-container">
    <!-- Header -->
    <div class="exams-header">
        <h1 class="exams-title">Exams</h1>
        <div class="search-box">
            <input type="text" placeholder="Search for exams">
            <svg class="search-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </div>

    <!-- Table -->
    <div class="exams-table-wrapper">
        <table class="exams-table">
            <thead>
                <tr>
                    <th style="width: 50px;">
                        <input class="form-check-input" type="checkbox">
                    </th>
                    <th class="sortable">Exam Name</th>
                    <th class="sortable">Subject</th>
                    <th>Classes</th>
                    <th class="sortable">Exam Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Row 1 -->
                <tr>
                    <td>
                        <input class="form-check-input" type="checkbox">
                    </td>
                    <td>
                        <div class="exam-name">Midterm Computer Programming</div>
                        <p class="teacher-name">Teacher Name</p>
                    </td>
                    <td class="subject-text">Computer Programming 1</td>
                    <td class="classes-text">1A and more...</td>
                    <td>
                        <span class="status-badge status-ongoing">Ongoing</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-view">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                View
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 2 -->
                <tr>
                    <td>
                        <input class="form-check-input" type="checkbox">
                    </td>
                    <td>
                        <div class="exam-name">Midterm Discrete Structures</div>
                        <p class="teacher-name">Teacher Name</p>
                    </td>
                    <td class="subject-text">Discrete Structures</td>
                    <td class="classes-text">2A and more...</td>
                    <td>
                        <span class="status-badge status-ongoing">Ongoing</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-view">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                View
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 3 -->
                <tr>
                    <td>
                        <input class="form-check-input" type="checkbox">
                    </td>
                    <td>
                        <div class="exam-name">Midterm System Administration</div>
                        <p class="teacher-name">Teacher Name</p>
                    </td>
                    <td class="subject-text">System Administration and Management</td>
                    <td class="classes-text">4A and more...</td>
                    <td>
                        <span class="status-badge status-archived">Archived</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-view">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                View
                            </button>
                            <button class="btn-stats">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                See Stats
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 4 -->
                <tr>
                    <td>
                        <input class="form-check-input" type="checkbox">
                    </td>
                    <td>
                        <div class="exam-name">Midterm Multimedia</div>
                        <p class="teacher-name">Teacher Name</p>
                    </td>
                    <td class="subject-text">Multimedia</td>
                    <td class="classes-text">2B and more...</td>
                    <td>
                        <span class="status-badge status-draft">Draft</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-view">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                View
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 5 -->
                <tr>
                    <td>
                        <input class="form-check-input" type="checkbox">
                    </td>
                    <td>
                        <div class="exam-name">Midterm Multimedia</div>
                        <p class="teacher-name">Teacher Name</p>
                    </td>
                    <td class="subject-text">Multimedia</td>
                    <td class="classes-text">2B and more...</td>
                    <td>
                        <span class="status-badge status-draft">Draft</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-view">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                View
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 6 -->
                <tr>
                    <td>
                        <input class="form-check-input" type="checkbox">
                    </td>
                    <td>
                        <div class="exam-name">Midterm DSA</div>
                        <p class="teacher-name">Teacher Name</p>
                    </td>
                    <td class="subject-text">Discrete Structures</td>
                    <td class="classes-text">2A and more...</td>
                    <td>
                        <span class="status-badge status-pending">Pending Approval</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-view">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                View
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        <button class="page-btn">Prev</button>
        <button class="page-btn active">1</button>
        <button class="page-btn">2</button>
        <button class="page-btn">3</button>
        <button class="page-btn">4</button>
        <button class="page-btn">5</button>
        <button class="page-btn">Next</button>
    </div>
</div>
@endsection