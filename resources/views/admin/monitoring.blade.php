@can('admin-access')
    @extends('layouts.Admin.app') 

    @section('content')
        <style>
            /* Base and Layout Styles */
            .main {
                margin-left: 60px;
                transition: margin-left 0.3s;
                padding: 2rem; 
            }

            /* Student Monitoring Card */
            .student-monitoring-card {
                background-color: #fff;
                border-radius: 12px; 
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                overflow: hidden; 
                border: 1px solid #e0e0e0;
            }

            /* Card Header */
            .card-header-title {
                padding: 1.5rem 2rem;
                font-size: 1.5rem;
                font-weight: 600;
                color: #2c3e50;
                border-bottom: 1px solid #e0e0e0;
                background-color: #fff;
            }

            /* Table Container */
            .table-wrapper {
                width: 100%;
            }

            /* Table Styles */
            .student-table {
                width: 100%;
                border-collapse: collapse;
            }

            /* Table Header */
            .student-table thead th {
                background-color: #d9e8f0 !important;
                color: #2c3e50;
                text-align: left;
                font-weight: 600;
                padding: 1rem 1.5rem;
                cursor: pointer;
                border-bottom: 1px solid #c5d9e3; 
                font-size: 0.875rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .student-table thead th:first-child {
                padding-left: 2rem;
            }

            .student-table thead i {
                font-size: 0.7rem;
                vertical-align: middle;
                margin-left: 0.25rem;
            }

            .student-table thead input[type="checkbox"] {
                width: 18px;
                height: 18px;
                cursor: pointer;
                accent-color: #667eea;
            }

            /* Table Body Rows */
            .student-table tbody tr {
                background-color: #fff;
                border-bottom: 1px solid #e8ecef;
                transition: background-color 0.2s;
            }

            .student-table tbody tr:hover {
                background-color: #f8f9fa;
            }

            .student-table tbody tr:last-child {
                border-bottom: none;
            }

            .student-table td {
                padding: 1.25rem 1.5rem;
                vertical-align: middle;
                font-size: 0.95rem;
                color: #2c3e50;
            }

            .student-table td:first-child {
                padding-left: 2rem;
            }

            /* Checkbox Styling */
            .student-checkbox {
                width: 18px;
                height: 18px;
                cursor: pointer;
                accent-color: #667eea;
            }

            /* Student Info */
            .student-info {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-weight: 500;
                color: #2c3e50;
            }

            .student-icon {
                width: 36px;
                height: 36px;
                background-color: #2c3e50;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .student-icon i {
                font-size: 1.3rem;
                color: #fff;
            }

            .student-name {
                font-size: 0.95rem;
                font-weight: 500;
            }
            
            /* Status Badges */
            .status-badge {
                font-weight: 600;
                font-size: 0.9rem;
                display: inline-block;
            }

            .status-suspicious { 
                color: #f39c12;
            }
            
            .status-active { 
                color: #3498db;
            }
            
            .status-submitted { 
                color: #27ae60;
            }

            /* Action Buttons */
            .action-buttons {
                display: flex;
                gap: 2rem;
                align-items: center;
            }

            .action-btn {
                background: none;
                border: none;
                cursor: pointer;
                padding: 0;
                color: #95a5a6;
                display: flex;
                align-items: center;
                gap: 0.4rem;
                font-size: 0.9rem;
                font-weight: 500;
                transition: color 0.2s;
            }

            .action-btn:hover {
                color: #667eea;
            }

            .action-btn.flagged {
                color: #e74c3c;
            }

            .action-btn i {
                font-size: 1rem;
            }

            /* Empty State */
            .empty-state {
                text-align: center;
                padding: 4rem 2rem;
                color: #95a5a6;
            }

            .empty-state i {
                font-size: 3rem;
                margin-bottom: 1rem;
                opacity: 0.3;
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .student-table {
                    font-size: 0.85rem;
                }
                
                .action-buttons {
                    gap: 1rem;
                }
            }

            @media (max-width: 768px) {
                .main {
                    margin-left: 0;
                    padding: 1rem;
                }

                .student-table thead th,
                .student-table td {
                    padding: 0.75rem;
                }

                .card-header-title {
                    padding: 1rem;
                    font-size: 1.2rem;
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
            <div class="student-monitoring-card">
                
                {{-- Card Header --}}
                <div class="card-header-title">
                    @if(isset($class) && $class)
                        {{ $class->title }}
                    @else
                        Computer Programming 1
                    @endif
                </div>

                <div class="table-wrapper">
                    <table class="student-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">
                                    <input type="checkbox" id="selectAllStudents">
                                </th>
                                <th style="width: 35%;">
                                    NAME <i class="bi bi-caret-down-fill"></i>
                                </th>
                                <th style="width: 25%;">
                                    CLASS <i class="bi bi-caret-down-fill"></i>
                                </th>
                                <th style="width: 15%;">
                                    STATUS <i class="bi bi-caret-down-fill"></i>
                                </th>
                                <th style="width: 20%;">
                                    ACTIONS
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students ?? [] as $student)
                                @php
                                    /** @var object $student */
                                    $studentStatus = property_exists($student, 'status') ? $student->status : 'active';
                                    $lowerStatus = strtolower(trim($studentStatus));
                                    $statusClass = match ($lowerStatus) {
                                        'suspicious', 'in progress' => 'status-suspicious', 
                                        'active' => 'status-active',         
                                        'submitted' => 'status-submitted',   
                                        default => 'status-active'
                                    };
                                    $displayStatus = ucfirst($lowerStatus);
                                    $studentId = property_exists($student, 'id') ? $student->id : 0;
                                    $studentName = property_exists($student, 'name') ? $student->name : 'Student Name';
                                    $studentClass = property_exists($student, 'class_name') ? $student->class_name : '1A - Comp Prog';
                                @endphp
                                <tr data-student-id="{{ $studentId }}">
                                    <td>
                                        <input type="checkbox" class="student-checkbox" value="{{ $studentId }}">
                                    </td>
                                    <td>
                                        <div class="student-info">
                                            <div class="student-icon">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <span class="student-name">{{ $studentName }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $studentClass }}</td>
                                    <td>
                                        <span class="status-badge {{ $statusClass }}">{{ $displayStatus }}</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn flag-btn" data-student-id="{{ $studentId }}" title="Flag Student">
                                                <i class="bi bi-flag"></i> Flag
                                            </button>
                                            <button class="action-btn stop-btn" data-student-id="{{ $studentId }}" title="Stop Monitoring">
                                                <i class="bi bi-trash"></i> Stop
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox"></i>
                                            <p>No students found for this exam.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Select All Checkbox functionality
                const selectAll = document.getElementById('selectAllStudents');
                const studentCheckboxes = document.querySelectorAll('.student-checkbox');

                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        studentCheckboxes.forEach(checkbox => {
                            checkbox.checked = selectAll.checked;
                        });
                    });

                    // Update Select All state when individual checkboxes change
                    studentCheckboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
                            const someChecked = Array.from(studentCheckboxes).some(cb => cb.checked);
                            selectAll.checked = allChecked;
                            selectAll.indeterminate = someChecked && !allChecked;
                        });
                    });
                }

                // Flag Button functionality
                const flagButtons = document.querySelectorAll('.flag-btn');
                flagButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const studentId = this.getAttribute('data-student-id');
                        const isFlagged = this.classList.contains('flagged');

                        if (isFlagged) {
                            // Unflag student
                            this.classList.remove('flagged');
                            this.innerHTML = '<i class="bi bi-flag"></i> Flag';
                            console.log(`Unflagged student ID: ${studentId}`);
                            
                            // AJAX call to unflag student
                            // fetch(`/admin/students/${studentId}/unflag`, {
                            //     method: 'POST',
                            //     headers: {
                            //         'Content-Type': 'application/json',
                            //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            //     }
                            // });
                        } else {
                            // Flag student
                            this.classList.add('flagged');
                            this.innerHTML = '<i class="bi bi-flag-fill"></i> Flagged';
                            console.log(`Flagged student ID: ${studentId}`);
                            
                            // AJAX call to flag student
                            // fetch(`/admin/students/${studentId}/flag`, {
                            //     method: 'POST',
                            //     headers: {
                            //         'Content-Type': 'application/json',
                            //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            //     }
                            // });
                        }
                    });
                });

                // Stop Button functionality
                const stopButtons = document.querySelectorAll('.stop-btn');
                stopButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const studentId = this.getAttribute('data-student-id');
                        const studentRow = this.closest('tr');
                        const studentName = studentRow.querySelector('.student-name').textContent;

                        if (confirm(`Are you sure you want to stop monitoring for "${studentName}"?`)) {
                            console.log(`Stopping monitoring for student ID: ${studentId}`);
                            
                            // Remove the row with animation
                            studentRow.style.transition = 'opacity 0.3s';
                            studentRow.style.opacity = '0';
                            
                            setTimeout(() => {
                                studentRow.remove();
                                
                                // Check if table is empty
                                const remainingRows = document.querySelectorAll('.student-table tbody tr');
                                if (remainingRows.length === 0) {
                                    const tbody = document.querySelector('.student-table tbody');
                                    tbody.innerHTML = `
                                        <tr>
                                            <td colspan="5">
                                                <div class="empty-state">
                                                    <i class="bi bi-inbox"></i>
                                                    <p>No students found for this exam.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                                }
                            }, 300);

                            // AJAX call to stop monitoring
                            // fetch(`/admin/students/${studentId}/stop-monitoring`, {
                            //     method: 'POST',
                            //     headers: {
                            //         'Content-Type': 'application/json',
                            //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            //     }
                            // }).then(response => response.json())
                            //   .then(data => {
                            //       if (data.success) {
                            //           // Row already removed above
                            //       }
                            //   });
                        }
                    });
                });

                // Optional: Sort functionality for table headers
                const sortableHeaders = document.querySelectorAll('.student-table thead th[style*="width"]');
                sortableHeaders.forEach((header, index) => {
                    if (index > 0 && index < 4) { // Skip checkbox and actions columns
                        header.style.cursor = 'pointer';
                        header.addEventListener('click', function() {
                            console.log(`Sorting by column: ${this.textContent.trim()}`);
                            // Implement sorting logic here
                        });
                    }
                });
            });
        </script>
    @endsection
@endcan