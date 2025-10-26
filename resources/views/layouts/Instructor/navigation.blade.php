<!-- Styles -->
<style>
    .sidebar {
        background-color: rgb(207, 218, 225);
        height: calc(100vh - 90px);
        display: flex;
        flex-direction: column;
        padding-top: 1rem;
        transition: width 0.3s;
        width: 60px;
        overflow-x: hidden;
        position: fixed;
        top: 90px;
        left: 0;
        z-index: 999;
    }

    .sidebar.expanded {
        width: 220px;
    }

    .sidebar a,
    .sidebar button {
        text-decoration: none;
        color: #000;
        display: flex;
        align-items: center;
        width: 100%;
        background: none;
        border: none;
        text-align: left;
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

    .sidebar .nav-item.active * {
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

    .no-sidebar .main {
        margin-left: 0;
    }
</style>

<!-- Top Navbar -->
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky-top">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="text-dark fs-2">
                    <i class="bi bi-clipboard-data"></i>
                </a>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <!-- Notification Bell -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none transition">
                        <i class="bi bi-bell text-xl"></i>
                        @php
                            $unreadCount = Auth::user()->notifications()->unread()->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                         style="display: none;">
                        
                        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                            <h4 class="font-semibold text-gray-800">Notifications</h4>
                            @if($unreadCount > 0)
                                <a href="{{ route('instructor.notifications.mark-all-read') }}" 
                                   class="text-xs text-blue-600 hover:text-blue-800"
                                   onclick="event.preventDefault(); markAllAsRead();">
                                    Mark all as read
                                </a>
                            @endif
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            @php
                                $notifications = Auth::user()->notifications()->latest()->limit(10)->get();
                            @endphp
                            
                            @forelse($notifications as $notification)
                                <a href="{{ route('instructor.notifications.show', $notification->notification_id) }}" 
                                   class="block p-3 hover:bg-gray-50 border-b border-gray-100 {{ !$notification->is_read ? 'bg-blue-50' : '' }}"
                                   onclick="event.preventDefault(); markAsReadAndRedirect({{ $notification->notification_id }}, '{{ $notification->data['url'] ?? route('dashboard') }}');">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            @if($notification->type === 'exam_approved')
                                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                    <i class="bi bi-check-circle text-green-600"></i>
                                                </div>
                                            @elseif($notification->type === 'exam_rejected')
                                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                    <i class="bi bi-x-circle text-red-600"></i>
                                                </div>
                                            @elseif($notification->type === 'collaborator_added')
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <i class="bi bi-people text-blue-600"></i>
                                                </div>
                                            @else
                                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <i class="bi bi-info-circle text-gray-600"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-800">{{ $notification->title }}</p>
                                            <p class="text-xs text-gray-600 mt-1">{{ $notification->message }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $notification->time_ago }}</p>
                                        </div>
                                        @if(!$notification->is_read)
                                            <div class="flex-shrink-0">
                                                <span class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center text-gray-500">
                                    <i class="bi bi-bell-slash text-4xl mb-2"></i>
                                    <p class="text-sm">No notifications yet</p>
                                </div>
                            @endforelse
                        </div>

                        @if($notifications->count() > 0)
                            <div class="p-3 border-t border-gray-200 text-center">
                                <a href="{{ route('instructor.notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                    View all notifications
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- User Dropdown -->
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

            <!-- Hamburger (mobile) -->
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

    <!-- Responsive Nav -->
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

<!-- Sidebar -->
@if(!request()->routeIs('instructor.exams.create'))
<div class="sidebar shadow border ms-3 rounded" id="sidebar">
    <button class="nav-item" onclick="toggleSidebar()">
        <i class="bi bi-list nav-icon"></i>
        <span class="nav-label">Menu</span>
    </button>

    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-house-door-fill nav-icon"></i>
        <span class="nav-label">Home</span>
    </a>

    <a href="{{ route('instructor.exam-statistics.index') }}" class="nav-item {{ request()->routeIs('instructor.exam-statistics.*') ? 'active' : '' }}">
        <i class="bi bi-graph-up-arrow nav-icon"></i>
        <span class="nav-label">Exam Statistics</span>
    </a>
{{-- 
    <a href="{{ route('instructor.classes.index') }}" class="nav-item {{ request()->routeIs('instructor.classes.*') ? 'active' : '' }}">
        <i class="bi bi-journal-text nav-icon"></i>
        <span class="nav-label">Your Classes</span>
    </a> --}}

    <a href="{{ route('profile.edit') }}" class="nav-item mt-auto mb-3 {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <i class="bi bi-gear-fill nav-icon"></i>
        <span class="nav-label">Account Options</span>
    </a>
</div>
@endif

<!-- Main Content -->
<div class="main-content {{ request()->routeIs('instructor.exams.create') ? 'no-sidebar' : '' }}" id="mainContent">
    @yield('main-content')
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const main = document.getElementById('mainContent');
        const examContent = document.querySelector('.exam-content');
        const notificationsContent = document.querySelector('.notifications-content');
        console.log('Toggle sidebar called');
        console.log('Sidebar:', sidebar);
        console.log('Main:', main);
        console.log('Exam Content:', examContent);
        console.log('Notifications Content:', notificationsContent);
        
        sidebar.classList.toggle('expanded');
        if (main) {
            main.classList.toggle('expanded');
            console.log('Main classes after toggle:', main.className);
        }
        if (examContent) {
            examContent.classList.toggle('expanded');
            console.log('Exam content classes after toggle:', examContent.className);
        }
        if (notificationsContent) {
            notificationsContent.classList.toggle('expanded');
            console.log('Notifications content classes after toggle:', notificationsContent.className);
        }
    }

    function markAsReadAndRedirect(notificationId, url) {
        fetch(`/instructor/notifications/${notificationId}/mark-as-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = url;
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            window.location.href = url;
        });
    }

    function markAllAsRead() {
        fetch('/instructor/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error marking all as read:', error);
        });
    }

    // Poll for new notifications every 30 seconds
    setInterval(() => {
        fetch('/instructor/notifications/unread/count')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.notification-badge');
                if (data.count > 0) {
                    if (badge) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                    }
                } else if (badge) {
                    badge.remove();
                }
            })
            .catch(error => console.error('Error fetching notification count:', error));
    }, 30000);
</script>