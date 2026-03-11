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
                        <label class="mb-2" for="eligible">
                            Eligible <span class="text-danger">*</span>
                        </label>

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
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                        
            <div class="col-12 col-md-6 mb-4">
                <label class="mb-2" for="year">
                    Tranche Year <span class="text-danger">*</span>
                </label>

                <select wire:model="year" id="year" class="form-select">
                    <option value=""> - CHOOSE YEAR - </option>

                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($yr); ?>"><?php echo e($yr); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>

                <div class="error-field">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['year'];
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


                        
                        <div class="col-12 col-md-6 mb-4 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" wire:model="is_active" id="is_active">
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="name">File <span class="text-danger">*</span></label>
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
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Salary Grade</th>
                                            <th>Step 1</th>
                                            <th>Step 2</th>
                                            <th>Step 3</th>
                                            <th>Step 4</th>
                                            <th>Step 5</th>
                                            <th>Step 6</th>
                                            <th>Step 7</th>
                                            <th>Step 8</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($data['salary_grade']); ?></td>
                                                <td>
                                                     <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_1"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_1_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_2"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_2_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                   <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_3"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_3_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_4"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_4_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_5"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_5_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_6"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_6_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_7"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_7_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_8"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.<?php echo e($key); ?>.step_8_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->                    
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
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/settings/tranches/create.blade.php ENDPATH**/ ?>