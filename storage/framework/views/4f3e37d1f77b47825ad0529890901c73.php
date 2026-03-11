<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center my-5">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header pt-4 px-4 bg-transparent border-0">
                    <h6 class="text-uppercase fw-bold mb-0"><?php echo e(__('Reset Password')); ?></h6>
                    <p class="mt-2 text-muted mb-0">Please provide the email that's associated to your account to perform recovery.</p>
                    <hr>
                </div>
                <div class="card-body px-4">
                    
                    <?php if(session('status')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('status')); ?>

                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('password.email')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="form-group mb-3">
                            <label for="email" class="mb-2"><?php echo e(__('Email Address')); ?></label>
                            <input id="email" type="email" 
                                   class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   name="email" value="<?php echo e(old('email')); ?>" placeholder="hello@peace.gov.ph">
                            <div class="error mt-2">
                                <?php $__errorArgs = ['email'];
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

                        <div class="form-group mb-0 d-flex gap-3 justify-content-end mt-5 mb-3">
                            <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                                <?php echo e(__('Proceed')); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/auth/passwords/email.blade.php ENDPATH**/ ?>