<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center my-5">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header pt-4 px-4 bg-transparent border-0">
                    <h6 class="text-uppercase fw-bold mb-0"><?php echo e(__('Change Password')); ?></h6>
                    <p class="mt-2 text-muted mb-0">Please provide a new and strong password.</p>
                    <hr>
                </div>
                <div class="card-body px-4">
                    <?php if(!is_null($token)): ?>
                        <form method="POST" action="<?php echo e(route('password.update')); ?>">
                            <?php echo csrf_field(); ?>

                            <input type="hidden" name="token" value="<?php echo e($token); ?>">
                            <input type="hidden" name="email" value="<?php echo e($email); ?>">

                            <div class="mb-3">
                                <label for="password" class="form-label"><?php echo e(__('New Password')); ?></label>
                                <input id="password" type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" placeholder="••••••••">
                                <div class="error mt-2">
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-danger" role="alert"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password-confirm" class="form-label"><?php echo e(__('Confirm Password')); ?></label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="••••••••">
                            </div>

                            <div class="mt-5 mb-4">
                                <button type="submit" class="w-100 btn btn-primary px-5 py-3 text-uppercase fw-bold">
                                    <?php echo e(__('Reset Password')); ?>

                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info text-uppercase fw-bold text-center"><?php echo e(__('auth.passwords.token')); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/auth/passwords/reset.blade.php ENDPATH**/ ?>