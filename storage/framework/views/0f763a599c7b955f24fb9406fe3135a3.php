<div class="notification d-none d-md-block" wire:poll="loadNotifications" wire:poll.keep-alive wire:visible>
    <div wire:click="toggle" data-bs-toggle="tooltip" title="Notification">
        <i class="fa-regular fa-bell"></i>
        <!--[if BLOCK]><![endif]--><?php if($notifications['unread'] > 0): ?>
            <span class="count"><?php echo e($notifications['unread']); ?></span>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
    <div class="content <?php echo e($isOpened ? 'd-block' : 'd-none'); ?>">
        <div class="header d-flex align-items-center justify-content-between">
            <div class="d-lg-flex justify-content-between align-items-center">
                <div>
                    <h6 class="m-0">All Notifications</h6>
                </div>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <!--[if BLOCK]><![endif]--><?php if(!$isShowSearch): ?>
                    <button class="btn btn-primary" wire:click="toggleSearch">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 16px"></i>
                    </button>
                    <!--[if BLOCK]><![endif]--><?php if($notifications['unread'] > 0): ?> 
                        <button wire:click="markAsRead" class="btn btn-info mb-0 px-3 fw-bold" style="font-size:12px">Mark as Read</button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div class="overlay close d-lg-none">
                        <i class="fa-solid fa-xmark" wire:click="toggle"></i>
                    </div>
                <?php else: ?>
                    <input type="text" wire:model="search_param" id="search" class="form-control">
                    <div class="d-flex align-items-center gap-1">
                        <button class="btn btn-primary" wire:click="search">
                            <i class="fa-solid fa-magnifying-glass" style="font-size: 16px"></i>
                        </button>
                        <button class="btn btn-primary" wire:click="toggleSearch">
                            <i class="fa-solid fa-xmark" style="font-size: 16px"></i>
                        </button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
        <div class="scrollable" id="notificationList">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $notifications['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $data = json_decode($item['data'], true);
                ?>
                <div wire:click="read('<?php echo e($item['id']); ?>', '<?php echo e($data['redirect']); ?>')" class="item nav-link <?php echo e(is_null($item['read_at']) ? 'active' : ''); ?>">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon">
                            <!--[if BLOCK]><![endif]--><?php if($data['type'] == 'info'): ?>
                                <i class="fa-regular fa-lightbulb" style="color: #d3b404"></i>
                            <?php elseif($data['type'] == 'success'): ?>
                                <i class="fa-regular fa-thumbs-up" style="color: #225F8B"></i>
                            <?php elseif($data['type'] == 'error'): ?>
                                <i class="fa-solid fa-triangle-exclamation" style="color: #dc3545"></i>
                            <?php elseif($data['type'] == 'message'): ?>
                                <i class="fa-regular fa-message" style="color: #225F8B"></i>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="message">
                            <div> <?php echo $data['message']; ?></div>
                            <small class="text-muted fw-medium">(click this notification to view more details)</small>
                        </div>
                    </div>
                    <div class="timestamp">
                        <small><?php echo e(\Carbon\Carbon::parse($item['created_at'])->diffForHumans()); ?></small>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-uppercase text-center p-3 mt-4">No notifications available.</p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    <video id="notification-video" controls width="400" class="d-none">
        <source src="<?php echo e(asset('sounds/notification.mp3')); ?>" type="video/mp4">
        Your browser does not support the video element.
    </video>      
</div>

<?php $__env->startSection('script'); ?>
<script>
  $(function() {

        var notificationList = $('#notificationList');
        notificationList.on('scroll', function() {
            if (notificationList.scrollTop() + 300) {
                Livewire.dispatch('loadNotifications');
            }
        });

        // Livewire.on('notify', function(event) {
        //     const videoPlayer = document.getElementById('notification-video'); // Select the <video> element
        //     videoPlayer.play().catch((error) => {
        //         console.error('Video playback failed:', error);
        //     });
        // });

        Livewire.on('refreshPage', () => {
            location.reload();  
        });

    });

</script>
<?php $__env->stopSection(); ?>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/notifications.blade.php ENDPATH**/ ?>