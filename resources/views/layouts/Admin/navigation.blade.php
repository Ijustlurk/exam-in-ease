<!-- Styles -->
<style>
    .sidebar {
        background-color: rgb(207, 218, 225);
        height: 550px;
        display: flex;
        flex-direction: column;
        padding-top: 1rem;
        transition: width 0.3s;
        width: 60px;
        overflow-x: hidden;
        position: fixed;
        top: 90px;
    }

    .sidebar.expanded {
        width: 220px;
    }

    .sidebar a {
        text-decoration: none;
        color: #000;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .sidebar .logo {
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #5f9eb7;
    }

    .sidebar .nav-item {
        padding: 1rem;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: background 0.2s;
        white-space: nowrap;
    }

    .sidebar .nav-item:hover {
        background-color: #c6e3ef;
    }

    .sidebar .nav-item.active {
        background-color: #5f9eb7;
        color: white;
        border-left: 4px solid #2c5f75;
    }

    .sidebar .nav-item.active a {
        color: white;
    }

    .sidebar .nav-icon {
        font-size: 1.5rem;
        min-width: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar .nav-label {
        display: none;
        font-weight: 500;
        margin-left: 1rem;
        line-height: 1.5rem;
    }

    .sidebar.expanded .nav-label {
        display: inline;
    }

    .main {
        margin-left: 60px;
        transition: margin-left 0.3s;
        padding: 2rem;
    }

    .main.expanded {
        margin-left: 220px;
    }
</style>

<!-- Navbar -->
<nav class="bg-white border-b border-gray-100 sticky-top">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="text-dark fs-2">
                    <i class="bi bi-clipboard-data"></i>
                </a>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link text-decoration-none d-inline-flex align-items-center px-3 py-2 text-secondary dropdown-toggle" 
                            type="button" 
                            id="userDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        {{ Auth::user()->name }}
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="bi bi-box-arrow-right me-2"></i>Log Out
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <span class="dropdown-item-text small text-muted">
                                Logged in as {{ Auth::user()->roles[0]->name ?? 'Admin' }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<div class="col-auto sidebar ms-3 rounded shadow border" id="sidebar" style="z-index: 9999;">
    <div class="nav-item" onclick="toggleSidebar()">
        <i class="bi bi-list nav-icon"></i>
        <span class="nav-label">Menu</span>
    </div>

    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-house nav-icon"></i>
        <span class="nav-label">Home</span>
    </a>

    <a href="{{ route('admin.exams.index') }}" class="nav-item {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text nav-icon"></i>
        <span class="nav-label">Exams</span>
    </a>

    <a href="{{ route('admin.manage-classes.index') }}" class="nav-item {{ request()->routeIs('admin.manage-classes.*') ? 'active' : '' }}">
        <i class="bi bi-book nav-icon"></i>
        <span class="nav-label">Manage Classes</span>
    </a>

    <a href="{{ route('admin.manage-subject.index') }}" class="nav-item {{ request()->routeIs('admin.manage-subject.*') ? 'active' : '' }}">
        <i class="bi bi-journal-text nav-icon"></i>
        <span class="nav-label">Manage Subject</span>
    </a>

    <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="bi bi-people nav-icon"></i>
        <span class="nav-label">Manage User</span>
    </a>

    <button type="button" class="nav-item mt-auto mb-3" style="width: 100%;" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="bi bi-power nav-icon"></i>
        <span class="nav-label">Logout</span>
    </button>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #6b9aac 0%, #7ca5b8 100%); color: white; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="logoutModalLabel">
                    <i class="bi bi-box-arrow-right me-2"></i>Confirm Logout
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="text-center mb-3">
                    <i class="bi bi-question-circle-fill" style="font-size: 3rem; color: #7ca5b8;"></i>
                </div>
                <p class="text-center mb-0" style="font-size: 1.1rem; color: #374151;">
                    Are you sure you want to log out?
                </p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 1rem 1.5rem;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    Cancel
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmLogout()" style="border-radius: 8px; padding: 0.5rem 1.5rem; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none;">
                    <i class="bi bi-power me-1"></i>Log Out
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden logout form -->
<form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
    @csrf
</form>

<!-- Scripts -->
<script>
    function confirmLogout() {
        document.getElementById('logout-form').submit();
    }
    
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const main = document.getElementById('mainContent');
        sidebar.classList.toggle('expanded');
        if (main) {
            main.classList.toggle('expanded');
        }
    }
</script>