<form wire:submit.prevent="save" wire:target="save">
    <div>
        <div class="d-flex justify-content-end mb-4">
            <button type="button" class="btn btn-info px-3 py-2 text-uppercase text-center fw-bold" wire:click="addRecord">
                <span wire:loading.remove wire:target="addRecord">Add Record </span>
                <span wire:loading wire:target="addRecord"><i class="fa-solid fa-spinner px-2 fa-spin"></i></span>
            </button>
        </div>
        <!--[if BLOCK]><![endif]--><?php if(!empty($records)): ?>
            <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center"></th>
                            <th colspan="2" class="text-center">Inclusive Dates <br> (mm/dd/yyyy)</th>
                            <th rowspan="2" class="text-center">Position Title <br> (Write in full / Do not abbreviate)</th>
                            <th rowspan="2" class="text-center">Department / Agency / Office / Company <br> (Write in full / Do not abbreviate)</th>
                            <th rowspan="2" class="text-center">Monthly Salary</th>
                            <th rowspan="2" class="text-center">Salary / Job / Pay Grade (if applicable) <br> & Step (Format "00-0") / Increment</th>
                            <th rowspan="2" class="text-center">Status of Appointment</th>
                            <th rowspan="2" class="text-center">Gov't Service (Y / N)</th>
                            <th rowspan="2" class="text-center">Documents</th>
                        </tr>
                        <tr>
                            <th class="text-center">From</th>
                            <th class="text-center">To</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-danger" wire:click="removeRecord('true', <?php echo e($key); ?>)">
                                        <i class="fa-solid fa-circle-minus"></i>
                                    </button>
                                </td>
                                <td>
                                    <input style="width: 300px" type="number" wire:model="records.<?php echo e($key); ?>.from_year" id="records.<?php echo e($key); ?>.from_year" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.from_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>
                                <td>
                                    <input style="width: 300px" type="number" wire:model="records.<?php echo e($key); ?>.to_year" id="records.<?php echo e($key); ?>.to_year" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.to_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>
                                <td>
                                    <input style="width: 600px" type="text" wire:model="records.<?php echo e($key); ?>.position" id="records.<?php echo e($key); ?>.position" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.position'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>
                                <td>
                                    <input style="width: 800px" type="text" wire:model="records.<?php echo e($key); ?>.department" id="records.<?php echo e($key); ?>.department" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.department'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>
                                <td>
                                    <input style="width: 300px" type="number" wire:model="records.<?php echo e($key); ?>.monthly_salary" id="records.<?php echo e($key); ?>.monthly_salary" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.monthly_salary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>
                                <td>
                                    <input style="width: 100%" type="text" wire:model="records.<?php echo e($key); ?>.salary_pay_grade" id="records.<?php echo e($key); ?>.salary_pay_grade" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.salary_pay_grade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>
                                <td>
                                    <input style="width: 500px" type="text" wire:model="records.<?php echo e($key); ?>.employment_status" id="records.<?php echo e($key); ?>.employment_status" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.employment_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>
                                <td>
                                    <select style="width: 300px" wire:model="records.<?php echo e($key); ?>.isGovernment" id="records.<?php echo e($key); ?>.isGovernment" class="form-select text-uppercase text-center">
                                        <option value=""> - CHOOSE - </option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.isGovernment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>                                                
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <input type="file" style="width: 300px;" wire:model="records.<?php echo e($key); ?>.documents" id="records.<?php echo e($key); ?>.documents" class="form-control">
                                        </div>
                                        <!--[if BLOCK]><![endif]--><?php if($records[$key]['document_control']): ?>
                                            <div class="d-flex gap-2">
                                                <a href="javascript:void(0)" wire:click.prevent="download('<?php echo e($key); ?>')" class="btn btn-primary">
                                                    <i class="fa-solid fa-download"></i>
                                                </a>
                                                <a href="javascript:void(0)" wire:click.prevent="removeDocument('true', '<?php echo e($key); ?>')" class="btn btn-danger">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.documents'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>                                                  
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-danger text-uppercase text-center fw-medium text-center">No data Found.</div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->            
        <div class="card-footer mt-5 pb-3 d-flex justify-content-end bg-transparent border-0">
            <div class="text-end">
                <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase text-center fw-bold">
                    <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                    <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                </button> 
            </div>
        </div>
    </div>
</form>

<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/hris/profile/employment-history.blade.php ENDPATH**/ ?>