<?php $__env->startSection('content'); ?>
    <div class="login">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-10 col-lg-5">
                <div class="container">
                    <form method="POST" action="<?php echo e(route('employee.login')); ?>">
                        <?php echo method_field('POST'); ?>
                        <?php echo csrf_field(); ?>
                        <div class="card shadow p-3">
                            <div class="card-header bg-transparent py-2 border-0">
                                <div class="d-lg-flex justify-content-between align-items-center">  
                                    <div class="logo" >
                                        <img src="<?php echo e(asset('/img/' . $provider['client_logo'])); ?>" style="position: relative; <?php echo e(config('app.product') == 'government' ? 'left: -20px' : ''); ?>">
                                    </div>
                                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button type="button" class="nav-link active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab" aria-controls="pills-login" aria-selected="true">Employee Login</button>
                                        </li>
                                    </ul>   
                                </div>                 
                                <div class="note mt-4 mb-3">
                                    By signing in, you agree to the <?php echo e($provider['company']); ?> HRIS Terms of Service and acknowledge our Cookie and Privacy Policies. This system is designed to help you securely access and manage your employment records, including personal details, job information, payroll, and other HR services through employee self-service features, in accordance with company policies and applicable regulations.
                                </div>                
                            </div>
                            <hr class="my-2 mx-3">
                            <div class="card-body">
                                <div class="row">
                                    <?php if(session()->has('error')): ?>
                                        <div class="alert alert-danger mb-4 text-uppercase fw-bold text-center" style="font-size:12px;">
                                            <?php echo session('error'); ?>

                                        </div>
                                    <?php endif; ?>
                                    <div class="col-12 mb-3">
                                        <label for="email" class="mb-2">Login <span class="text-danger">*</span></label>
                                        <input type="text" name="email" id="email" class="form-control" value="<?php echo e(old('email')); ?>" placeholder="E-ID or E-No.">
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
                                <div class="text-uppercase fw-bold" style="font-size: 12px; letter-spacing: 1px;">
                                    Forgot Password? Click <a href="<?php echo e(route('password.request')); ?>">Here</a>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 d-flex gap-2 justify-content-end pb-3">
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
    'title' => 'HRIS | Employee Login'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/auth/employee/login.blade.php ENDPATH**/ ?>