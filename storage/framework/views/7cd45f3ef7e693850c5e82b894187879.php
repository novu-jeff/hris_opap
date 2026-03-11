<form wire:submit.prevent='login'>
    <div class="card shadow p-3">
        <div class="card-header bg-transparent py-2 border-0">
            <div class="d-lg-flex justify-content-between align-items-center">  
                <div class="logo">
                    <img src="<?php echo e(asset('/img/' . $provider['client_logo'])); ?>" style="position: relative; <?php echo e(config('app.product') == 'government' ? 'left: -20px' : ''); ?>">
                </div>
                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab" aria-controls="pills-login" aria-selected="true">Applicant Login</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="<?php echo e(route('home.register')); ?>" class="nav-link">Register</a>
                    </li>
                </ul>   
            </div>                 
            <div class="note mt-4 mb-3">
                By creating an account or signing in, you agree to the <?php echo e($provider['company']); ?> HRIS Terms of Service and acknowledge our Cookie and Privacy Policies. This system is intended to securely manage your employment records, including personal information, job details, payroll, and other HR-related services, in accordance with our policies and applicable regulations.
            </div>                
        </div>
        <hr class="my-2 mx-3">
        <div class="card-body">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="pills-login" tabindex="0">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="email" class="mb-2">Email <span class="text-danger">*</span></label>
                            <input type="text" wire:model="email" id="email" class="form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="password" class="mb-2">Password <span class="text-danger">*</span></label>
                            <input type="password" wire:model="password" id="password" class="form-control" placeholder="Enter Password" autocomplete="off">
                            <div class="error-field">
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
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 d-flex gap-2 justify-content-end pb-3">
            <button class="btn btn-primary px-5 py-3 text-uppercase fw-bold">Proceed</button>
        </div>
    </div>
</form><?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/home/login.blade.php ENDPATH**/ ?>