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

        /* Student Management Modal Styles */
        .student-management-modal .modal-dialog {
            max-width: 900px;
        }

        .student-management-header {
            background-color: #6ba5b3;
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .student-management-header i {
            font-size: 24px;
        }

        .student-management-header h5 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .student-management-body {
            padding: 25px;
            background-color: #d7e9ed;
        }

        .student-panel {
            background: white;
            border-radius: 10px;
            padding: 20px;
            height: 550px;
            display: flex;
            flex-direction: column;
        }

        .student-panel-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .student-search {
            position: relative;
            margin-bottom: 15px;
        }

        .student-search input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            font-style: italic;
        }

        .student-search i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .student-table-container {
            flex: 1;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
        }

        .student-table thead {
            background-color: #f3f4f6;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .student-table thead th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            color: #1a1a1a;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }

        .student-table thead th.sortable {
            cursor: pointer;
            user-select: none;
        }

        .student-table thead th.sortable:hover {
            background-color: #e5e7eb;
        }

        .student-table thead th .sort-icon {
            margin-left: 5px;
            font-size: 12px;
        }

        .student-table tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }

        .student-table tbody tr:hover {
            background-color: #f9fafb;
        }

        .student-table tbody td {
            padding: 12px 15px;
            font-size: 14px;
            color: #333;
        }

        .student-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #6ba5b3;
        }

        .remove-student-btn {
            color: #ef4444;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.2s;
        }

        .remove-student-btn:hover {
            color: #dc2626;
        }

        .student-management-footer {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            padding: 20px 25px;
            background-color: #f8f9fa;
            border-radius: 0 0 10px 10px;
        }

        .btn-secondary-custom {
            background-color: #6ba5b3;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-secondary-custom:hover {
            background-color: #5a8f9c;
        }

        .btn-primary-custom {
            background-color: #6ba5b3;
            color: white;
            padding: 10px 30px;
            border-radius: 8px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-primary-custom:hover {
            background-color: #5a8f9c;
        }

        /* Copy Classes Modal */
        .copy-classes-modal .modal-dialog {
            max-width: 900px;
        }

        .copy-classes-header {
            background-color: #6ba5b3;
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }

        .copy-classes-header h5 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .copy-classes-body {
            padding: 25px;
            background-color: white;
            max-height: 600px;
            overflow-y: auto;
        }

        .class-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 12px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .class-item:hover {
            background-color: #f9fafb;
            border-color: #6ba5b3;
        }

        .class-item input[type="radio"] {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            cursor: pointer;
            accent-color: #6ba5b3;
        }

        .class-item-content {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .class-icon {
            width: 45px;
            height: 45px;
            background-color: #6ba5b3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .class-info {
            flex: 1;
        }

        .class-info-name {
            font-weight: 600;
            color: #1a1a1a;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .class-info-details {
            display: flex;
            gap: 20px;
            font-size: 14px;
            color: #6b7280;
        }

        .copy-classes-footer {
            display: flex;
            justify-content: flex-end;
            padding: 20px 25px;
            background-color: #f8f9fa;
            border-radius: 0 0 10px 10px;
        }

        .no-classes-message {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
            font-style: italic;
        }
    </style>

    <div id="mainContent" class="main classes-container">
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
                            <td>{{ $class->teacher ? $class->teacher->first_name . ' ' . $class->teacher->last_name : 'No Teacher Assigned' }}
                            </td>
                            <td>{{ $class->subject->subject_name }}</td>
                            <td>{{ $class->students->count() }}</td>
                            <td>
                                <div class="actions-cell">
                                    <button class="action-btn"
                                        onclick="manageStudents({{ $class->class_id }}, '{{ $class->title }}')">
                                        <i class="fas fa-users"></i> Manage Students
                                    </button>
                                    @if ($class->status == 'Active')
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
                    <div class="modal-header"
                        style="background-color: #6ba5b3; color: white; border-radius: 15px 15px 0 0; padding: 20px 30px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fas fa-school" style="font-size: 28px;"></i>
                            <h5 class="modal-title" style="font-size: 24px; font-weight: 600; margin: 0;">New Class</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="padding: 30px; background-color: #f8f9fa;">
                        <form id="newClassForm">
                            @csrf
                            <!-- Class Name -->
                            <div class="mb-4">
                                <label class="form-label" style="font-weight: 600; color: #333; margin-bottom: 10px;">Class
                                    Name</label>
                                <div style="position: relative;">
                                    <input type="text" class="form-control" name="title"
                                        placeholder="1F Computer Programming" required
                                        style="padding: 12px 45px 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                                    <i class="fas fa-edit"
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Subject -->
                                <div class="col-md-4 mb-4">
                                    <label class="form-label"
                                        style="font-weight: 600; color: #333; margin-bottom: 10px;">Subject</label>
                                    <select class="form-select" name="subject_id" required
                                        style="padding: 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                                        <option value="">Select one</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->subject_id }}">{{ $subject->subject_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Academic Year -->
                                <div class="col-md-4 mb-4">
                                    <label class="form-label"
                                        style="font-weight: 600; color: #333; margin-bottom: 10px;">Academic Year</label>
                                    <input type="text" class="form-control" name="school_year" placeholder="2024-2025"
                                        required
                                        style="padding: 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                                </div>

                                <!-- Semester -->
                                <div class="col-md-4 mb-4">
                                    <label class="form-label"
                                        style="font-weight: 600; color: #333; margin-bottom: 10px;">Semester</label>
                                    <select class="form-select" name="semester" required
                                        style="padding: 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                                        <option value="">Select</option>
                                        <option value="1">1st Semester</option>
                                        <option value="2">2nd Semester</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Section -->
                                <div class="col-md-4 mb-4">
                                    <label class="form-label"
                                        style="font-weight: 600; color: #333; margin-bottom: 10px;">Section</label>
                                    <input type="text" class="form-control" name="section" placeholder="A" required
                                        style="padding: 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                                </div>

                                <!-- Year Level -->
                                <div class="col-md-4 mb-4">
                                    <label class="form-label"
                                        style="font-weight: 600; color: #333; margin-bottom: 10px;">Year Level</label>
                                    <select class="form-select" name="year_level" required
                                        style="padding: 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                                        <option value="">Select</option>
                                        <option value="1">1st Year</option>
                                        <option value="2">2nd Year</option>
                                        <option value="3">3rd Year</option>
                                        <option value="4">4th Year</option>
                                    </select>
                                </div>

                                <!-- Teacher -->
                                <div class="col-md-4 mb-4">
                                    <label class="form-label"
                                        style="font-weight: 600; color: #333; margin-bottom: 10px;">Teacher</label>
                                    <select class="form-select" name="teacher_id" required
                                        style="padding: 12px 15px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px;">
                                        <option value="">Select teacher</option>
                                        @foreach ($teachers as $teacher)
                                            <option value="{{ $teacher->user_id }}">{{ $teacher->first_name }}
                                                {{ $teacher->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn"
                                    style="background-color: #6ba5b3; color: white; padding: 12px 40px; border-radius: 10px; font-size: 16px; font-weight: 600; border: none;">
                                    Create Class
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 15px; border: none;">
                    <div class="modal-header"
                        style="background-color: #6ba5b3; color: white; border-radius: 15px 15px 0 0; padding: 20px 30px;">
                        <h5 class="modal-title" style="font-size: 24px; font-weight: 600; margin: 0;">Information</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="padding: 40px 30px; background-color: white;">
                        <p style="font-size: 24px; font-weight: 400; color: #1a1a1a; margin-bottom: 20px;">Class
                            successfully created.</p>
                        <p style="font-size: 24px; font-weight: 400; color: #1a1a1a; margin-bottom: 30px;">Add students
                            now?</p>

                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn" onclick="skipForNow()"
                                style="background-color: #6ba5b3; color: white; padding: 12px 35px; border-radius: 10px; font-size: 16px; font-weight: 600; border: none; min-width: 150px;">
                                Skip for Now
                            </button>
                            <button type="button" class="btn" onclick="addStudentsAfterCreate()"
                                style="background-color: #6ba5b3; color: white; padding: 12px 35px; border-radius: 10px; font-size: 16px; font-weight: 600; border: none; min-width: 150px;">
                                Add Students
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Management Modal -->
        <div class="modal fade student-management-modal" id="studentManagementModal" tabindex="-1" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 10px; border: none;">
                    <div class="student-management-header">
                        <i class="fas fa-school"></i>
                        <h5 id="modalClassTitle">Class Name</h5>
                        <button type="button" class="btn-close btn-close-white ms-auto"
                            onclick="closeStudentManagement()"></button>
                    </div>

                    <div class="student-management-body">
                        <div class="row">
                            <!-- Student Pool (Left Side) -->
                            <div class="col-md-6">
                                <div class="student-panel">
                                    <div class="student-panel-title">Student Pool</div>

                                    <div class="student-search">
                                        <input type="text" id="studentPoolSearch" placeholder="Search for users">
                                        <i class="fas fa-search"></i>
                                    </div>

                                    <div class="student-table-container">
                                        <table class="student-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40px;"></th>
                                                    <th class="sortable" onclick="sortStudentPool('name')">
                                                        NAME <i class="fas fa-sort sort-icon"></i>
                                                    </th>
                                                    <th class="sortable" onclick="sortStudentPool('id')">
                                                        ID Number <i class="fas fa-sort sort-icon"></i>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="studentPoolBody">
                                                <tr>
                                                    <td colspan="3"
                                                        style="text-align: center; padding: 40px; color: #9ca3af;">
                                                        Loading students...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Class Members (Right Side) -->
                            <div class="col-md-6">
                                <div class="student-panel">
                                    <div class="student-panel-title">Class Members</div>

                                    <div class="student-table-container">
                                        <table class="student-table">
                                            <thead>
                                                <tr>
                                                    <th class="sortable" onclick="sortClassMembers('name')">
                                                        NAME <i class="fas fa-sort sort-icon"></i>
                                                    </th>
                                                    <th class="sortable" onclick="sortClassMembers('id')">
                                                        ID Number <i class="fas fa-sort sort-icon"></i>
                                                    </th>
                                                    <th style="width: 40px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="classMembersBody">
                                                <tr>
                                                    <td colspan="3"
                                                        style="text-align: center; padding: 40px; color: #9ca3af;">
                                                        No students enrolled
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="student-management-footer">
                        <button type="button" class="btn-secondary-custom" onclick="openCopyClassesModal()">
                            Copy from Existing Classes
                        </button>
                        <button type="button" class="btn-primary-custom" onclick="addSelectedStudents()">
                            Add Students
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copy from Classes Modal -->
        <div class="modal fade copy-classes-modal" id="copyClassesModal" tabindex="-1" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 10px; border: none;">
                    <div class="copy-classes-header">
                        <h5>All Classes</h5>
                        <button type="button" class="btn-close btn-close-white ms-auto"
                            onclick="closeCopyClassesModal()"></button>
                    </div>

                    <div class="copy-classes-body" id="copyClassesBody">
                        <div class="text-center" style="padding: 40px; color: #9ca3af;">
                            Loading classes...
                        </div>
                    </div>

                    <div class="copy-classes-footer">
                        <button type="button" class="btn-primary-custom" onclick="copyFromSelectedClass()">
                            Copy from this Class
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let createdClassId = null;
        let currentClassId = null;
        let currentClassName = null;
        let availableStudents = [];
        let classMembers = [];
        let selectedStudentIds = [];
        let selectedSourceClassId = null;

        // Modal Functions
        function openNewClassModal() {
            const modal = new bootstrap.Modal(document.getElementById('newClassModal'));
            modal.show();
        }

        // Handle form submission for new class
        document.getElementById('newClassForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('{{ route('admin.manage-classes.store') }}', {
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
                        const newClassModal = bootstrap.Modal.getInstance(document.getElementById(
                            'newClassModal'));
                        newClassModal.hide();

                        // Reset form
                        document.getElementById('newClassForm').reset();

                        // Show success modal
                        setTimeout(() => {
                            const successModal = new bootstrap.Modal(document.getElementById(
                                'successModal'));
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

        function addStudentsAfterCreate() {
            const successModal = bootstrap.Modal.getInstance(document.getElementById('successModal'));
            successModal.hide();

            setTimeout(() => {
                // Get the created class info
                fetch(`/admin/manage-classes/${createdClassId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            manageStudents(createdClassId, data.class.title);
                        }
                    });
            }, 300);
        }

        // Student Management Functions
        function manageStudents(classId, className) {
            currentClassId = classId;
            currentClassName = className;
            selectedStudentIds = [];

            document.getElementById('modalClassTitle').textContent = className;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('studentManagementModal'));
            modal.show();

            // Load data
            loadAvailableStudents();
            loadClassMembers();
        }

        function closeStudentManagement() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('studentManagementModal'));
            modal.hide();
            location.reload();
        }

        function loadAvailableStudents() {
            fetch(`/admin/manage-classes/${currentClassId}/available-students`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        availableStudents = data.students;
                        renderStudentPool();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('studentPoolBody').innerHTML =
                        '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #ef4444;">Error loading students</td></tr>';
                });
        }

        function loadClassMembers() {
            fetch(`/admin/manage-classes/${currentClassId}/class-members`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        classMembers = data.members;
                        renderClassMembers();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('classMembersBody').innerHTML =
                        '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #ef4444;">Error loading members</td></tr>';
                });
        }

        function renderStudentPool() {
            const tbody = document.getElementById('studentPoolBody');

            if (availableStudents.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="3" style="text-align: center; padding: 40px; color: #9ca3af; font-style: italic;">No available students</td></tr>';
                return;
            }

            tbody.innerHTML = availableStudents.map(student => `
            <tr>
                <td>
                    <input type="checkbox" 
                           class="student-checkbox" 
                           value="${student.user_id}"
                           onchange="toggleStudentSelection(${student.user_id}, this.checked)">
                </td>
                <td>${student.name}</td>
                <td>${student.id_number}</td>
            </tr>
        `).join('');
        }

        function renderClassMembers() {
            const tbody = document.getElementById('classMembersBody');

            if (classMembers.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="3" style="text-align: center; padding: 40px; color: #9ca3af; font-style: italic;">No students enrolled</td></tr>';
                return;
            }

            tbody.innerHTML = classMembers.map(member => `
            <tr>
                <td>${member.name}</td>
                <td>${member.id_number}</td>
                <td>
                    <i class="fas fa-times remove-student-btn" 
                       onclick="removeStudentFromClass(${member.user_id})"
                       title="Remove student"></i>
                </td>
            </tr>
        `).join('');
        }

        function toggleStudentSelection(studentId, isChecked) {
            if (isChecked) {
                if (!selectedStudentIds.includes(studentId)) {
                    selectedStudentIds.push(studentId);
                }
            } else {
                selectedStudentIds = selectedStudentIds.filter(id => id !== studentId);
            }
        }

        function addSelectedStudents() {
            if (selectedStudentIds.length === 0) {
                alert('Please select at least one student');
                return;
            }

            fetch(`/admin/manage-classes/${currentClassId}/add-students`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        student_ids: selectedStudentIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        selectedStudentIds = [];
                        loadAvailableStudents();
                        loadClassMembers();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while adding students');
                });
        }

        function removeStudentFromClass(studentId) {
            if (!confirm('Are you sure you want to remove this student from the class?')) {
                return;
            }

            fetch(`/admin/manage-classes/${currentClassId}/remove-student/${studentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadAvailableStudents();
                        loadClassMembers();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while removing the student');
                });
        }

        // Copy Classes Functions
        function openCopyClassesModal() {
            console.log('=== Opening Copy Classes Modal ===');
            console.log('Current class ID:', currentClassId);
            selectedSourceClassId = null; // Reset selection
            const modal = new bootstrap.Modal(document.getElementById('copyClassesModal'));
            modal.show();
            loadOtherClasses();
        }

        function closeCopyClassesModal() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('copyClassesModal'));
            if (modal) {
                modal.hide();
            }
            selectedSourceClassId = null; // Reset on close
        }

        function loadOtherClasses() {
            console.log('Loading other classes...');
            fetch(`/admin/manage-classes/${currentClassId}/other-classes`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Other classes response:', data);
                    if (data.success) {
                        renderOtherClasses(data.classes);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('copyClassesBody').innerHTML =
                        '<div class="text-center" style="padding: 40px; color: #ef4444;">Error loading classes</div>';
                });
        }

        function renderOtherClasses(classes) {
            console.log('Rendering', classes.length, 'other classes');
            const container = document.getElementById('copyClassesBody');

            if (classes.length === 0) {
                container.innerHTML = '<div class="no-classes-message">No other classes available</div>';
                return;
            }

            let html = '';
            classes.forEach((cls) => {
                html += `
            <label class="class-item" for="sourceClass_${cls.class_id}" style="cursor: pointer; display: flex; align-items: center; padding: 15px; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 12px; transition: all 0.2s;">
                <input type="radio" 
                       name="sourceClass" 
                       id="sourceClass_${cls.class_id}"
                       value="${cls.class_id}" 
                       style="width: 20px; height: 20px; margin-right: 15px; cursor: pointer; accent-color: #6ba5b3;"
                       onchange="handleSourceClassSelection(${cls.class_id}, this)">
                <div class="class-item-content" style="flex: 1; display: flex; align-items: center; gap: 20px;">
                    <div class="class-icon" style="width: 45px; height: 45px; background-color: #6ba5b3; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="class-info" style="flex: 1;">
                        <div class="class-info-name" style="font-weight: 600; color: #1a1a1a; font-size: 16px; margin-bottom: 5px;">${cls.name}</div>
                        <div class="class-info-details" style="display: flex; gap: 20px; font-size: 14px; color: #6b7280;">
                            <span><strong>Teacher:</strong> ${cls.teacher}</span>
                            <span><strong>Subject:</strong> ${cls.subject}</span>
                            <span><strong>Students:</strong> ${cls.student_count}</span>
                        </div>
                    </div>
                </div>
            </label>
        `;
            });

            container.innerHTML = html;
            console.log('Rendered HTML for classes');
        }

        function handleSourceClassSelection(classId, radioElement) {
            console.log('=== Class Selection Changed ===');
            console.log('Selected class ID:', classId);
            console.log('Radio element:', radioElement);
            console.log('Radio checked:', radioElement.checked);

            // Store the selected class ID
            selectedSourceClassId = classId;

            console.log('selectedSourceClassId updated to:', selectedSourceClassId);

            // Visual feedback
            document.querySelectorAll('.class-item').forEach(item => {
                item.style.backgroundColor = '';
                item.style.borderColor = '#e5e7eb';
            });

            const selectedLabel = radioElement.closest('.class-item');
            if (selectedLabel) {
                selectedLabel.style.backgroundColor = '#f0f9ff';
                selectedLabel.style.borderColor = '#6ba5b3';
            }
        }

        function copyFromSelectedClass() {
            console.log('=== Copy From Selected Class ===');
            console.log('selectedSourceClassId:', selectedSourceClassId);
            console.log('currentClassId:', currentClassId);

            if (!selectedSourceClassId) {
                alert('Please select a class to copy from');
                return;
            }

            if (!currentClassId) {
                alert('Target class is not set');
                return;
            }

            // Get the button that was clicked
            const btn = event ? event.target : document.querySelector('.copy-classes-footer .btn-primary-custom');
            const originalText = btn ? btn.textContent : '';

            // Disable button during request
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Copying...';
            }

            console.log('Making POST request to:',
                `/admin/manage-classes/${currentClassId}/copy-students/${selectedSourceClassId}`);

            fetch(`/admin/manage-classes/${currentClassId}/copy-students/${selectedSourceClassId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response ok:', response.ok);
                    return response.text();
                })
                .then(text => {
                    console.log('Raw response text:', text);
                    try {
                        const data = JSON.parse(text);
                        console.log('Parsed JSON response:', data);

                        if (data.success) {
                            alert(data.message);
                            closeCopyClassesModal();

                            // Reload student lists after short delay
                            setTimeout(() => {
                                loadAvailableStudents();
                                loadClassMembers();
                            }, 300);

                            selectedSourceClassId = null;
                        } else {
                            alert('Error: ' + data.message);
                            console.error('Server returned error:', data);
                        }
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        console.error('Response was:', text);
                        alert('Invalid response from server');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('An error occurred while copying students: ' + error.message);
                })
                .finally(() => {
                    // Re-enable button
                    if (btn) {
                        btn.disabled = false;
                        btn.textContent = originalText || 'Copy from this Class';
                    }
                });
        }

        // Search functionality for student pool
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('studentPoolSearch').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('#studentPoolBody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        });

        // Sorting functions
        let studentPoolSortOrder = {
            name: 'asc',
            id: 'asc'
        };
        let classMembersSortOrder = {
            name: 'asc',
            id: 'asc'
        };

        function sortStudentPool(column) {
            const order = studentPoolSortOrder[column];

            availableStudents.sort((a, b) => {
                const aVal = column === 'name' ? a.name : a.id_number;
                const bVal = column === 'name' ? b.name : b.id_number;

                if (order === 'asc') {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });

            studentPoolSortOrder[column] = order === 'asc' ? 'desc' : 'asc';
            renderStudentPool();
        }

        function sortClassMembers(column) {
            const order = classMembersSortOrder[column];

            classMembers.sort((a, b) => {
                const aVal = column === 'name' ? a.name : a.id_number;
                const bVal = column === 'name' ? b.name : b.id_number;

                if (order === 'asc') {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });

            classMembersSortOrder[column] = order === 'asc' ? 'desc' : 'asc';
            renderClassMembers();
        }

        // Action Functions
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

        // Search functionality for main table
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
