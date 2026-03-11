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
                                            <th>Position</th>
                                            <th>Salary Grade</th>
                                            <th>Eligible</th>
                                            <th>W/Tax</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <input type="text" wire:model="records.<?php echo e($key); ?>.Position" class="form-control">
                                                </td>
                                                <td>
                                                    <input type="text" wire:model="records.<?php echo e($key); ?>.Salary Grade" class="form-control">
                                                </td>
                                                <td>
                                                    <select wire:model="records.<?php echo e($key); ?>.Type" class="form-select">
                                                        <option value="1">Regular Contractual</option>
                                                        <option value="2">Contract of Service</option>
                                                        <option value="3">Job Offer</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" wire:model="records.<?php echo e($key); ?>.W/Tax" class="form-control">
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
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/settings/hris/position/create.blade.php ENDPATH**/ ?>