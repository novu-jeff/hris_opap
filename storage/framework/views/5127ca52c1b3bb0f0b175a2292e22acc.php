<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-12">
            <div class="card shadow p-4">
                
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">
                        All <span class="text-danger">*</span> is required
                    </p>
                </div>
                <hr class="mx-3">

                
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" id="name" class="form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="eligible">Eligible <span class="text-danger">*</span></label>
                            <select wire:model="eligible" id="eligible" class="form-select">
                            <option value=""> - CHOOSE - </option>

                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $employmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type->id); ?>">
                                        <?php echo e($type->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['eligible'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                      
                    <div class="col-12 col-md-6 mb-4">
                    <label class="mb-2">Tranche Year <span class="text-danger">*</span></label>

                    
                    <div class="form-control bg-light">
                        <?php echo e($year); ?>

                    </div>

                    
                    <input type="hidden" wire:model="year">

                    <div class="error-field">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>



                        
                        <div class="col-12 col-md-6 mb-4 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" wire:model="is_active" id="is_active">
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>

                        
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="file">File </label>
                            <input type="file" wire:model="file" id="file" class="form-control">
                            <div class="mt-2">
                                <small class="text-muted text-uppercase">(only accepts csv file)</small>
                            </div>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        
                        <!--[if BLOCK]><![endif]--><?php if($records): ?>
                            <div class="col-12 mb-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Salary Grade</th>
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = range(1,8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <th>Step <?php echo e($i); ?></th>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($data['salary_grade']); ?></td>
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = range(1,8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <td>
                                                            <div class="border rounded p-2">
                                                                <label class="form-label mb-1 small text-muted">Salary</label>
                                                                <input type="text" wire:model="records.<?php echo e($key); ?>.step_<?php echo e($i); ?>"
                                                                    class="form-control form-control-sm mb-2">

                                                                <label class="form-label mb-1 small text-muted">WTAX</label>
                                                                <input type="text" wire:model="records.<?php echo e($key); ?>.step_<?php echo e($i); ?>_wtax"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                        </td>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <hr class="mx-3">

                
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="save">
                            Save <i class="fa-solid fa-arrow-right ms-2"></i>
                        </span>
                        <span wire:loading wire:target="save">
                            Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/settings/tranches/edit.blade.php ENDPATH**/ ?>