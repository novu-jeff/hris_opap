<div>
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
                            <div class="col-md-12 mb-4">
                                <label class="mb-2" for="type">Section <span class="text-danger">*</span></label>
                               <input type="text" wire:model="section" class="form-control text-uppercase"  disabled>
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['section'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="mb-2" for="type">Department <span class="text-danger">*</span></label>
                               <input type="text" class="form-control text-uppercase" wire:model="department" disabled>
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['department'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="mb-2" for="type">Branch <span class="text-danger">*</span></label>
                               <input type="text" class="form-control text-uppercase" wire:model="branch" disabled>
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['branch'];
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
                        <div class="row">
                            <div class="col-md-3 mb-4">
                                <label class="mb-2" for="type">Lastname <span class="text-danger">*</span></label>
                               <input type="text" class="form-control text-uppercase" wire:model="lastname" disabled>
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['lastname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="mb-2" for="type">Firstname <span class="text-danger">*</span></label>
                               <input type="text" class="form-control text-uppercase" wire:model="firstname" disabled>
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['firstname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="mb-2" for="type">M.I. <span class="text-danger">*</span></label>
                               <input type="text" class="form-control text-uppercase" wire:model="middlename" disabled>
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['middlename'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="mb-2" for="type">Position <span class="text-danger">*</span></label>
                               <input type="text" class="form-control text-uppercase" wire:model="position" disabled>
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['position'];
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

                        <hr class="mb-4">  

                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="mb-2" for="type">Date Filed <span class="text-danger">*</span></label>
                                       <input type="date" class="form-control text-uppercase" wire:model="date_filed">
                                        <div class="error-field">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date_filed'];
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
                            <div class="col-md-6 mb-4">
                                <label class="mb-2" for="type">Destination <span class="text-danger">*</span></label>
                                <textarea wire:model="destination" id="destination" cols="30" rows="5" class="form-control text-uppercase" placeholder="Write something..."></textarea>
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['destination'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="mb-2" for="type">Purpose <span class="text-danger">*</span></label>
                                <textarea wire:model="purpose" id="purpose" cols="30" rows="5" class="form-control text-uppercase" placeholder="Write something..."></textarea>
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['purpose'];
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
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="mb-2" for="type">Departure Time <span class="text-danger">*</span></label>
                               <input type="text" class="timepicker form-control" wire:model="departure_time">
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['departure_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="mb-2" for="type">Arrival Time <span class="text-danger">*</span></label>
                               <input type="text" class="timepicker form-control" wire:model="arrival_time">
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['arrival_time'];
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
                        <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">Proceed <i class="fa-solid fa-arrow-right ms-1"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>    
</div>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/employee/business-slip/apply.blade.php ENDPATH**/ ?>