@extends('layouts.Admin.app')

@section('content')
<style>
    .subjects-container {
        padding: 30px;
        height: calc(100vh - 60px);
        display: flex;
        flex-direction: column;
    }

    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-shrink: 0;
    }

    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
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

    .btn-add-subject {
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

    .btn-add-subject:hover {
        background-color: #5a8f9c;
    }

    .table-container {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .table-scroll-wrapper {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .table-scroll-wrapper::-webkit-scrollbar {
        width: 10px;
    }

    .table-scroll-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .table-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 5px;
    }

    .table-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .subjects-table {
        width: 100%;
        margin: 0;
    }

    .subjects-table thead {
        background-color: #d4e5ea;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .subjects-table thead th {
        padding: 18px 20px;
        font-weight: 600;
        font-size: 14px;
        color: #1a1a1a;
        text-transform: uppercase;
        border: none;
        letter-spacing: 0.5px;
    }

    .subjects-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
    }

    .subjects-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .subjects-table tbody td {
        padding: 20px;
        font-size: 15px;
        color: #333;
        vertical-align: middle;
        border: none;
    }

    .subject-name {
        font-weight: 600;
        color: #1a1a1a;
    }

    .subject-code {
        display: inline-block;
        background-color: #e0f2f7;
        color: #0891b2;
        padding: 4px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
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

    /* Modal Styles */
    .modal-header-custom {
        background-color: #6ba5b3;
        color: white;
        padding: 20px 30px;
        border-radius: 15px 15px 0 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .modal-header-custom i {
        font-size: 28px;
    }

    .modal-header-custom h5 {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
    }

    .modal-body-custom {
        padding: 30px;
        background-color: #f8f9fa;
    }

    .form-label-custom {
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }

    .form-control-custom {
        padding: 12px 15px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 15px;
    }

    .form-control-custom:focus {
        outline: none;
        border-color: #6ba5b3;
        box-shadow: 0 0 0 3px rgba(107, 165, 179, 0.1);
    }

    .btn-submit {
        background-color: #6ba5b3;
        color: white;
        padding: 12px 40px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .btn-submit:hover {
        background-color: #5a8f9c;
    }

    .error-message {
        color: #dc2626;
        font-size: 13px;
        margin-top: 5px;
    }
</style>

<div id="mainContent" class="main subjects-container">
    <!-- Header Section -->
    <div class="header-section">
        <h1 class="page-title">All Subjects</h1>

        <div class="search-wrapper">
            <input type="text" class="search-input" id="searchInput" placeholder="Search for Subjects">
            <i class="fas fa-search search-icon"></i>
        </div>

        <button class="btn-add-subject" onclick="openNewSubjectModal()">
            <i class="fas fa-plus"></i>
            Add Subject
        </button>
    </div>

    <!-- Table Section -->
    <div class="table-container">
        <div class="table-scroll-wrapper">
            <table class="subjects-table">
                <thead>
                    <tr>
                        <th>SUBJECT CODE</th>
                        <th>SUBJECT NAME</th>
                        <th>SEMESTER OFFERED</th>
                        <th>CLASSES USING</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="subjectsTableBody">
                @forelse($subjects as $subject)
                <tr>
                    <td><span class="subject-code">{{ $subject->subject_code }}</span></td>
                    <td class="subject-name">{{ $subject->subject_name }}</td>
                    <td>{{ $subject->semester }}</td>
                    <td>{{ $subject->classes_count }} class(es)</td>
                    <td>
                        <div class="actions-cell">
                            <button class="action-btn" onclick="editSubject({{ $subject->subject_id }})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="action-btn" onclick="deleteSubject({{ $subject->subject_id }}, '{{ $subject->subject_name }}', {{ $subject->classes_count }})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="no-data-row">No subjects found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <!-- New Subject Modal -->
    <div class="modal fade" id="newSubjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header-custom">
                    <i class="fas fa-book"></i>
                    <h5>New Subject</h5>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-custom">
                    <form id="newSubjectForm">
                        @csrf
                        <div class="row">
                            <!-- Subject Code -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label-custom">Subject Code</label>
                                <input type="text" 
                                       class="form-control form-control-custom" 
                                       name="subject_code" 
                                       id="new_subject_code"
                                       placeholder="Subject Code" 
                                       required 
                                       style="text-transform: uppercase;">
                                <div id="new_subject_code_error" class="error-message"></div>
                            </div>

                            <!-- Subject Name -->
                            <div class="col-md-8 mb-4">
                                <label class="form-label-custom">Subject Name</label>
                                <input type="text" 
                                       class="form-control form-control-custom" 
                                       name="subject_name" 
                                       id="new_subject_name"
                                       placeholder="Subject Name" 
                                       required>
                                <div id="new_subject_name_error" class="error-message"></div>
                            </div>

                            <!-- Semester -->
                            <div class="col-md-12 mb-4">
                                <label class="form-label-custom">Semester Offered</label>
                                <select class="form-control form-control-custom" 
                                        name="semester" 
                                        id="new_semester"
                                        required>
                                    <option value="">Select Semester</option>
                                    <option value="1st Semester">1st Semester</option>
                                    <option value="2nd Semester">2nd Semester</option>
                                </select>
                                <div id="new_semester_error" class="error-message"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn-submit">
                                Create Subject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Subject Modal -->
    <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header-custom">
                    <i class="fas fa-edit"></i>
                    <h5>Edit Subject</h5>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-custom">
                    <form id="editSubjectForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_subject_id" name="subject_id">
                        
                        <div class="row">
                            <!-- Subject Code -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label-custom">Subject Code</label>
                                <input type="text" 
                                       class="form-control form-control-custom" 
                                       name="subject_code" 
                                       id="edit_subject_code"
                                       placeholder="CS101" 
                                       required
                                       style="text-transform: uppercase;">
                                <div id="edit_subject_code_error" class="error-message"></div>
                            </div>

                            <!-- Subject Name -->
                            <div class="col-md-8 mb-4">
                                <label class="form-label-custom">Subject Name</label>
                                <input type="text" 
                                       class="form-control form-control-custom" 
                                       name="subject_name" 
                                       id="edit_subject_name"
                                       placeholder="Computer Programming 1" 
                                       required>
                                <div id="edit_subject_name_error" class="error-message"></div>
                            </div>

                            <!-- Semester -->
                            <div class="col-md-12 mb-4">
                                <label class="form-label-custom">Semester Offered</label>
                                <select class="form-control form-control-custom" 
                                        name="semester" 
                                        id="edit_semester"
                                        required>
                                    <option value="">Select Semester</option>
                                    <option value="1st Semester">1st Semester</option>
                                    <option value="2nd Semester">2nd Semester</option>
                                </select>
                                <div id="edit_semester_error" class="error-message"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn-submit">
                                Update Subject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Modal Functions
    function openNewSubjectModal() {
        // Clear form and errors
        document.getElementById('newSubjectForm').reset();
        clearErrors('new');
        
        const modal = new bootstrap.Modal(document.getElementById('newSubjectModal'));
        modal.show();
    }

    function openEditSubjectModal() {
        const modal = new bootstrap.Modal(document.getElementById('editSubjectModal'));
        modal.show();
    }

    // Clear error messages
    function clearErrors(prefix) {
        document.getElementById(`${prefix}_subject_code_error`).textContent = '';
        document.getElementById(`${prefix}_subject_name_error`).textContent = '';
        document.getElementById(`${prefix}_semester_error`).textContent = '';
    }

    // Display error messages
    function displayErrors(errors, prefix) {
        clearErrors(prefix);
        
        if (errors.subject_code) {
            document.getElementById(`${prefix}_subject_code_error`).textContent = errors.subject_code[0];
        }
        if (errors.subject_name) {
            document.getElementById(`${prefix}_subject_name_error`).textContent = errors.subject_name[0];
        }
        if (errors.semester) {
            document.getElementById(`${prefix}_semester_error`).textContent = errors.semester[0];
        }
    }

    // Handle new subject form submission
    document.getElementById('newSubjectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating...';
        
        fetch('{{ route("admin.manage-subject.store") }}', {
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
                alert(data.message);
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('newSubjectModal'));
                modal.hide();
                
                // Reload page
                location.reload();
            } else {
                if (data.errors) {
                    displayErrors(data.errors, 'new');
                } else {
                    alert('Error: ' + data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the subject');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Create Subject';
        });
    });

    // Edit subject
    function editSubject(id) {
        // Clear form and errors
        clearErrors('edit');
        
        // Fetch subject data
        fetch(`/admin/manage-subject/${id}`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('edit_subject_id').value = data.subject.subject_id;
                document.getElementById('edit_subject_code').value = data.subject.subject_code;
                document.getElementById('edit_subject_name').value = data.subject.subject_name;
                document.getElementById('edit_semester').value = data.subject.semester;
                
                openEditSubjectModal();
            } else {
                alert('Error loading subject data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading subject data');
        });
    }

    // Handle edit subject form submission
    document.getElementById('editSubjectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const subjectId = document.getElementById('edit_subject_id').value;
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Updating...';
        
        fetch(`/admin/manage-subject/${subjectId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'PUT'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editSubjectModal'));
                modal.hide();
                
                // Reload page
                location.reload();
            } else {
                if (data.errors) {
                    displayErrors(data.errors, 'edit');
                } else {
                    alert('Error: ' + data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the subject');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Update Subject';
        });
    });

    // Delete subject
    function deleteSubject(id, name, classCount) {
        if (classCount > 0) {
            alert(`Cannot delete "${name}". It is being used by ${classCount} class(es). Please remove or reassign those classes first.`);
            return;
        }
        
        if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
            fetch(`/admin/manage-subject/${id}`, {
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
                alert('An error occurred while deleting the subject');
            });
        }
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#subjectsTableBody tr');
        
        rows.forEach(row => {
            if (row.querySelector('.no-data-row')) return;
            
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Auto-uppercase subject code
    document.getElementById('new_subject_code').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });

    document.getElementById('edit_subject_code').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });
</script>
@endsection