<div>
    <form wire:submit.prevent="save">
        <div class="card mb-4 border-0">
            <div class="card-body px-4">
                <!--[if BLOCK]><![endif]--><?php if(is_null($records['employee_account']['email_id'])): ?>
                    <div class="alert alert-primary text-center text-uppercase fw-bold">Please complete the required personal information before proceeding with account setup.</div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-12 col-md-12 mb-3">
                            <label class="mb-2" for="records.employee_account.email_id">Email ID</label>
                            <input type="email" class="form-control restricted" wire:model="records.employee_account.email_id" placeholder="System Generated" readonly>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_account.email_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mb-3">
                            <label class="mb-2" for="records.employee_account.personal_email">Personal Email</label>
                            <input type="email" class="form-control text-lowercase" wire:model="records.employee_account.personal_email">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_account.personal_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mb-3">
                            <label class="mb-2" for="records.employee_account.password">Password <span class="text-danger">*</span></label>
                            <input type="password" wire:model="records.employee_account.password" id="records.employee_account.password" class="form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_account.password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mb-3">
                            <label class="mb-2" for="records.employee_account.confirm_password">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" wire:model="records.employee_account.confirm_password" id="records.employee_account.confirm_password" class="form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_account.confirm_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mb-3">
                            <label class="mb-2" for="records.employee_account.notify_user">Notify User</label>
                            <input type="checkbox" wire:model="records.employee_account.notify_user" id="records.employee_account.notify_user" class="form-check-input ms-1">
                            <p class="text-uppercase fw-bold text-muted" style="font-size:10px">By Checking this, we will send a notification to the employee's personal email associated with their updated password.</p>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_account.notify_user'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                    <span class="text-danger"><?php echo e($message); ?></span> 
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>   
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
        <!--[if BLOCK]><![endif]--><?php if(!empty($records) && !is_null($records['employee_account']['email_id'])): ?>
            <hr class="mb-4">
            <div class="card-footer d-flex justify-content-end bg-transparent border-0">
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                        <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                    </button>
                    <div class="mt-3 pb-5">
                        <!--[if BLOCK]><![endif]--><?php if($errors->any()): ?>
                            <small class="text-danger">There's an error upon submitting, please review your form.</small>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </form>
</div>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/hris/profile/account.blade.php ENDPATH**/ ?>