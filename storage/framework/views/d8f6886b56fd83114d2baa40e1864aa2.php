<form wire:submit.prevent="save" wire:target="save">
    <div class="row">
        <div class="col-12">
            <div class="card shadow p-4">
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                </div>
                <hr class="mx-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="date">Date <span class="text-danger">*</span></label>
                            <input type="date" wire:model="date" id="date" class="form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2" for="clock_in">Clock In <span class="text-danger">*</span></label>
                            <input type="text" wire:model="clock_in" id="clock_in" class="timepicker form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['clock_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2" for="break_out">Lunch Out <span class="text-danger">*</span></label>
                            <input type="text" wire:model="break_out" id="break_out" class="timepicker form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['break_out'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2" for="break_in">Lunch In <span class="text-danger">*</span></label>
                            <input type="text" wire:model="break_in" id="break_in" class="timepicker form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['break_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2" for="clock_out">Clock Out <span class="text-danger">*</span></label>
                            <input type="text" wire:model="clock_out" id="clock_out" class="timepicker form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['clock_out'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="reason">Reason <span class="text-danger">*</span></label>
                            <textarea wire:model="reason" id="reason" cols="30" rows="10" class="form-control" placeholder="Write here..."></textarea>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="attachments">Attachments <span class="text-danger">*</span></label>
                            <div class="mt-1 mb-3">
                                <small class="text-muted fw-bold">Note: Please provide any supporting proof for the reason stated above.</small>
                            </div>
                            <input type="file" wire:model="attachments"   id="attachments" class="form-control mb-1" multiple>                            
                            <small class="text-muted fw-medium fst-italic"><span class="text-danger">*</span> Accepts multiple files</small>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['attachments'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <!--[if BLOCK]><![endif]--><?php if(!is_null($preview_attachments)): ?>
                                <div class="attachments mt-3">
                                    <ul class="list-unstyled text-uppercase mt-3">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $preview_attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="list-unstyled-item">
                                                <div class="d-flex align-items-center gap-2">
                                                    <a class="d-flex align-items-center text-decoration-none" href="<?php echo e(Storage::url($item['attachment'])); ?>" download>
                                                        <?php echo e($item['attachment']); ?>

                                                    </a>
                                                    <button type="button" class="btn text-danger" wire:click="removeAttachment(<?php echo e($item['id']); ?>)">
                                                        <i class="fa-solid fa-close"></i>
                                                    </button>
                                                </div>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </ul>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>
                <hr class="mx-3">
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="save">Proceed <i class="fa-solid fa-arrow-right ms-2"></i></span>
                        <span wire:loading wire:target="save">Proceeding <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>


    document.addEventListener('livewire:init', () => {
     

        Livewire.on('form-reset', () => {

   

            // Clear file input
            console.log('Resetting file input');
            const fileInput = document.getElementById('attachments');
            if (fileInput) {
                fileInput.value = '';
            }

        });
    });
</script>

<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/employee/time-adjustments/apply.blade.php ENDPATH**/ ?>