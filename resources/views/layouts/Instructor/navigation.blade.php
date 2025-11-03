<!-- Styles -->
<style>
    /* Navbar fixes */
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 1020;
    }

    .notification-badge {
        font-size: 0.65rem;
    }

    /* Ensure dropdowns appear above other content */
    .dropdown-menu {
        z-index: 1030;
    }

    /* Custom dropdown styling */
    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: #f8f9fa;
    }

    /* Mobile menu styles */
    @media (max-width: 640px) {
        .sm\\:hidden {
            display: none !important;
        }
    }

    @media (min-width: 640px) {
        .hidden.sm\\:flex {
            display: flex !important;
        }
    }

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
<nav class="bg-white border-b border-gray-100 sticky-top">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="text-dark fs-2">
                    <i class="bi bi-clipboard-data"></i>
                </a>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <!-- Notification Bell -->
                <div class="dropdown">
                    <button class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none transition dropdown-toggle" 
                            type="button" 
                            id="notificationDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false"
                            data-bs-auto-close="outside">
                        <i class="bi bi-bell text-xl"></i>
                        @php
                            $unreadCount = Auth::user()->notifications()->unread()->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Notification Dropdown -->
                    <div class="dropdown-menu dropdown-menu-end shadow-lg border" 
                         aria-labelledby="notificationDropdown"
                         style="width: 320px; max-height: 400px; overflow-y: auto;">
                        
                        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-semibold">Notifications</h6>
                            @if($unreadCount > 0)
                                <a href="{{ route('instructor.notifications.mark-all-read') }}" 
                                   class="text-decoration-none small text-primary"
                                   onclick="event.preventDefault(); markAllAsRead();">
                                    Mark all as read
                                </a>
                            @endif
                        </div>

                        <div style="max-height: 300px; overflow-y: auto;">
                            @php
                                $notifications = Auth::user()->notifications()->latest()->limit(5)->get();
                            @endphp
                            
                            @forelse($notifications as $notification)
                                <a href="{{ route('instructor.notifications.show', $notification->notification_id) }}" 
                                   class="dropdown-item border-bottom py-3 {{ !$notification->is_read ? 'bg-light' : '' }}"
                                   onclick="event.preventDefault(); markAsReadAndRedirect({{ $notification->notification_id }}, '{{ $notification->data['url'] ?? route('dashboard') }}');">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="flex-shrink-0">
                                            @if($notification->type === 'exam_approved')
                                                <div class="rounded-circle bg-success bg-opacity-10 p-2">
                                                    <i class="bi bi-check-circle text-success"></i>
                                                </div>
                                            @elseif($notification->type === 'exam_rejected')
                                                <div class="rounded-circle bg-danger bg-opacity-10 p-2">
                                                    <i class="bi bi-x-circle text-danger"></i>
                                                </div>
                                            @elseif($notification->type === 'collaborator_added')
                                                <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                                                    <i class="bi bi-people text-primary"></i>
                                                </div>
                                            @else
                                                <div class="rounded-circle bg-secondary bg-opacity-10 p-2">
                                                    <i class="bi bi-info-circle text-secondary"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1 small fw-semibold text-dark">{{ $notification->title }}</p>
                                            <p class="mb-1 small text-muted">{{ $notification->message }}</p>
                                            <p class="mb-0 small text-muted">{{ $notification->time_ago }}</p>
                                        </div>
                                        @if(!$notification->is_read)
                                            <div class="flex-shrink-0">
                                                <span class="badge bg-primary rounded-circle p-1" style="width: 8px; height: 8px;"></span>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="p-4 text-center text-muted">
                                    <i class="bi bi-bell-slash fs-1 mb-2 d-block"></i>
                                    <p class="small mb-0">No notifications yet</p>
                                </div>
                            @endforelse
                        </div>

                        @if($notifications->count() > 0)
                            <div class="p-2 border-top text-center">
                                <a href="{{ route('instructor.notifications.index') }}" class="text-decoration-none small text-primary">
                                    View all notifications
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

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
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i>Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i>Log Out
                                </button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <span class="dropdown-item-text small text-muted">
                                Logged in as {{ Auth::user()->roles[0]->name }}
                            </span>
                        </li>
                    </ul>
                </div>
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
                const bellButton = document.getElementById('notificationDropdown');
                
                if (data.count > 0) {
                    if (badge) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                    } else if (bellButton) {
                        // Create badge if it doesn't exist
                        const newBadge = document.createElement('span');
                        newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge';
                        newBadge.textContent = data.count > 99 ? '99+' : data.count;
                        bellButton.appendChild(newBadge);
                    }
                } else if (badge) {
                    badge.remove();
                }
            })
            .catch(error => console.error('Error fetching notification count:', error));
    }, 30000);

    // Ensure Bootstrap dropdowns work properly
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all dropdowns
        var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });
    });
</script>