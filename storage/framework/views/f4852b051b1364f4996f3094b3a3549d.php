<?php $__env->startSection('content'); ?>
    <div class="login d-flex justify-content-center align-items-center">
        <div class="row py-5 d-flex justify-content-center align-items-center w-100">
            <div class="col-12 col-md-10 col-lg-5">
                <div class="container">
                    <form method="POST" action="<?php echo e(route('admin.login')); ?>">
                        <?php echo method_field('POST'); ?>
                        <?php echo csrf_field(); ?>
                        <div class="card shadow p-3">
                            <div class="card-header bg-transparent py-2 border-0">
                                <div class="d-flex justify-content-between align-items-center">    
                                    <div class="mb-3 logo">
                                        <img src="<?php echo e(asset('/img/' . ($provider['client_logo'] ?? 'client-logo.png'))); ?>" style="position: relative; <?php echo e(config('app.product') == 'government' ? 'left: -20px' : ''); ?>">
                                    </div>
                                    <ul class="mb-3 nav nav-pills" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button type="button" class="nav-link active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab" aria-controls="pills-login" aria-selected="true">Admin Login</button>
                                        </li>
                                    </ul> 
                                </div>                 
                                <div class="note mt-4 mb-3">
                                    By signing in, you agree to the <?php echo e(($provider['company'] ?? 'Novulutions Inc.')); ?> HRIS Terms of Service and acknowledge our Cookie and Privacy Policies. This platform is intended for administrators to securely manage employee records, oversee job and payroll data, and perform other HR-related functions in line with company policies and applicable regulations.
                                </div>                
                            </div>
                            <hr class="my-2">
                            <div class="card-body">
                                <div class="row">
                                    <?php if(session()->has('error')): ?>
                                        <div class="alert alert-danger mb-3 text-uppercase fw-medium text-center fs-6"><?php echo e(session('error')); ?></div>
                                    <?php endif; ?>
                                    <div class="col-12 mb-3">
                                        <label for="email" class="mb-2">Login <span class="text-danger">*</span></label>
                                        <input type="text" name="email" id="email" class="form-control" placeholder="Username or Email">
                                        <div class="error-field mt-1">
                                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="password" class="mb-2">Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" autocomplete="off">
                                        <div class="error-field mt-1">
                                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 d-flex gap-2 justify-content-end">
                                <button class="btn btn-primary px-5 py-3 text-uppercase fw-bold">Proceed</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', [
    'title' => 'HRIS | Admin Login'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/auth/admin/login.blade.php ENDPATH**/ ?>