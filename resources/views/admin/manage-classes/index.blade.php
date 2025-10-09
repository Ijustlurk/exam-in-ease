@extends('layouts.Admin.app')

@section('content')
<style>
    .classes-container {
        background-color: #e8f1f5;
        min-height: 100vh;
        padding: 30px;
    }

    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 30px;
    }

    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
    }

    .show-archived-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .show-archived-wrapper input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .show-archived-wrapper label {
        margin: 0;
        font-size: 16px;
        color: #333;
        cursor: pointer;
        user-select: none;
    }

    .search-wrapper {
        flex: 0 0 500px;
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

    .btn-add-class {
        background-color: #6ba5b3;
        color: white;
        padding: 12px 30px;
        border-radius: 10px;
        border: none;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: background-color 0.2s;
        white-space: nowrap;
    }

    .btn-add-class:hover {
        background-color: #5a8f9c;
    }

    .table-container {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .classes-table {
        width: 100%;
        margin: 0;
    }

    .classes-table thead {
        background-color: #d4e5ea;
    }

    .classes-table thead th {
        padding: 18px 20px;
        font-weight: 600;
        font-size: 14px;
        color: #1a1a1a;
        text-transform: uppercase;
        border: none;
        letter-spacing: 0.5px;
    }

    .classes-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
    }

    .classes-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .classes-table tbody tr.archived {
        background-color: #f5f5f5;
        opacity: 0.7;
    }

    .classes-table tbody tr.archived td {
        color: #999;
    }

    .classes-table tbody td {
        padding: 20px;
        font-size: 15px;
        color: #333;
        vertical-align: middle;
        border: none;
    }

    .class-name {
        font-weight: 600;
        color: #1a1a1a;
    }

    .actions-cell {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .action-btn {
        background: none;
        border: none;
        color: #999;
        font-size: 14px;
        cursor: pointer;
        padding: 5px 10px;
        transition: color 0.2s;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .action-btn:hover {
        color: #333;
    }

    .action-btn i {
        font-size: 16px;
    }

    .no-data-row {
        text-align: center;
        padding: 40px;
        color: #999;
        font-style: italic;
    }
</style>

<div class="classes-container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-left">
            <h1 class="page-title">All Classes</h1>
            <div class="show-archived-wrapper">
                <input type="checkbox" id="showArchived" name="showArchived">
                <label for="showArchived">Show Archived</label>
            </div>
        </div>

        <div class="search-wrapper">
            <input type="text" class="search-input" id="searchInput" placeholder="Search for Classes">
            <i class="fas fa-search search-icon"></i>
        </div>

        <button class="btn-add-class" onclick="openNewClassModal()">
            <i class="fas fa-plus"></i>
            Add Class
        </button>
    </div>

    <!-- Table Section -->
    <div class="table-container">
        <table class="classes-table">
            <thead>
                <tr>
                    <th>NAME</th>
                    <th>TEACHER</th>
                    <th>SUBJECT</th>
                    <th>NO. OF STUDENTS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody id="classesTableBody">
                @forelse($classes as $class)
                <tr class="{{ $class->status == 'Archived' ? 'archived' : '' }}" data-status="{{ $class->status }}">
                    <td class="class-name">{{ $class->title }}</td>
                    <td>{{ $class->teacher ? $class->teacher->full_name : 'No Teacher Assigned' }}</td>
                    <td>{{ $class->subject->subject_name }}</td>
                    <td>{{ $class->students->count() }}</td>
                    <td>
                        <div class="actions-cell">
                            <button class="action-btn" onclick="editClass({{ $class->class_id }})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="action-btn" onclick="manageStudents({{ $class->class_id }})">
                                <i class="fas fa-users"></i> Manage Students
                            </button>
                            @if($class->status == 'Active')
                            <button class="action-btn" onclick="archiveClass({{ $class->class_id }})">
                                <i class="fas fa-archive"></i> Archive
                            </button>
                            @else
                            <button class="action-btn" onclick="unarchiveClass({{ $class->class_id }})">
                                <i class="fas fa-box-open"></i> Unarchive
                            </button>
                            @endif
                            <button class="action-btn" onclick="deleteClass({{ $class->class_id }})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="no-data-row">No classes found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- New Class Modal -->
    <div class="modal fade" id="newClassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header" style="background-color: #6ba5b3; color: white; border-radius: 15px 15px 0 0; padding: 20px 30px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <i class="fas fa-school" style="font-size: 28px;"></i>
                        <h5 class="modal-title" style="font-size: 24px; font-weight: 600; margin: 0;">New Class</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 30px; background-color: #f8f9fa;">
                    <form id="newClassForm">
                        @csrf
                        <!-- Class Name -->
                        <div class="mb-4">
                            <label class="form-label" style="font-weight: 600; color: #333; margin-bottom: 10px;">Class Name</label>
                            <div style="position: relative;">
                                <input type="text" class="form-control" name="title" placeholder="1F Computer Programming" required style="padding: 12px 45px 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                                <i class="fas fa-edit" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Subject -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label" style="font-weight: 600; color: #333; margin-bottom: 10px;">Subject</label>
                                <select class="form-select" name="subject_id" required style="padding: 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                                    <option value="">Select one</option>
                                    @foreach($subjects as $subject)
                                    <option value="{{ $subject->subject_id }}">{{ $subject->subject_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Academic Year -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label" style="font-weight: 600; color: #333; margin-bottom: 10px;">Academic Year</label>
                                <input type="text" class="form-control" name="school_year" placeholder="2024-2025" required style="padding: 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                            </div>

                            <!-- Semester -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label" style="font-weight: 600; color: #333; margin-bottom: 10px;">Semester</label>
                                <select class="form-select" name="semester" required style="padding: 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                                    <option value="">Select</option>
                                    <option value="1">1st Semester</option>
                                    <option value="2">2nd Semester</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Section -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label" style="font-weight: 600; color: #333; margin-bottom: 10px;">Section</label>
                                <input type="text" class="form-control" name="section" placeholder="A" required style="padding: 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                            </div>

                            <!-- Year Level -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label" style="font-weight: 600; color: #333; margin-bottom: 10px;">Year Level</label>
                                <select class="form-select" name="year_level" required style="padding: 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                                    <option value="">Select</option>
                                    <option value="1">1st Year</option>
                                    <option value="2">2nd Year</option>
                                    <option value="3">3rd Year</option>
                                    <option value="4">4th Year</option>
                                </select>
                            </div>

                            <!-- Teacher -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label" style="font-weight: 600; color: #333; margin-bottom: 10px;">Teacher</label>
                                <select class="form-select" name="teacher_id" required style="padding: 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                                    <option value="">Select teacher</option>
                                    @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->user_id }}">{{ $teacher->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn" style="background-color: #6ba5b3; color: white; padding: 12px 40px; border-radius: 10px; font-size: 16px; font-weight: 600; border: none;">
                                Create Class
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Modal (Success) -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header" style="background-color: #6ba5b3; color: white; border-radius: 15px 15px 0 0; padding: 20px 30px;">
                    <h5 class="modal-title" style="font-size: 24px; font-weight: 600; margin: 0;">Information</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 40px 30px; background-color: white;">
                    <p style="font-size: 24px; font-weight: 400; color: #1a1a1a; margin-bottom: 20px;">Class successfully created.</p>
                    <p style="font-size: 24px; font-weight: 400; color: #1a1a1a; margin-bottom: 30px;">Add students now?</p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn" onclick="skipForNow()" style="background-color: #6ba5b3; color: white; padding: 12px 35px; border-radius: 10px; font-size: 16px; font-weight: 600; border: none; min-width: 150px;">
                            Skip for Now
                        </button>
                        <button type="button" class="btn" onclick="addStudents()" style="background-color: #6ba5b3; color: white; padding: 12px 35px; border-radius: 10px; font-size: 16px; font-weight: 600; border: none; min-width: 150px;">
                            Add Students
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let createdClassId = null;

    // Modal Functions
    function openNewClassModal() {
        const modal = new bootstrap.Modal(document.getElementById('newClassModal'));
        modal.show();
    }

    // Handle form submission
    document.getElementById('newClassForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("admin.manage-classes.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                createdClassId = data.class_id;
                
                // Close the new class modal
                const newClassModal = bootstrap.Modal.getInstance(document.getElementById('newClassModal'));
                newClassModal.hide();
                
                // Reset form
                document.getElementById('newClassForm').reset();
                
                // Show success modal
                setTimeout(() => {
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                }, 300);
            } else {
                alert('Error creating class: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the class');
        });
    });

    function skipForNow() {
        const successModal = bootstrap.Modal.getInstance(document.getElementById('successModal'));
        successModal.hide();
        
        setTimeout(() => {
            location.reload();
        }, 300);
    }

    function addStudents() {
        const successModal = bootstrap.Modal.getInstance(document.getElementById('successModal'));
        successModal.hide();
        
        setTimeout(() => {
            window.location.href = `/admin/manage-classes/${createdClassId}/students`;
        }, 300);
    }

    // Action Functions
    function editClass(id) {
        window.location.href = `/admin/manage-classes/${id}/edit`;
    }

    function manageStudents(id) {
        window.location.href = `/admin/manage-classes/${id}/students`;
    }

    function archiveClass(id) {
        if (confirm('Are you sure you want to archive this class?')) {
            fetch(`/admin/manage-classes/${id}/archive`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }
    }

    function unarchiveClass(id) {
        if (confirm('Are you sure you want to unarchive this class?')) {
            fetch(`/admin/manage-classes/${id}/unarchive`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }
    }

    function deleteClass(id) {
        if (confirm('Are you sure you want to delete this class? This action cannot be undone.')) {
            fetch(`/admin/manage-classes/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }
    }

    // Show/Hide Archived
    document.getElementById('showArchived').addEventListener('change', function() {
        const archivedRows = document.querySelectorAll('tr[data-status="Archived"]');
        archivedRows.forEach(row => {
            row.style.display = this.checked ? '' : 'none';
        });
    });

    // Initially hide archived rows
    document.addEventListener('DOMContentLoaded', function() {
        const archivedRows = document.querySelectorAll('tr[data-status="Archived"]');
        archivedRows.forEach(row => {
            row.style.display = 'none';
        });
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#classesTableBody tr');
        
        rows.forEach(row => {
            if (row.querySelector('.no-data-row')) return;
            
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endsection