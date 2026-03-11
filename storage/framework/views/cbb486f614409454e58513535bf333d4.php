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
                            <th rowspan="2" class="text-center">Level</th>
                            <th rowspan="2" class="text-center">Name of School</th>
                            <th rowspan="2" class="text-center">Basic Education / Degree / Course</th>
                            <th colspan="2" class="text-center">Period of Attendance</th>
                            <th rowspan="2" class="text-center">Highest Level / Units Earned <br> (if not graduated)</th>
                            <th rowspan="2" class="text-center">Year Graduated</th>
                            <th rowspan="2" class="text-center">Scholarship / Academic <br> Honors Received</th>
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
                                <select style="width: 300px" wire:model="records.<?php echo e($key); ?>.level" id="records.<?php echo e($key); ?>.level" class="form-select text-uppercase text-center">
                                    <option value=""> - CHOOSE - </option>
                                    <option value="elementary">Elementary</option>
                                    <option value="secondary">Secondary</option>
                                    <option value="vocational">Vocational</option>
                                    <option value="highschool">High School</option>
                                    <option value="senior_highschool">Senior High School</option>
                                    <option value="college">College</option>
                                    <option value="masters">Masters</option>
                                    <option value="doctoral">Doctoral</option>
                                </select>                                                
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.level'];
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
                                <input type="text" style="width: 600px" wire:model="records.<?php echo e($key); ?>.school_name" id="records.<?php echo e($key); ?>.school_name" class="form-control text-uppercase text-center">
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.school_name'];
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
                                <input type="text" style="width: 800px" wire:model="records.<?php echo e($key); ?>.course" id="records.<?php echo e($key); ?>.course" class="form-control text-uppercase text-center">
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.course'];
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
                                <input type="number" style="width: 300px" wire:model="records.<?php echo e($key); ?>.from_year" id="records.<?php echo e($key); ?>.from_year" class="form-control text-uppercase text-center">
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
                                <input type="number" style="width: 300px" wire:model="records.<?php echo e($key); ?>.to_year" id="records.<?php echo e($key); ?>.to_year" class="form-control text-uppercase text-center">
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
                                <input type="text" style="width: 300px" wire:model="records.<?php echo e($key); ?>.highest_level" id="records.<?php echo e($key); ?>.highest_level" class="form-control text-uppercase text-center">
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.highest_level'];
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
                                <input type="number" style="width: 300px" wire:model="records.<?php echo e($key); ?>.year_graduated" id="records.<?php echo e($key); ?>.year_graduated" class="form-control text-uppercase text-center">
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.year_graduated'];
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
                                <input type="text" style="width: 800px" wire:model="records.<?php echo e($key); ?>.scholarship_honors" id="records.<?php echo e($key); ?>.scholarship_honors" class="form-control text-uppercase text-center">
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.'.$key.'.scholarship_honors'];
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

<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/employee/profile/education.blade.php ENDPATH**/ ?>