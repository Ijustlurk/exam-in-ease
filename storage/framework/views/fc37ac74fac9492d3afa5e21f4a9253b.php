

<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin-access')): ?>
        <style>
            /* Base Styles */
            .main {
                margin-left: 60px;
                transition: margin-left 0.3s;
                padding: 2rem;
            }

            .users-container {
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                overflow: hidden;
                border: 1px solid #e0e0e0;
                display: flex;
                flex-direction: column;
                height: calc(100vh - 6rem);
                max-height: calc(100vh - 6rem);
            }

            .users-header {
                padding: 1.5rem 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #e0e0e0;
                background-color: #fff;
                gap: 1rem;
                flex-shrink: 0;
            }

            .users-title-section {
                display: flex;
                align-items: center;
                gap: 1.5rem;
            }

            .role-filter-wrapper {
                position: relative;
            }

            .role-filter-btn {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.7rem 1.2rem;
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                cursor: pointer;
                font-size: 0.95rem;
                font-weight: 500;
                color: #2c3e50;
                transition: all 0.2s;
                min-width: 160px;
            }

            .role-filter-btn:hover {
                background-color: #e9ecef;
                border-color: #6ba5bb;
            }

            .role-filter-btn i {
                font-size: 0.85rem;
                color: #95a5a6;
            }

            .role-filter-dropdown {
                position: fixed;
                background: white;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                min-width: 200px;
                z-index: 1000;
                display: none;
                overflow: hidden;
            }

            .role-filter-dropdown.show {
                display: block;
                animation: dropdownFadeIn 0.2s ease-out;
            }

            .role-filter-item {
                padding: 0.75rem 1rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                color: #2c3e50;
                cursor: pointer;
                transition: background-color 0.2s;
                border: none;
                background: none;
                width: 100%;
                text-align: left;
                font-size: 0.9rem;
            }

            .role-filter-item:hover {
                background-color: #f8f9fa;
            }

            .role-filter-item.active {
                background-color: #e3f2fd;
                color: #6ba5bb;
                font-weight: 600;
            }

            .role-filter-item i {
                font-size: 1rem;
                color: #95a5a6;
            }

            .role-filter-item.active i {
                color: #6ba5bb;
            }

            .table-scroll-container {
                flex: 1;
                overflow-y: auto;
                overflow-x: hidden;
                background-color: #fff;
            }

            .table-scroll-container::-webkit-scrollbar {
                width: 10px;
            }

            .table-scroll-container::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .table-scroll-container::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 5px;
            }

            .table-scroll-container::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
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
                border-color: #6ba5bb;
                box-shadow: 0 0 0 0.2rem rgba(107, 165, 187, 0.15);
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
                position: sticky;
                top: 0;
                z-index: 10;
            }

            .users-table thead th.sortable {
                cursor: pointer;
                user-select: none;
                transition: background-color 0.2s;
            }

            .users-table thead th.sortable:hover {
                background-color: #c5d9e3 !important;
            }

            .users-table thead th.sortable i {
                margin-left: 8px;
                font-size: 0.75rem;
                color: #6b7280;
                transition: transform 0.2s;
            }

            .users-table thead th.sortable.asc i {
                transform: rotate(180deg);
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
                accent-color: #6ba5bb;
            }

            .user-info {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .user-icon {
                width: 40px;
                height: 40px;
                background-color: #2c3e50;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .user-icon i {
                font-size: 1.4rem;
                color: #fff;
            }

            .user-details {
                display: flex;
                flex-direction: column;
            }

            .user-name {
                font-size: 0.95rem;
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 2px;
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

            /* Status Cell with Dropdown */
            .status-cell {
                position: relative;
            }

            .status-trigger {
                cursor: pointer;
                display: inline-block;
                font-weight: 600;
            }

            .status-active {
                color: #27ae60;
            }

            .status-inactive {
                color: #e74c3c;
            }

            .status-dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                min-width: 220px;
                z-index: 1000;
                display: none;
                margin-top: 0.5rem;
            }

            .status-dropdown.show {
                display: block;
                animation: dropdownFadeIn 0.2s ease-out;
            }

            @keyframes dropdownFadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .dropdown-item-custom {
                padding: 0.75rem 1rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                color: #2c3e50;
                text-decoration: none;
                transition: background-color 0.2s;
                cursor: pointer;
                border: none;
                background: none;
                width: 100%;
                text-align: left;
                font-size: 0.9rem;
            }

            .dropdown-item-custom:first-child {
                border-radius: 8px 8px 0 0;
            }

            .dropdown-item-custom:last-child {
                border-radius: 0 0 8px 8px;
            }

            .dropdown-item-custom:hover {
                background-color: #f8f9fa;
            }

            .dropdown-item-custom i {
                font-size: 1.1rem;
                color: #95a5a6;
            }

            .dropdown-item-custom.delete {
                color: #e74c3c;
            }

            .dropdown-item-custom.delete i {
                color: #e74c3c;
            }

            /* Action Buttons */
            .action-buttons {
                display: flex;
                gap: 1rem;
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

            .action-btn i {
                font-size: 1.1rem;
            }

            .action-btn.reset:hover {
                color: #6ba5bb;
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

            /* View Details Modal */
            .detail-row {
                display: flex;
                padding: 1rem 0;
                border-bottom: 1px solid #e8ecef;
            }

            .detail-row:last-child {
                border-bottom: none;
            }

            .detail-label {
                font-weight: 600;
                color: #2c3e50;
                width: 150px;
                flex-shrink: 0;
            }

            .detail-value {
                color: #5a6c7d;
                flex: 1;
            }

            /* Modal Header Custom */
            .modal-header-custom {
                background-color: #6ba5bb;
                color: white;
                padding: 1.5rem 2rem;
                border-radius: 15px 15px 0 0;
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .modal-header-custom i {
                font-size: 1.5rem;
            }

            .modal-header-custom h5 {
                font-size: 1.25rem;
                font-weight: 600;
                margin: 0;
            }
        </style>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div id="mainContent" class="main">
            <div class="users-container">
                <div class="users-header">
                    <div class="users-title-section">
                        <h4 class="users-title" id="usersTitle">All Users</h4>
                        <span class="users-count" id="usersCount"><?php echo e(number_format($users->count() ?? 0)); ?></span>
                    </div>

                    <div class="role-filter-wrapper">
                        <button class="role-filter-btn" id="roleFilterBtn" onclick="toggleRoleFilter()">
                            <span id="roleFilterText">All Roles</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="role-filter-dropdown" id="roleFilterDropdown">
                            <button class="role-filter-item active" onclick="filterByRole('all')">
                                <i class="bi bi-people-fill"></i>
                                <span>All Roles</span>
                            </button>
                            <button class="role-filter-item" onclick="filterByRole('instructor')">
                                <i class="bi bi-person-badge"></i>
                                <span>Instructor</span>
                            </button>
                            <button class="role-filter-item" onclick="filterByRole('student')">
                                <i class="bi bi-person"></i>
                                <span>Student</span>
                            </button>
                            <button class="role-filter-item" onclick="filterByRole('programchair')">
                                <i class="bi bi-briefcase"></i>
                                <span>Program Chair</span>
                            </button>
                            <button class="role-filter-item" onclick="filterByRole('admin')">
                                <i class="bi bi-shield-fill-check"></i>
                                <span>Admin</span>
                            </button>
                        </div>
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

                <div class="table-scroll-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">
                                    <input type="checkbox" id="selectAllUsers" class="user-checkbox">
                                </th>
                                <th style="width: 35%;" class="sortable" onclick="sortByName()">
                                    NAME
                                    <i class="bi bi-chevron-down" id="nameSortIcon"></i>
                                </th>
                                <th style="width: 20%;">ROLE</th>
                                <th style="width: 15%;">STATUS</th>
                                <th style="width: 25%;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
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
                                
                                // Prepare user data for modal
                                $userData = [
                                    'id' => $userId,
                                    'name' => $userName,
                                    'email' => $userEmail,
                                    'role' => $userRole,
                                    'status' => $userStatus,
                                    'first_name' => $user->first_name ?? '',
                                    'last_name' => $user->last_name ?? '',
                                    'middle_name' => $user->middle_name ?? '',
                                    'username' => $user->username ?? '',
                                    'id_number' => $user->id_number ?? 'N/A'
                                ];
                            ?>
                            <tr data-user-id="<?php echo e($userId); ?>" data-user-role="<?php echo e($userRole); ?>">
                                <td>
                                    <input type="checkbox" class="user-checkbox" value="<?php echo e($userId); ?>">
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-icon">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div class="user-details">
                                            <span class="user-name"><?php echo e($userName); ?></span>
                                            <span class="user-email"><?php echo e($userEmail); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-badge <?php echo e($roleBadgeClass); ?>">
                                        <?php echo e($userRole === 'programchair' ? 'Program Chair' : ucfirst($userRole)); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="status-cell">
                                        <span class="status-trigger <?php echo e($statusClass); ?>" onclick="toggleStatusDropdown(event, <?php echo e($userId); ?>)">
                                            <?php echo e($userStatus); ?>

                                        </span>
                                        <div class="status-dropdown" id="status-dropdown-<?php echo e($userId); ?>">
                                            <button class="dropdown-item-custom" onclick="openEditUserModal(<?php echo e($userId); ?>)">
                                                <i class="bi bi-pencil"></i>
                                                <span>Edit User</span>
                                            </button>
                                            <button class="dropdown-item-custom" onclick='viewUserDetails(<?php echo json_encode($userData, 15, 512) ?>)'>
                                                <i class="bi bi-eye"></i>
                                                <span>View User Details</span>
                                            </button>
                                            <button class="dropdown-item-custom" onclick="resetUserPassword(<?php echo e($userId); ?>, '<?php echo e($userName); ?>')">
                                                <i class="bi bi-arrow-clockwise"></i>
                                                <span>Reset User Password</span>
                                            </button>
                                            <button class="dropdown-item-custom delete" onclick="deleteUser(<?php echo e($userId); ?>, '<?php echo e($userName); ?>')">
                                                <i class="bi bi-trash"></i>
                                                <span>Delete User</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn reset" onclick="resetUserPassword(<?php echo e($userId); ?>, '<?php echo e($userName); ?>')" title="Reset Password">
                                            <i class="bi bi-arrow-clockwise"></i>
                                            <span>Reset Password</span>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteUser(<?php echo e($userId); ?>, '<?php echo e($userName); ?>')" title="Delete User">
                                            <i class="bi bi-trash"></i>
                                            <span>Delete User</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="bi bi-people"></i>
                                        <p>No users found.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <!-- View User Details Modal -->
        <div class="modal fade" id="viewUserDetailsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content" style="border-radius: 15px; border: none;">
                    <div class="modal-header-custom">
                        <i class="bi bi-person-circle"></i>
                        <h5>User Details</h5>
                        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="padding: 2rem;">
                        <div id="userDetailsContent">
                            <!-- User details will be populated here -->
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e8ecef; padding: 1rem 2rem;">
                        <button type="button" class="btn" style="background-color: #6ba5bb; color: white; padding: 0.6rem 1.5rem; border-radius: 8px;" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add User Options Modal -->
        <div class="modal fade" id="addUserOptionsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
                <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden;">
                    <div class="modal-header-custom">
                        <i class="bi bi-person-plus-fill"></i>
                        <h5>Add User</h5>
                        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body" style="padding: 2rem;">
                        <button class="btn w-100 mb-3 d-flex align-items-center justify-content-start" 
                                onclick="openAddUserModal()"
                                style="background: #6ba5bb; color: white; border: none; border-radius: 10px; padding: 1.2rem 1.5rem; font-size: 1rem; font-weight: 500; transition: all 0.3s;">
                            <i class="bi bi-person-plus-fill me-3" style="font-size: 1.5rem;"></i>
                            <span>Add One</span>
                        </button>
                        
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
        <?php echo $__env->make('admin.users.partials.add-user-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('admin.users.partials.edit-user-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('admin.users.partials.import-users-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <script>
            let currentRoleFilter = 'all';
            let nameSortOrder = 'asc'; // Track current sort order

            // Sort users by name
            function sortByName() {
                const tbody = document.querySelector('.users-table tbody');
                const rows = Array.from(tbody.querySelectorAll('tr:not(.empty-state)'));
                const sortIcon = document.getElementById('nameSortIcon');
                const sortHeader = document.querySelector('.sortable');
                
                // Toggle sort order
                nameSortOrder = nameSortOrder === 'asc' ? 'desc' : 'asc';
                
                // Update header class for icon rotation
                if (nameSortOrder === 'asc') {
                    sortHeader.classList.remove('asc');
                } else {
                    sortHeader.classList.add('asc');
                }
                
                // Sort rows
                rows.sort((a, b) => {
                    const nameA = a.querySelector('.user-name')?.textContent.trim().toLowerCase() || '';
                    const nameB = b.querySelector('.user-name')?.textContent.trim().toLowerCase() || '';
                    
                    if (nameSortOrder === 'asc') {
                        return nameA.localeCompare(nameB);
                    } else {
                        return nameB.localeCompare(nameA);
                    }
                });
                
                // Re-append sorted rows
                rows.forEach(row => tbody.appendChild(row));
            }

            // Toggle Role Filter Dropdown
            function toggleRoleFilter() {
                const dropdown = document.getElementById('roleFilterDropdown');
                const button = document.getElementById('roleFilterBtn');
                
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                } else {
                    // Position the dropdown relative to the button
                    const rect = button.getBoundingClientRect();
                    dropdown.style.top = (rect.bottom + 8) + 'px';
                    dropdown.style.left = rect.left + 'px';
                    dropdown.classList.add('show');
                }
            }

            // Filter users by role
            function filterByRole(role) {
                currentRoleFilter = role;
                
                // Update active state in dropdown
                document.querySelectorAll('.role-filter-item').forEach(item => {
                    item.classList.remove('active');
                });
                event.target.closest('.role-filter-item').classList.add('active');
                
                // Update filter button text
                const roleText = {
                    'all': 'All Roles',
                    'instructor': 'Instructor',
                    'student': 'Student',
                    'programchair': 'Program Chair',
                    'admin': 'Admin'
                };
                document.getElementById('roleFilterText').textContent = roleText[role] || 'All Roles';
                
                // Update title
                const titleText = {
                    'all': 'All Users',
                    'instructor': 'Instructors',
                    'student': 'Students',
                    'programchair': 'Program Chairs',
                    'admin': 'Admins'
                };
                document.getElementById('usersTitle').textContent = titleText[role] || 'All Users';
                
                // Close dropdown
                document.getElementById('roleFilterDropdown').classList.remove('show');
                
                // Apply filter
                applyFilters();
            }

            // Apply both role and search filters
            function applyFilters() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();
                const rows = document.querySelectorAll('.users-table tbody tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    if (row.querySelector('.empty-state')) return;
                    
                    const userRole = row.getAttribute('data-user-role');
                    const name = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
                    const email = row.querySelector('.user-email')?.textContent.toLowerCase() || '';
                    const role = row.querySelector('.role-badge')?.textContent.toLowerCase() || '';

                    // Check role filter
                    const matchesRole = currentRoleFilter === 'all' || userRole === currentRoleFilter;
                    
                    // Check search filter
                    const matchesSearch = searchTerm === '' || 
                                         name.includes(searchTerm) || 
                                         email.includes(searchTerm) || 
                                         role.includes(searchTerm);

                    if (matchesRole && matchesSearch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update count
                document.getElementById('usersCount').textContent = visibleCount.toLocaleString();
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.role-filter-wrapper')) {
                    document.getElementById('roleFilterDropdown').classList.remove('show');
                }
            });

            // Toggle Status Dropdown (when clicking Active/Inactive)
            function toggleStatusDropdown(event, userId) {
                event.stopPropagation();
                
                // Close all other dropdowns
                document.querySelectorAll('.status-dropdown').forEach(dropdown => {
                    if (dropdown.id !== `status-dropdown-${userId}`) {
                        dropdown.classList.remove('show');
                    }
                });
                
                // Toggle current dropdown
                const dropdown = document.getElementById(`status-dropdown-${userId}`);
                dropdown.classList.toggle('show');
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.status-cell')) {
                    document.querySelectorAll('.status-dropdown').forEach(dropdown => {
                        dropdown.classList.remove('show');
                    });
                }
            });

            // View User Details
            function viewUserDetails(userData) {
                console.log('User data:', userData);
                
                const content = document.getElementById('userDetailsContent');
                
                const roleDisplay = userData.role === 'programchair' ? 'Program Chair' : 
                                   userData.role.charAt(0).toUpperCase() + userData.role.slice(1);
                
                content.innerHTML = `
                    <div class="detail-row">
                        <div class="detail-label">User ID:</div>
                        <div class="detail-value">${userData.id}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Full Name:</div>
                        <div class="detail-value">${userData.name}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">First Name:</div>
                        <div class="detail-value">${userData.first_name || 'N/A'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Last Name:</div>
                        <div class="detail-value">${userData.last_name || 'N/A'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Middle Name:</div>
                        <div class="detail-value">${userData.middle_name || 'N/A'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Email:</div>
                        <div class="detail-value">${userData.email}</div>
                    </div>
                    ${userData.username ? `
                    <div class="detail-row">
                        <div class="detail-label">Username:</div>
                        <div class="detail-value">${userData.username}</div>
                    </div>
                    ` : ''}
                    ${userData.id_number && userData.id_number !== 'N/A' ? `
                    <div class="detail-row">
                        <div class="detail-label">ID Number:</div>
                        <div class="detail-value">${userData.id_number}</div>
                    </div>
                    ` : ''}
                    <div class="detail-row">
                        <div class="detail-label">Role:</div>
                        <div class="detail-value">
                            <span class="role-badge role-${userData.role}">${roleDisplay}</span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Status:</div>
                        <div class="detail-value">
                            <span class="${userData.status.toLowerCase() === 'active' || userData.status.toLowerCase() === 'enrolled' ? 'status-active' : 'status-inactive'}">
                                ${userData.status}
                            </span>
                        </div>
                    </div>
                `;
                
                // Close dropdown
                document.querySelectorAll('.status-dropdown').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('viewUserDetailsModal'));
                modal.show();
            }

            // Reset User Password
            function resetUserPassword(userId, userName) {
                // Close dropdown
                document.querySelectorAll('.status-dropdown').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
                
                if (confirm(`Are you sure you want to reset the password for ${userName}?`)) {
                    fetch(`/admin/users/${userId}/reset-password`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message || 'Password reset successfully');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to reset password'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while resetting the password');
                    });
                }
            }

            // Delete User
            function deleteUser(userId, userName) {
                // Close dropdown
                document.querySelectorAll('.status-dropdown').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
                
                if (confirm(`Are you sure you want to delete ${userName}? This action cannot be undone.`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/users/${userId}`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '<?php echo e(csrf_token()); ?>';
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            // Function to open Add User Modal from Options Modal
            function openAddUserModal() {
                const optionsModal = bootstrap.Modal.getInstance(document.getElementById('addUserOptionsModal'));
                if (optionsModal) {
                    optionsModal.hide();
                }
                
                setTimeout(() => {
                    const addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
                    addUserModal.show();
                }, 300);
            }

            // Function to open Import Modal
            function openImportModal() {
                const optionsModal = bootstrap.Modal.getInstance(document.getElementById('addUserOptionsModal'));
                if (optionsModal) {
                    optionsModal.hide();
                }
                
                setTimeout(() => {
                    const importModal = new bootstrap.Modal(document.getElementById('importUsersModal'));
                    importModal.show();
                }, 300);
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Select All Checkbox
                const selectAll = document.getElementById('selectAllUsers');
                const userCheckboxes = document.querySelectorAll('.user-checkbox:not(#selectAllUsers)');

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
                        applyFilters();
                    });
                }
            });
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.Admin.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\exam1\resources\views/admin/users/index.blade.php ENDPATH**/ ?>