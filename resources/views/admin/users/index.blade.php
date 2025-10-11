@extends('layouts.Admin.app')

@section('content')
    @can('admin-access')
        <style>
            /* Base Styles */
            .main {
                margin-left: 60px;
                transition: margin-left 0.3s;
                padding: 2rem;
                background-color: #e8f0f5;
            }

            .users-container {
                background-color: #fff;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                overflow: hidden;
                border: 1px solid #e0e0e0;
            }

            .users-header {
                padding: 1.5rem 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #e0e0e0;
                background-color: #fff;
            }

            .users-title-section {
                display: flex;
                align-items: center;
                gap: 1.5rem;
            }

            .users-title {
                font-size: 1.5rem;
                font-weight: 600;
                color: #2c3e50;
                margin: 0;
            }

            .users-count {
                font-size: 1.5rem;
                color: #95a5a6;
                font-weight: 400;
            }

            .search-bar {
                position: relative;
                width: 400px;
            }

            .search-bar input {
                width: 100%;
                padding: 0.7rem 2.5rem 0.7rem 1rem;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                font-size: 0.95rem;
                font-style: italic;
            }

            .search-bar input:focus {
                outline: none;
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
            }

            .search-bar .search-icon {
                position: absolute;
                right: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: #95a5a6;
                font-size: 1.1rem;
            }

            .add-user-btn {
                background-color: #6ba5bb;
                color: white;
                border: none;
                padding: 0.7rem 1.5rem;
                border-radius: 8px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
                transition: background-color 0.3s;
                font-size: 0.95rem;
            }

            .add-user-btn:hover {
                background-color: #5a94aa;
            }

            .users-table {
                width: 100%;
                border-collapse: collapse;
            }

            .users-table thead th {
                background-color: #d9e8f0 !important;
                color: #2c3e50;
                text-align: left;
                font-weight: 600;
                padding: 1rem 1.5rem;
                border-bottom: 1px solid #c5d9e3;
                font-size: 0.875rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .users-table thead th:first-child {
                padding-left: 2rem;
            }

            .users-table tbody tr {
                background-color: #fff;
                border-bottom: 1px solid #e8ecef;
                transition: background-color 0.2s;
            }

            .users-table tbody tr:hover {
                background-color: #f8f9fa;
            }

            .users-table td {
                padding: 1.25rem 1.5rem;
                vertical-align: middle;
                font-size: 0.95rem;
                color: #2c3e50;
            }

            .users-table td:first-child {
                padding-left: 2rem;
            }

            .user-checkbox {
                width: 18px;
                height: 18px;
                cursor: pointer;
                accent-color: #667eea;
            }

            .user-info {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .user-icon {
                width: 36px;
                height: 36px;
                background-color: #2c3e50;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .user-icon i {
                font-size: 1.3rem;
                color: #fff;
            }

            .user-details {
                display: flex;
                flex-direction: column;
            }

            .user-name {
                font-size: 0.95rem;
                font-weight: 500;
                color: #2c3e50;
            }

            .user-email {
                font-size: 0.85rem;
                color: #95a5a6;
                font-style: italic;
            }

            .role-badge {
                display: inline-block;
                padding: 0.4rem 1rem;
                border-radius: 20px;
                font-size: 0.85rem;
                font-weight: 600;
                color: white;
                text-align: center;
            }

            .role-instructor {
                background-color: #6ba5bb;
            }

            .role-student {
                background-color: #95b85f;
            }

            .role-chair {
                background-color: #f39c12;
            }

            .role-admin {
                background-color: #e74c3c;
            }

            .status-active {
                color: #27ae60;
                font-weight: 600;
            }

            .status-inactive {
                color: #e74c3c;
                font-weight: 600;
            }

            .action-buttons {
                display: flex;
                gap: 1.5rem;
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

            .action-btn.delete:hover {
                color: #e74c3c;
            }

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

            /* Add User Options Modal Styles */
            #addUserOptionsModal .btn:hover {
                background: #5a94aa !important;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(107, 165, 187, 0.3);
            }

            #addUserOptionsModal .btn i {
                transition: transform 0.3s;
            }

            #addUserOptionsModal .btn:hover i {
                transform: scale(1.1);
            }
        </style>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div id="mainContent" class="main">
            <div class="users-container">
                <div class="users-header">
                    <div class="users-title-section">
                        <h4 class="users-title">All Users</h4>
                        <span class="users-count">{{ number_format($users->count() ?? 0) }}</span>
                    </div>

                    <div class="search-bar">
                        <input type="text" id="searchInput" placeholder="Search for users">
                        <i class="bi bi-search search-icon"></i>
                    </div>

                    <button class="add-user-btn" data-bs-toggle="modal" data-bs-target="#addUserOptionsModal">
                        <i class="bi bi-plus-lg"></i>
                        Add User
                    </button>
                </div>

                <table class="users-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">
                                <input type="checkbox" id="selectAllUsers">
                            </th>
                            <th style="width: 35%;">NAME</th>
                            <th style="width: 20%;">ROLE</th>
                            <th style="width: 15%;">STATUS</th>
                            <th style="width: 25%;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users ?? [] as $user)
                            @php
                                $userId = $user->user_id ?? 0;
                                $userRole = $user->role ?? 'student';
                                
                                // Build name
                                if ($userRole === 'admin') {
                                    $userName = $user->last_name ?? 'Admin';
                                    $userEmail = 'admin@system.local';
                                } else {
                                    $middleInitial = !empty($user->middle_name) ? ' ' . substr($user->middle_name, 0, 1) . '.' : '';
                                    $userName = ($user->first_name ?? '') . $middleInitial . ' ' . ($user->last_name ?? '');
                                    $userEmail = $user->email_address ?? '';
                                }

                                $roleBadgeClass = match ($userRole) {
                                    'instructor' => 'role-instructor',
                                    'student' => 'role-student',
                                    'programchair' => 'role-chair',
                                    'admin' => 'role-admin',
                                    default => 'role-student',
                                };

                                $userStatus = $user->status ?? 'Active';
                                $statusClass = (strtolower($userStatus) === 'active' || strtolower($userStatus) === 'enrolled') ? 'status-active' : 'status-inactive';
                            @endphp
                            <tr data-user-id="{{ $userId }}" data-user-role="{{ $userRole }}">
                                <td>
                                    <input type="checkbox" class="user-checkbox" value="{{ $userId }}">
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-icon">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div class="user-details">
                                            <span class="user-name">{{ $userName }}</span>
                                            <span class="user-email">{{ $userEmail }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-badge {{ $roleBadgeClass }}">
                                        {{ $userRole === 'programchair' ? 'Program Chair' : ucfirst($userRole) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="{{ $statusClass }}">{{ $userStatus }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn edit-btn" onclick="openEditUserModal({{ $userId }})" title="Edit User">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <form action="{{ route('admin.users.destroy', $userId) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="Delete User">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="bi bi-people"></i>
                                        <p>No users found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add User Options Modal (First Modal) -->
        <div class="modal fade" id="addUserOptionsModal" tabindex="-1" aria-labelledby="addUserOptionsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
                <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden;">
                    <!-- Header -->
                    <div class="modal-header" style="background: #6ba5bb; color: white; border: none; padding: 1.5rem 2rem;">
                        <h5 class="modal-title d-flex align-items-center" id="addUserOptionsModalLabel" style="font-size: 1.25rem; font-weight: 600;">
                            <i class="bi bi-person-plus-fill me-2" style="font-size: 1.5rem;"></i> Add User
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Body -->
                    <div class="modal-body" style="padding: 2rem;">
                        <!-- Add One Button -->
                        <button class="btn w-100 mb-3 d-flex align-items-center justify-content-start" 
                                onclick="openAddUserModal()"
                                style="background: #6ba5bb; color: white; border: none; border-radius: 10px; padding: 1.2rem 1.5rem; font-size: 1rem; font-weight: 500; transition: all 0.3s;">
                            <i class="bi bi-person-plus-fill me-3" style="font-size: 1.5rem;"></i>
                            <span>Add One</span>
                        </button>
                        
                        <!-- Import File Button -->
                        <button class="btn w-100 d-flex align-items-center justify-content-start" 
                                onclick="openImportModal()"
                                style="background: #6ba5bb; color: white; border: none; border-radius: 10px; padding: 1.2rem 1.5rem; font-size: 1rem; font-weight: 500; transition: all 0.3s;">
                            <i class="bi bi-file-earmark-arrow-up-fill me-3" style="font-size: 1.5rem;"></i>
                            <span>Import File</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Include Modals -->
        @include('admin.users.partials.add-user-modal')
        @include('admin.users.partials.edit-user-modal')
        @include('admin.users.partials.import-users-modal')

        <script>
            // Function to open Add User Modal from Options Modal
            function openAddUserModal() {
                // Close the options modal
                const optionsModal = bootstrap.Modal.getInstance(document.getElementById('addUserOptionsModal'));
                if (optionsModal) {
                    optionsModal.hide();
                }
                
                // Wait for the options modal to fully close, then open add user modal
                setTimeout(() => {
                    const addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
                    addUserModal.show();
                }, 300);
            }

            // Function to open Import Modal (placeholder for now)
            function openImportModal() {
                // Close the options modal
                const optionsModal = bootstrap.Modal.getInstance(document.getElementById('addUserOptionsModal'));
                if (optionsModal) {
                    optionsModal.hide();
                }
                
                // Wait for the options modal to fully close, then open import modal
                setTimeout(() => {
                    const importModal = new bootstrap.Modal(document.getElementById('importUsersModal'));
                    importModal.show();
                }, 300);
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Select All Checkbox
                const selectAll = document.getElementById('selectAllUsers');
                const userCheckboxes = document.querySelectorAll('.user-checkbox');

                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        userCheckboxes.forEach(checkbox => {
                            checkbox.checked = selectAll.checked;
                        });
                    });
                }

                // Search Functionality
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();
                        const rows = document.querySelectorAll('.users-table tbody tr');

                        rows.forEach(row => {
                            if (row.querySelector('.empty-state')) return;
                            
                            const name = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
                            const email = row.querySelector('.user-email')?.textContent.toLowerCase() || '';
                            const role = row.querySelector('.role-badge')?.textContent.toLowerCase() || '';

                            if (name.includes(searchTerm) || email.includes(searchTerm) || role.includes(searchTerm)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });
                }
            });
        </script>
    @endcan
@endsection