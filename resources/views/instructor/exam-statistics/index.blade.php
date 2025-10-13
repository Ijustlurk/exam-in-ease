@extends('layouts.Instructor.app')

@section('content')
<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background-color: #e8eef2;
    }
    
    .exam-table-container {
        padding: 24px;
        background-color: #e8eef2;
        min-height: 100vh;
    }
    
    .exam-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    
    .exam-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #212529;
    }
    
    .search-box {
        position: relative;
        width: 100%;
        max-width: 800px;
    }
    
    .search-box input {
        width: 100%;
        padding: 12px 50px 12px 16px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.95rem;
        background-color: white;
        font-style: italic;
        color: #9ca3af;
    }
    
    .search-box input:focus {
        outline: none;
        border-color: #7ca5b8;
        box-shadow: 0 0 0 3px rgba(124, 165, 184, 0.1);
    }
    
    .search-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 1.2rem;
    }
    
    .exam-table-wrapper {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .exam-table {
        width: 100%;
        margin: 0;
    }
    
    .exam-table thead {
        background-color: #e1ecf1;
    }
    
    .exam-table thead th {
        padding: 16px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #212529;
        border: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .exam-table thead th i {
        margin-left: 4px;
        font-size: 0.75rem;
    }
    
    .exam-table tbody td {
        padding: 20px 16px;
        border-bottom: 1px solid #f1f3f5;
        vertical-align: middle;
        color: #212529;
    }
    
    .exam-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .exam-table tbody tr:hover {
        background-color: #f9fafb;
    }
    
    .checkbox-cell {
        width: 50px;
    }
    
    .checkbox-cell input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        border: 2px solid #d1d5db;
        border-radius: 4px;
    }
    
    .exam-name-cell {
        min-width: 250px;
    }
    
    .exam-name {
        font-size: 0.95rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 4px;
    }
    
    .teacher-name {
        font-size: 0.8rem;
        color: #9ca3af;
        font-style: italic;
    }
    
    .subject-cell {
        font-size: 0.9rem;
        color: #212529;
        min-width: 200px;
    }
    
    .classes-cell {
        font-size: 0.9rem;
        color: #6b7280;
        min-width: 150px;
    }
    
    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: capitalize;
    }
    
    .status-ongoing {
        background-color: #fff3e0;
        color: #e65100;
    }
    
    .status-approved {
        background-color: #fff9c4;
        color: #f57f17;
    }
    
    .status-responses {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
    
    .actions-cell {
        min-width: 200px;
    }
    
    .action-btn {
        background: transparent;
        border: none;
        color: #6b7280;
        font-size: 0.9rem;
        cursor: pointer;
        padding: 6px 12px;
        margin-right: 8px;
        transition: color 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .action-btn:hover {
        color: #374151;
    }
    
    .action-btn i {
        font-size: 1rem;
    }
    
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 24px;
        padding: 20px;
    }
    
    .pagination-btn {
        padding: 8px 16px;
        background-color: white;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        color: #374151;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .pagination-btn:hover:not(:disabled) {
        background-color: #f9fafb;
        border-color: #9ca3af;
    }
    
    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .pagination-btn.active {
        background-color: #374151;
        color: white;
        border-color: #374151;
    }
    
    .page-number {
        padding: 8px 14px;
        background-color: white;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        color: #374151;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        min-width: 40px;
        text-align: center;
    }
    
    .page-number:hover {
        background-color: #f9fafb;
        border-color: #9ca3af;
    }
    
    .page-number.active {
        background-color: #374151;
        color: white;
        border-color: #374151;
    }
</style>

<div class="exam-table-container">
    <div class="container-fluid">
        <!-- Header -->
        <div class="exam-header">
            <h1 class="exam-title">Your Exams</h1>
            <div class="search-box">
                <input type="text" placeholder="Search for exams">
                <i class="bi bi-search search-icon"></i>
            </div>
        </div>
        
        <!-- Table -->
        <div class="exam-table-wrapper">
            <table class="exam-table">
                <thead>
                    <tr>
                        <th class="checkbox-cell">
                            <input type="checkbox">
                        </th>
                        <th class="exam-name-cell">
                            EXAM NAME <i class="bi bi-caret-down-fill"></i>
                        </th>
                        <th class="subject-cell">
                            SUBJECT <i class="bi bi-caret-down-fill"></i>
                        </th>
                        <th class="classes-cell">
                            CLASSES <i class="bi bi-caret-down-fill"></i>
                        </th>
                        <th>
                            EXAM STATUS <i class="bi bi-caret-down-fill"></i>
                        </th>
                        <th class="actions-cell">
                            ACTIONS
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row 1 -->
                    <tr>
                        <td class="checkbox-cell">
                            <input type="checkbox">
                        </td>
                        <td class="exam-name-cell">
                            <div class="exam-name">Midterm Computer Programming</div>
                            <div class="teacher-name">Teacher Name</div>
                        </td>
                        <td class="subject-cell">
                            Computer Programming 1
                        </td>
                        <td class="classes-cell">
                            1A and more...
                        </td>
                        <td>
                            <span class="status-badge status-ongoing">Ongoing</span>
                        </td>
                        <td class="actions-cell">
                            <a href="{{ route('instructor.exam-statistics.show') }}">
                            <button class="action-btn">
                                <i class="bi bi-search"></i> View
                            </button>
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Row 2 -->
                    <tr>
                        <td class="checkbox-cell">
                            <input type="checkbox">
                        </td>
                        <td class="exam-name-cell">
                            <div class="exam-name">Midterm Advanced Python</div>
                            <div class="teacher-name">Teacher Name</div>
                        </td>
                        <td class="subject-cell">
                            Advanced Python
                        </td>
                        <td class="classes-cell">
                            1A and more...
                        </td>
                        <td>
                            <span class="status-badge status-approved">Approved</span>
                        </td>
                        <td class="actions-cell">
                            <button class="action-btn">
                                <i class="bi bi-search"></i> View
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Row 3 -->
                    <tr>
                        <td class="checkbox-cell">
                            <input type="checkbox">
                        </td>
                        <td class="exam-name-cell">
                            <div class="exam-name">Midterm System Administration</div>
                            <div class="teacher-name">Teacher Name</div>
                        </td>
                        <td class="subject-cell">
                            System Administration and Management
                        </td>
                        <td class="classes-cell">
                            1A and more...
                        </td>
                        <td>
                            <span class="status-badge status-responses">Responses Gathered</span>
                        </td>
                        <td class="actions-cell">
                            <button class="action-btn">
                                <i class="bi bi-search"></i> View
                            </button>
                            <button class="action-btn">
                                <i class="bi bi-bar-chart-fill"></i> See Stats
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="pagination-wrapper">
            <button class="pagination-btn" disabled>Prev</button>
            <div class="page-number active">1</div>
            <div class="page-number">2</div>
            <div class="page-number">3</div>
            <div class="page-number">4</div>
            <div class="page-number">5</div>
            <button class="pagination-btn">Next</button>
        </div>
    </div>
</div>

@endsection