@can('admin-access')
@extends('layouts.Admin.app')

@section('content')
<style>
    .exams-container {
      
        min-height: 100vh;
        padding: 30px;
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
        flex: 0 0 800px;
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 12px 45px 12px 20px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 15px;
        font-style: italic;
        color: #9ca3af;
        background-color: white;
    }

    .search-input:focus {
        outline: none;
        border-color: #6ba5b3;
        color: #333;
    }

    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 18px;
    }

    .table-container {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .exams-table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
    }

    .exams-table thead {
        background-color: #d4e5ea;
    }

    .exams-table thead th {
        padding: 18px 20px;
        font-weight: 600;
        font-size: 14px;
        color: #1a1a1a;
        text-transform: uppercase;
        border: none;
        letter-spacing: 0.5px;
        text-align: left;
    }

    .exams-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
    }

    .exams-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .exams-table tbody td {
        padding: 20px;
        font-size: 15px;
        color: #333;
        vertical-align: middle;
        border: none;
    }

    .exam-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #6ba5b3;
    }

    .exam-name {
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 5px;
    }

    .teacher-name {
        font-size: 13px;
        color: #95a5a6;
        font-style: italic;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .status-ongoing {
        background-color: #fff3cd;
        color: #ff9800;
    }

    .status-archived {
        background-color: #d4edda;
        color: #27ae60;
    }

    .status-draft {
        background-color: #e8ecef;
        color: #95a5a6;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #ff9800;
    }

    .actions-cell {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .action-btn {
        background: none;
        border: none;
        color: #95a5a6;
        cursor: pointer;
        padding: 5px 10px;
        transition: color 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
    }

    .action-btn:hover {
        color: #6ba5b3;
    }

    .action-btn i {
        font-size: 18px;
    }

    .no-data-row {
        text-align: center;
        padding: 40px;
        color: #999;
        font-style: italic;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        padding: 20px;
        background: white;
        border-radius: 0 0 15px 15px;
    }

    .pagination-btn {
        padding: 8px 16px;
        border: 1px solid #d1d5db;
        background: white;
        color: #333;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
    }

    .pagination-btn:hover:not(:disabled) {
        background-color: #6ba5b3;
        color: white;
        border-color: #6ba5b3;
    }

    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-btn.active {
        background-color: #6ba5b3;
        color: white;
        border-color: #6ba5b3;
    }
</style>

<div id="mainContent" class="main exams-container">
    <!-- Header Section -->
    <div class="exams-header">
        <h1 class="page-title">Exams</h1>

        <div class="search-wrapper">
            <input type="text" class="search-input" id="searchInput" placeholder="Search for exams">
            <i class="fas fa-search search-icon"></i>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-container">
        <table class="exams-table">
            <thead>
                <tr>
                    <th style="width: 3%;">
                        <input type="checkbox" id="selectAllExams" class="exam-checkbox">
                    </th>
                    <th style="width: 30%;">EXAM NAME</th>
                    <th style="width: 25%;">SUBJECT</th>
                    <th style="width: 20%;">CLASSES</th>
                    <th style="width: 12%;">EXAM STATUS</th>
                    <th style="width: 10%;">ACTIONS</th>
                </tr>
            </thead>
            <tbody id="examsTableBody">
                @forelse($exams as $exam)
                <tr>
                    <td>
                        <input type="checkbox" class="exam-checkbox" value="{{ $exam->exam_id }}">
                    </td>
                    <td>
                        <div class="exam-name">{{ $exam->exam_title }}</div>
                        <div class="teacher-name">{{ $exam->teacher_name }}</div>
                    </td>
                    <td>{{ $exam->subject->subject_name }}</td>
                    <td>{{ $exam->classes_display ?: 'No classes assigned' }}</td>
                    <td>
                        <span class="status-badge status-{{ strtolower($exam->status_display) }}">
                            {{ $exam->status_display }}
                        </span>
                    </td>
                    <td>
                        <div class="actions-cell">
                            <a href="{{ route('admin.exam-statistics.show', $exam->exam_id) }}" class="action-btn">
                                <i class="fas fa-search"></i>
                                <span>View</span>
                            </a>
                            @if($exam->status === 'archived')
                            <a href="{{ route('admin.exam-statistics.stats', $exam->exam_id) }}" class="action-btn">
                                <i class="fas fa-chart-bar"></i>
                                <span>See Stats</span>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="no-data-row">No exams found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($exams->hasPages())
        <div class="pagination-wrapper">
            {{-- Previous Button --}}
            @if ($exams->onFirstPage())
                <button class="pagination-btn" disabled>Prev</button>
            @else
                <a href="{{ $exams->previousPageUrl() }}" class="pagination-btn">Prev</a>
            @endif

            {{-- Page Numbers --}}
            @foreach(range(1, $exams->lastPage()) as $page)
                @if($page == $exams->currentPage())
                    <button class="pagination-btn active">{{ $page }}</button>
                @else
                    <a href="{{ $exams->url($page) }}" class="pagination-btn">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next Button --}}
            @if ($exams->hasMorePages())
                <a href="{{ $exams->nextPageUrl() }}" class="pagination-btn">Next</a>
            @else
                <button class="pagination-btn" disabled>Next</button>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
    // Select All Checkbox
    document.getElementById('selectAllExams')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.exam-checkbox:not(#selectAllExams)');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Search functionality
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#examsTableBody tr');
        
        rows.forEach(row => {
            if (row.querySelector('.no-data-row')) return;
            
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endsection
@endcan