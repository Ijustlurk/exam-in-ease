@can('admin-access')
    @extends('layouts.Admin.app') {{-- Siguraduhin na ito ang tamang layout --}}

    @section('content')
        <style>
            /* Base styles for the main content area */
            .main {
                margin-left: 60px; /* Adjust based on your sidebar width */
                transition: margin-left 0.3s;
                padding: 2rem;
            }
            .main.expanded {
                margin-left: 220px; /* Adjust if sidebar expands */
            }

            /* Container for the whole section */
            .approval-dashboard-container {
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                padding: 1.5rem;
                margin-bottom: 2rem;
            }

            /* Header Section: Title and Search Bar */
            .header-section {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
                flex-wrap: wrap; /* Para mag-adjust sa maliliit na screen */
                gap: 1rem; /* Space between items if wrapped */
            }
            .header-section h2 {
                margin: 0;
                font-weight: 600;
                color: #333;
                font-size: 1.8rem;
            }
            .search-bar {
                display: flex;
                flex-grow: 1; /* Allow search bar to take available space */
                max-width: 400px; /* Limit search bar width */
                border: 1px solid #ced4da;
                border-radius: 0.25rem;
                overflow: hidden; /* Ensure button stays inside border */
            }
            .search-bar input {
                border: none;
                padding: 0.5rem 1rem;
                flex-grow: 1;
                outline: none;
            }
            .search-bar button {
                background-color: #f8f9fa;
                border: none;
                padding: 0.5rem 1rem;
                cursor: pointer;
                color: #495057;
                transition: background-color 0.2s;
            }
            .search-bar button:hover {
                background-color: #e2e6ea;
            }

            /* Table specific styles */
            .exams-table {
                width: 100%;
                border-collapse: collapse;
            }
            .exams-table th,
            .exams-table td {
                padding: 12px 15px;
                border-bottom: 1px solid #e0e0e0;
                vertical-align: middle;
            }
            .exams-table thead th {
                background-color: #f8f9fa;
                color: #495057;
                text-align: left;
                font-weight: 600;
                cursor: pointer; /* Indicate sortable columns */
                position: sticky;
                top: 0;
                z-index: 1;
            }
            .exams-table tbody tr:hover {
                background-color: #f2f2f2;
            }

            /* Exam Name Column */
            .exam-info .title {
                font-weight: 500;
                color: #333;
            }
            .exam-info .author {
                font-size: 0.85em;
                color: #6c757d;
                margin-top: 2px;
            }

            /* Approval Status Badges/Spans */
            .status-badge {
                padding: 5px 10px;
                border-radius: 5px;
                font-size: 0.85em;
                font-weight: 600;
                display: inline-block;
            }
            .status-pending { background-color: #ffeeba; color: #856404; } /* yellow */
            .status-approved { background-color: #d4edda; color: #155724; } /* green */
            .status-rejected { background-color: #f8d7da; color: #721c24; } /* red (if applicable) */

            /* Actions Column */
            .action-buttons button {
                background: none;
                border: none;
                color: #6c757d;
                font-size: 1.1em;
                padding: 5px 8px;
                cursor: pointer;
                transition: color 0.2s ease;
            }
            .action-buttons button:hover {
                color: #007bff;
            }
            .action-buttons button.text-success:hover {
                color: #28a745;
            }
            .action-buttons button.text-danger:hover {
                color: #dc3545;
            }
            .action-buttons .btn-icon-text {
                display: inline-flex; /* Use flexbox for icon and text alignment */
                align-items: center;
                gap: 5px; /* Space between icon and text */
            }

            /* Pagination Styles */
            .pagination-container {
                display: flex;
                justify-content: flex-end; /* Align to the right */
                align-items: center;
                padding-top: 1rem;
                margin-top: 1rem;
            }
            .pagination-container .pagination {
                margin: 0;
                display: flex;
                list-style: none;
                padding: 0;
            }
            .pagination-container .page-item {
                margin: 0 5px;
            }
            .pagination-container .page-link {
                display: block;
                padding: 8px 12px;
                border: 1px solid #dee2e6;
                border-radius: 5px;
                color: #007bff;
                text-decoration: none;
                transition: background-color 0.2s, color 0.2s;
            }
            .pagination-container .page-link:hover {
                background-color: #e9ecef;
                color: #0056b3;
            }
            .pagination-container .page-item.active .page-link {
                background-color: #007bff;
                color: #fff;
                border-color: #007bff;
            }
            .pagination-container .page-item.disabled .page-link {
                color: #6c757d;
                pointer-events: none;
                background-color: #fff;
            }
        </style>

        <div id="mainContent" class="main">
            <div class="approval-dashboard-container">
                <div class="header-section">
                    <h2>Exams for Approval</h2>
                    <div class="search-bar">
                        <input type="text" placeholder="Search for exams" id="examSearchInput">
                        <button type="button"><i class="bi bi-search"></i></button>
                    </div>
                </div>

                <table class="exams-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAllExams"></th>
                            <th>EXAM NAME <i class="bi bi-caret-down-fill ms-1"></i></th>
                            <th>SUBJECT <i class="bi bi-caret-down-fill ms-1"></i></th>
                            <th>APPROVAL STATUS <i class="bi bi-caret-down-fill ms-1"></i></th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($exams as $exam)
                            <tr>
                                <td><input type="checkbox" class="exam-checkbox" value="{{ $exam->exam_id }}"></td>
                                <td>
                                    <div class="exam-info">
                                        <div class="title">{{ $exam->exam_title }}</div>
                                        <div class="author">{{ $exam->author_names ?? 'Author Name, 1 other' }}</div>
                                    </div>
                                </td>
                                <td>{{ $exam->subject_name }}</td>
                                <td>
                                    @php
                                        $statusClass = '';
                                        switch (strtolower($exam->approval_status)) {
                                            case 'pending':
                                                $statusClass = 'status-pending';
                                                break;
                                            case 'approved':
                                                $statusClass = 'status-approved';
                                                break;
                                            case 'rejected': // Add 'rejected' if applicable
                                                $statusClass = 'status-rejected';
                                                break;
                                            default:
                                                $statusClass = '';
                                                break;
                                        }
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">{{ ucfirst($exam->approval_status) }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if (strtolower($exam->approval_status) === 'pending')
                                            <button title="Approve Exam" class="text-success btn-icon-text"><i class="bi bi-check2-circle"></i> Approve</button>
                                            <button title="Revise Exam" class="text-danger btn-icon-text"><i class="bi bi-x-circle"></i> Revise</button>
                                        @elseif (strtolower($exam->approval_status) === 'approved')
                                            <button title="Rescind Approval" class="btn-icon-text"><i class="bi bi-arrow-counterclockwise"></i> Rescind</button>
                                            <button title="Edit Settings" class="btn-icon-text"><i class="bi bi-pencil-square"></i> Edit Settings</button>
                                        @endif
                                        <a href="{{ route('admin.monitoring', ['id' => $exam->exam_id]) }}" title="View Exam" class="btn-icon-text" style="color: #6c757d; text-decoration: none;"><i class="bi bi-eye"></i> View</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No exams for approval found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination Links --}}
                <div class="pagination-container">
                    {{ $exams->links('vendor.pagination.simple-bootstrap-4') }} {{-- You might need to adjust the pagination view path --}}
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Select All Checkbox Logic
                const selectAll = document.getElementById('selectAllExams');
                const examCheckboxes = document.querySelectorAll('.exam-checkbox');

                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        examCheckboxes.forEach(checkbox => {
                            checkbox.checked = selectAll.checked;
                        });
                    });
                }

                examCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (!this.checked) {
                            if (selectAll) selectAll.checked = false;
                        } else {
                            const allChecked = Array.from(examCheckboxes).every(cb => cb.checked);
                            if (selectAll) selectAll.checked = allChecked;
                        }
                    });
                });

                // Basic Search Functionality (Client-side, for demo. For real use, use AJAX/server-side search)
                const searchInput = document.getElementById('examSearchInput');
                searchInput.addEventListener('keyup', function() {
                    const filter = searchInput.value.toLowerCase();
                    const table = document.querySelector('.exams-table tbody');
                    const rows = table.querySelectorAll('tr');

                    rows.forEach(row => {
                        const examName = row.querySelector('.exam-info .title')?.textContent.toLowerCase() || '';
                        const subjectName = row.children[2]?.textContent.toLowerCase() || ''; // Subject column

                        if (examName.includes(filter) || subjectName.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });

                // Optional: Add logic for Approve, Revise, Rescind, Edit Settings buttons (e.g., AJAX calls)
                // Example:
                document.querySelectorAll('.action-buttons button').forEach(button => {
                    button.addEventListener('click', function() {
                        const examId = this.closest('tr').querySelector('.exam-checkbox').value;
                        const action = this.textContent.trim();
                        alert(`Action: ${action} for Exam ID: ${examId}`);
                        // Implement AJAX call or form submission here
                    });
                });

                 // Ensure Bootstrap Icons (bi-*) are loaded if used
                 // <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
            });
        </script>
    @endsection
@endcan