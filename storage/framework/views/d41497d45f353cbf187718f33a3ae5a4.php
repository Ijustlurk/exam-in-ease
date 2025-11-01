

<?php $__env->startSection('content'); ?>
<style>
    .notifications-content {
        margin-left: 60px;
        transition: margin-left 0.3s;
        padding: 24px;
        min-height: 100vh;
    }
    
    .notifications-content.expanded {
        margin-left: 220px;
    }
    
    .notification-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        border: 1px solid #e5e7eb;
        transition: all 0.2s;
    }
    
    .notification-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .notification-card.unread {
        background: #f0f9ff;
        border-color: #7dd3fc;
    }
    
    .notification-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .notification-content {
        flex: 1;
    }
    
    @media (max-width: 768px) {
        .notifications-content {
            margin-left: 0;
            padding: 16px;
        }
        .notifications-content.expanded {
            margin-left: 0;
        }
    }
</style>

<div class="notifications-content">
    <div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">All Notifications</h2>
        <?php if($notifications->where('is_read', false)->count() > 0): ?>
            <button onclick="markAllAsRead()" class="btn btn-sm btn-outline-primary">
                Mark all as read
            </button>
        <?php endif; ?>
    </div>

    <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="notification-card <?php echo e(!$notification->is_read ? 'unread' : ''); ?>">
            <div class="d-flex gap-3">
                <div class="notification-icon 
                    <?php if($notification->type === 'exam_approved'): ?> bg-success-subtle text-success
                    <?php elseif($notification->type === 'exam_rejected'): ?> bg-danger-subtle text-danger
                    <?php elseif($notification->type === 'collaborator_added'): ?> bg-info-subtle text-info
                    <?php else: ?> bg-secondary-subtle text-secondary
                    <?php endif; ?>">
                    <?php if($notification->type === 'exam_approved'): ?>
                        <i class="bi bi-check-circle-fill"></i>
                    <?php elseif($notification->type === 'exam_rejected'): ?>
                        <i class="bi bi-x-circle-fill"></i>
                    <?php elseif($notification->type === 'collaborator_added'): ?>
                        <i class="bi bi-people-fill"></i>
                    <?php else: ?>
                        <i class="bi bi-info-circle-fill"></i>
                    <?php endif; ?>
                </div>
                
                <div class="notification-content">
                    <h5 class="mb-2"><?php echo e($notification->title); ?></h5>
                    <p class="mb-2 text-muted"><?php echo e($notification->message); ?></p>
                    <small class="text-muted"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                    
                    <?php if(isset($notification->data['url'])): ?>
                        <div class="mt-3">
                            <a href="<?php echo e($notification->data['url']); ?>" class="btn btn-sm btn-primary">
                                View Details
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if(!$notification->is_read): ?>
                    <div class="flex-shrink-0">
                        <button onclick="markAsRead(<?php echo e($notification->notification_id); ?>)" 
                                class="btn btn-sm btn-outline-secondary">
                            Mark as read
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center py-5">
            <i class="bi bi-bell-slash" style="font-size: 4rem; color: #d1d5db;"></i>
            <p class="text-muted mt-3">No notifications yet</p>
        </div>
    <?php endif; ?>

    <div class="mt-4">
        <?php echo e($notifications->links()); ?>

    </div>
    </div>
</div>

<script>
    function markAsRead(notificationId) {
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
                location.reload();
            }
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
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.Instructor.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\exam1\resources\views/instructor/notifications/index.blade.php ENDPATH**/ ?>