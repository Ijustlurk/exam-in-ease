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
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky-top">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <i class="bi bi-clipboard-data text-dark fs-2"></i>
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="d-flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>

                        <x-dropdown-link>
                            Logged in as {{ Auth::user()->roles[0]->name }}
                        </x-dropdown-link>
                    </x-slot>
                </x-dropdown>
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

    <a href="{{ route('admin.exam-statistics.index') }}" class="nav-item {{ request()->routeIs('admin.exam-statistics.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-line nav-icon"></i>
        <span class="nav-label">Exam Statistics</span>
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

    <a href="{{ route('profile.edit') }}" class="nav-item mt-auto mb-3 {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <i class="bi bi-person-circle nav-icon"></i>
        <span class="nav-label">Account Options</span>
    </a>
</div>

<!-- Scripts -->
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const main = document.getElementById('mainContent');
        sidebar.classList.toggle('expanded');
        if (main) {
            main.classList.toggle('expanded');
        }
    }
</script>