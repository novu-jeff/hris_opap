<div>
    <!--[if BLOCK]><![endif]--><?php if($isNewEmployee || $isForcedEmployee): ?>
    <div class="change-password">
        <div class="content" wire:ignore.self>
            <div class="card px-3 py-3 shadow rounded-4">
                <div class="card-header pt-3 border-0 bg-transparent">
                    <h4 class="mt-4 mb-3 fw-bold">Hello <span><?php echo e(ucfirst($name)); ?></span>,</h4>
                    <p class="text-justify mb-0">
                        <!--[if BLOCK]><![endif]--><?php if($isNewEmployee): ?>
                            As part of our commitment to ensuring the security of your personal information, we kindly request that you update your password before accessing the employee self-service portal. This step is essential to protect your account and maintain the integrity of our system. Thank you for your cooperation and understanding.
                        <?php elseif($isForcedEmployee): ?>
                            Your account password has been already exceeded for 30 days. As part of our commitment to security, you are required to update your password before accessing the employee self-service portal. This step is essential to protect your account and maintain the integrity of our system. Thank you for your cooperation and understanding.
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </p>
                </div>
                <hr>
                <div class="card-body">
                    <form wire:submit.prevent="save" wire:target="save">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="mb-2" for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" wire:model="password" placeholder="••••••••">
                                <div class="error-field mt-2">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="mb-2" for="confirm-password">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" wire:model="confirm_password" placeholder="••••••••">
                                <div class="error-field mt-2">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['confirm_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                                <span wire:loading.remove wire:target="save">Change Password</span>
                                <span wire:loading wire:target="save">Changing <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>

<!--[if BLOCK]><![endif]--><?php if($isNewEmployee || $isForcedEmployee): ?>
<?php $__env->startSection('script'); ?>
<script>
    $(function() {
        $('body').addClass('overflow-hidden');
        setTimeout(() => {
            $('.change-password .content').fadeIn();
        }, 200);
    });
</script>
<?php $__env->stopSection(); ?>
<?php endif; ?><!--[if ENDBLOCK]><![endif]--><?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/employee/new-employee.blade.php ENDPATH**/ ?>