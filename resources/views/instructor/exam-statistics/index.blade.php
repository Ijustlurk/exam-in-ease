@extends('layouts.Instructor.app')

@section('content')

<style>
    .exam-content {
        padding: 30px;
        min-height: 100vh;
        margin-left: 60px;
        transition: margin-left 0.3s;
    }

    .exam-content.expanded {
        margin-left: 220px;
    }

    .exams-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
    }

    .search-wrapper {
        flex: 0 0 400px;
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 12px 45px 12px 20px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 15px;
    }

    .table-container {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .exams-table {
        width: 100%;
        border-collapse: collapse;
    }

    .exams-table thead {
        background-color: #d4e5ea;
    }

    .exams-table thead th {
        padding: 18px 20px;
        font-weight: 600;
        text-align: left;
    }

    .exams-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
    }

    .exams-table tbody td {
        padding: 20px;
    }

    .action-btn {
        color: #95a5a6;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .action-btn:hover {
        color: #6ba5b3;
    }

    .action-btn i {
        font-size: 16px;
    }

    .classes-cell {
        position: relative;
        cursor: pointer;
    }

    .classes-tooltip {
        position: fixed;
        background-color: #2c3e50;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s, visibility 0.2s;
        max-width: 400px;
        min-width: 200px;
        white-space: normal;
        word-wrap: break-word;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        pointer-events: none;
        transform: translate(-50%, 0);
    }

    .classes-cell:hover .classes-tooltip {
        opacity: 1;
        visibility: visible;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        display: inline-block;
    }

    .status-approved {
        background-color: #d1f2eb;
        color: #0a6847;
    }

    .status-ongoing {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-archived {
        background-color: #e2e3e5;
        color: #6c757d;
    }
</style>

<div id="mainContent" class="exam-content">
    <div class="exams-header">
        <h1 class="page-title">Your Exams</h1>
    </div>

    <div class="table-container">
        <table class="exams-table">
            <thead>
                <tr>
                    <th>EXAM NAME</th>
                    <th>SUBJECT</th>
                    <th>CLASSES</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $exam)
                <tr>
                    <td>{{ $exam->exam_title }}</td>
                    <td>{{ $exam->subject->subject_name }}</td>
                    <td class="classes-cell" data-classes="{{ $exam->classes_list }}">
                        {{ $exam->classes_count }} {{ Str::plural('class', $exam->classes_count) }}
                        @if($exam->classes_count > 0)
                        <div class="classes-tooltip">{{ $exam->classes_list }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge status-{{ strtolower($exam->status) }}">
                            {{ $exam->status_display }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('instructor.exam-statistics.show', $exam->exam_id) }}" class="action-btn">
                            <i class="bi bi-bar-chart-fill"></i>
                            View Stats
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">No approved exams found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.getElementById('mainContent');
    
    if (sidebar) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (sidebar.classList.contains('expanded')) {
                        mainContent.classList.add('expanded');
                    } else {
                        mainContent.classList.remove('expanded');
                    }
                }
            });
        });
        
        observer.observe(sidebar, { attributes: true });
        
        if (sidebar.classList.contains('expanded')) {
            mainContent.classList.add('expanded');
        }
    }

    // Position tooltips dynamically to follow mouse cursor
    const classCells = document.querySelectorAll('.classes-cell');
    classCells.forEach(cell => {
        const tooltip = cell.querySelector('.classes-tooltip');
        if (tooltip) {
            cell.addEventListener('mouseenter', function(e) {
                tooltip.style.left = e.pageX + 'px';
                tooltip.style.top = (e.pageY - tooltip.offsetHeight - 10) + 'px';
            });
            
            cell.addEventListener('mousemove', function(e) {
                tooltip.style.left = e.pageX + 'px';
                tooltip.style.top = (e.pageY - tooltip.offsetHeight - 10) + 'px';
            });
        }
    });
});
@endsection
