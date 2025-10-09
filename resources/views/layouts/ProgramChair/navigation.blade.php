<style>
    /* Sidebar */
    .sidebar {
        background-color: #cfdadf;
        display: flex;
        flex-direction: column;
        padding-top: 1rem;
        transition: width 0.3s;
        width: 60px;
        overflow-x: hidden;
        position: fixed;
        top: 90px;
        /* Corrected 'left' positioning to account for 'ms-3' in the HTML */
        left: 0;
        height: calc(100vh - 90px);
        z-index: 999;
        border-radius: 0.5rem;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar.sidebar-expanded {
        width: 220px;
    }

    /* FIX: Changed .sidebar-icon to .nav-icon */
    .nav-icon {
        font-size: 1.5rem;
    }

    /* FIX: Changed .sidebar-icon.active to .nav-icon.active */
    .nav-icon.active {
        color: #0d6efd;
    }

    /* FIX: Changed .sidebar-icon-container to .nav-item */
    .nav-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0.75rem 1rem;
        white-space: nowrap;
        transition: background 0.3s;
        cursor: pointer;
    }
    
    /* Ensure the anchor tags inside nav-item inherit the display properties */
    .nav-item a {
        display: flex;
        align-items: center;
        text-decoration: none; /* Add this if you want to remove underline from links */
        color: inherit; /* Ensure link color is inherited or set explicitly */
    }

    /* FIX: Changed .sidebar-icon-container:hover to .nav-item:hover */
    .nav-item:hover {
        background-color: #c0d8e2;
    }

    /* FIX: Changed .sidebar-label to .nav-label */
    .nav-label {
        display: none;
        font-size: 1rem;
    }

    /* FIX: Changed .sidebar.sidebar-expanded .sidebar-label to .sidebar.sidebar-expanded .nav-label */
    .sidebar.sidebar-expanded .nav-label {
        display: inline;
    }

    /* Content offset */
    .main-content {
        /* FIX: Adjusted for sidebar width (60px) + ms-3 (which is usually 1rem or 16px) */
        margin-left: 60px;
        transition: margin-left 0.3s;
        padding: 1rem;
    }

    .sidebar.sidebar-expanded~.main-content {
        /* FIX: Adjusted for expanded sidebar (220px) + ms-3 */
        margin-left: 220px;
    }
    
    /* Additional fix for Top Navbar to prevent content from scrolling under it */
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 1000;
    }
</style>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky-top">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="text-dark fs-2">
                    <i class="bi bi-clipboard-data"></i>
                </a>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                            {{ Auth::user()->name }}
                            <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>

                        <x-dropdown-link>Logged in as {{ Auth::user()->roles[0]->name }}</x-dropdown-link>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open"
                    class="p-2 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<div class="sidebar shadow border ms-3" id="sidebar">
    <button class="nav-item" onclick="toggleSidebar()">
        <i class="bi bi-list nav-icon"></i>
        <span class="nav-label">Menu</span>
    </button>

    <a href="{{ route('dashboard') }}" class="nav-item">
        <i class="bi bi-house nav-icon"></i>
        <span class="nav-label">Home</span>
    </a>

    <a href="{{ route('programchair.manage-approval.index') }}" class="nav-item">
        <i class="bi bi-journal-text nav-icon"></i>
        <span class="nav-label">Manage Approval</span>
    </a>

    <a href="{{ route('programchair.exam-statistics.index') }}" class="nav-item">
        <i class="bi bi-bar-chart-line nav-icon"></i>
        <span class="nav-label">Exam Statistics</span>
    </a>

    <button class="nav-item mt-auto mb-3" onclick="switchView('account', event)">
        <i class="bi bi-person-circle nav-icon"></i>
        <span class="nav-label">Account Options</span>
    </button>
</div>

<div class="main-content">
    @yield('main-content')
</div>

<script>
    function switchView(viewId, event) {
        const sections = ['home', 'statistics', 'classes', 'account'];
        sections.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.remove('visible');
        });
        const target = document.getElementById(viewId);
        if (target) target.classList.add('visible');

        // FIX: Changed .sidebar-icon to .nav-icon
        document.querySelectorAll('.nav-icon').forEach(icon => icon.classList.remove('active'));
        if (event) {
            // FIX: Ensure it selects the icon from the correct element (button/anchor) that was clicked
            const icon = event.currentTarget.querySelector('.nav-icon');
            if (icon) icon.classList.add('active');
        }
    }

    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('sidebar-expanded');
    }
</script>