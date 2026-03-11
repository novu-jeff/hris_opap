<form wire:submit.prevent="save" wire:target="save">
    <div>
        <div class="d-flex justify-content-end mb-4">
            <button type="button" class="btn btn-info px-3 py-2 text-uppercase fw-bold" wire:click="addRecord">
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
                            <th rowspan="2" class="text-center">Career Service/ RA 1080 (Board / Bar) Under Special Laws/ CES/ CSEE<br>Barangay Eligibility / Driver's License</th>
                            <th rowspan="2" class="text-center">Rating <br> (if applicable)</th>
                            <th rowspan="2" class="text-center">Date of Examination / Conferment</th>
                            <th rowspan="2" class="text-center">Place of Exam / Conferment</th>
                            <th colspan="2" class="text-center">License (if applicable)</th>
                            <th rowspan="2" class="text-center">Documents</th>
                        </tr>
                        <tr>
                            <th class="text-center">Number</th>
                            <th class="text-center">Date of validity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-danger" wire:click="removeRecord(<?php echo e($key); ?>)">
                                        <i class="fa-solid fa-circle-minus"></i>
                                    </button>
                                </td>
                                <td>
                                    <input style="width: 800px" type="text" wire:model="records.<?php echo e($key); ?>.certification" id="records.<?php echo e($key); ?>.certification" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.certification'];
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
                                    <input style="width: 300px" type="text" wire:model="records.<?php echo e($key); ?>.rating" id="records.<?php echo e($key); ?>.rating" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.rating'];
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
                                    <input style="width: 300px" type="date" wire:model="records.<?php echo e($key); ?>.date_exam" id="records.<?php echo e($key); ?>.date_exam" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.date_exam'];
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
                                    <input style="width: 300px" type="text" wire:model="records.<?php echo e($key); ?>.place_exam" id="records.<?php echo e($key); ?>.place_exam" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.place_exam'];
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
                                    <input style="width: 300px" type="text" wire:model="records.<?php echo e($key); ?>.license_no" id="records.<?php echo e($key); ?>.license_no" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.license_no'];
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
                                    <input style="width: 300px" type="date" wire:model="records.<?php echo e($key); ?>.date_validity" id="records.<?php echo e($key); ?>.date_validity" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.date_validity'];
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
                                        <!--[if BLOCK]><![endif]--><?php if($records[$key]['documents']): ?>
                                            <div>
                                                <a href="javascript:void(0)" wire:click.prevent="download('<?php echo e($key); ?>')" class="btn btn-primary">
                                                    <i class="fa-solid fa-download"></i>
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
            <div class="alert alert-danger text-uppercase fw-medium text-center">No data found.</div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->            
        <div class="card-footer mt-5 pb-3 d-flex justify-content-end bg-transparent border-0">
            <div class="text-end">
                <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                    <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                    <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>
</form>

<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/employee/profile/civil-service.blade.php ENDPATH**/ ?>