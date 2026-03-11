<div class="chat-area" wire:ignore.self style="display: flex; flex-direction: column; height: 100%;">
    <div class="chatbox" style="flex: 1 1 auto; display: flex; flex-direction: column;">
        <div class="modal-content" style="flex: 1 1 auto; display: flex; flex-direction: column;">

            <div class="msg-head p-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <img class="img-fluid" src="<?php echo e(asset('img/logo.png')); ?>" alt="user img" style="width: 40px;">
                    <div>
                        <h6 class="mb-0">Human Resources (HR)</h6>
                        <small>Administrator</small>
                    </div>
                </div>
            </div>

            <!-- ONLY THIS PART SHOULD AUTO-REFRESH -->
            <div class="modal-body msg-body" id="messagesContainer" wire:poll.3s="loadRecords">
                <ul class="list-unstyled mb-0">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <!--[if BLOCK]><![endif]--><?php if(!empty($message['message']) || !$message['attachments']->isEmpty()): ?>
                            <li class="<?php echo e($message['from_role'] === 'admin' ? 'sender' : 'reply'); ?> mb-2">
                                <?php if(!empty($message['message'])): ?>
                                    <p><?php echo e($message['message']); ?></p>
                                    <small class="text-muted">
                                        <!--[if BLOCK]><![endif]--><?php if($message['from_role'] != 'admin'): ?>
                                            <!--[if BLOCK]><![endif]--><?php if($message['isSeen']): ?>
                                                Seen at <?php echo e(format_date($message['created_at'], 'day_date_time_string')); ?>

                                            <?php else: ?>
                                                <?php echo e(relative_time($message['created_at'])); ?>

                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <?php echo e(relative_time($message['created_at'])); ?>

                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </small>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </li>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </ul>
            </div>

            <!-- DO NOT RE-RENDER BELOW -->
            <div class="send-box" wire:ignore>
                <textarea
                    id="message"
                    class="form-control"
                    placeholder="Type something..."
                ></textarea>

                <button class="btn btn-primary" type="button" wire:click="send">
                    <i class="fa fa-paper-plane"></i>
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    document.querySelector('.send-box button').addEventListener('click', function () {
        window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('message', document.getElementById('message').value);
    });

    document.addEventListener('DOMContentLoaded', function() {
    const sendBtn = document.querySelector('.send-box button');
    const textarea = document.getElementById('message');

    if (sendBtn) {
        sendBtn.addEventListener('click', function () {
            window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('message', textarea.value);

            // Clear textarea IMMEDIATELY
            textarea.value = '';
        });
    }

 
    
    });


</script>

<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/employee/request-status.blade.php ENDPATH**/ ?>