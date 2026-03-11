<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-12">
            <div class="card shadow p-4">
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                </div>
                <hr class="mx-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12 mb-4">
                            <label class="mb-2" for="fields.clear_notification">Clear Notification <span class="text-danger">*</span></label>
                            <p class="text-uppercase fw-medium" style="font-size: 12px">
                                Specify a timeframe for automatically removing a notification once it has been marked as read, ensuring a balance between timely information management and user convenience.
                            </p>
                            <select wire:model="fields.clear_notification" id="fields.clear_notification" class="form-select">
                                <option value=""> - CHOOSE - </option>
        
                                <optgroup label="Hours">
                                    <option value="1">After 1 Hour</option>
                                    <option value="2">After 2 Hours</option>
                                    <option value="3">After 3 Hours</option>
                                    <option value="10">After 10 Hours</option>
                                    <option value="12">After 12 Hours</option>
                                    <option value="14">After 14 Hours</option>
                                </optgroup>

                                <optgroup label="Days">
                                    <option value="24">After 1 Day</option>
                                    <option value="48">After 2 Days</option>
                                    <option value="72">After 3 Days</option>
                                </optgroup>

                                <optgroup label="Months">
                                    <option value="730">After 1 Month</option>
                                    <option value="1460">After 2 Months</option>
                                    <option value="2190">After 3 Months</option>
                                </optgroup>
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.clear_notification'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mb-4">
                            <label class="mb-2" for="fields.change_password">Change Password <span class="text-danger">*</span></label>
                            <p class="text-uppercase fw-medium" style="font-size: 12px">
                                Define the timeframe within which an employee is required to update their password, helping maintain security while providing a smooth user experience.                            
                            </p>
                            <select wire:model="fields.change_password" id="fields.change_password" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <optgroup label="Months">
                                    <option value="730">After 1 Month</option>
                                    <option value="1460">After 2 Months</option>
                                    <option value="2190">After 3 Months</option>
                                    <option value="2920">After 4 Months</option>
                                    <option value="3650">After 5 Months</option>
                                    <option value="4380">After 6 Months</option>
                                </optgroup>
                                
                                <optgroup label="Years">
                                    <option value="52560">After 1 Year</option>
                                </optgroup>
                                
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.change_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mb-4">
                            <label class="mb-2" for="fields.reset_leave_credits">Reset Leave Credits <span class="text-danger">*</span></label>
                            <p class="text-uppercase fw-medium" style="font-size: 12px">
                                Define the Schedule for Leave Credit Reset
                            </p>
                            <select wire:model="fields.reset_leave_credits" id="fields.reset_leave_credits" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <optgroup label="Months">
                                    <option value="730">After 1 Month</option>
                                </optgroup>
                                <optgroup label="Years">
                                    <option value="52560">After 1 Year</option>
                                </optgroup>
                                
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.reset_leave_credits'];
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
                <hr class="mx-3">
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                        <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/settings/scheduler.blade.php ENDPATH**/ ?>