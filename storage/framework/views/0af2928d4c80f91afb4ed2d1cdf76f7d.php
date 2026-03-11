<div>
    <form wire:submit.prevent="save">
        <div class="card mb-4 border-0">
            <div class="card-header border-0 bg-transparent">
                <h5 class="mb-0 text-uppercase fw-bold pt-4 pb-0 ps-2">Employee Details</h5>
            </div>
            <div class="card-body px-4">
                <div class="row my-3">
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="employee_no">Employee No. <span class="text-danger">*</span></label>
                        <input type="text" wire:model="records.employee_information.employee_no" id="records.employee_information.employee_no" class="form-control" >
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.employee_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="biometrics_id">Biometrics ID <span class="text-danger">*</span></label>
                        <input type="number" min="0"  wire:model="records.employee_information.biometrics_id" id="records.employee_information.biometrics_id" class="form-control">
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.biometrics_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>  
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="date_hired">Date Hired <span class="text-danger">*</span></label>
                        <input type="date" wire:change="select_change('date_hired')" wire:model="records.employee_information.date_hired" id="records.employee_information.date_hired" class="form-control" >
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.date_hired'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div> 
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="service_duration">Service Duration   </label>
                        <input type="text" wire:model="records.employee_information.service_duration" id="records.employee_information.service_duration" class="form-control restricted" readonly>
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.service_duration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div> 
                    <div class="col-md-3 mb-3">
                        <label class="mb-2" for="status">Account Status <span class="text-danger">*</span></label>
                        <select wire:model="records.employee_information.status" id="records.employee_information.status" class="form-select">
                            <option value=""> - CHOOSE - </option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label class="mb-2" for="date_resignation">Date Resignation</label>
                        <input type="text" wire:model="records.employee_information.date_resignation" id="records.employee_information.date_resignation" class="form-control" >
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.date_resignation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>  
                    <div class="col-12 mt-4 mb-3">
                        <h5 class="mb-0 text-uppercase fw-bold pt-4 pb-0 ps-2">Organization Details</h5>
                        <hr>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="mb-2" for="section_id">Section <span class="text-danger">*</span></label>
                        <select wire:model="records.employee_information.section_id" wire:change="select_change('section')" id="records.employee_information.section_id" class="form-select">
                            <option value=""> - CHOOSE - </option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($section->id); ?>"><?php echo e($section->code . ' - ' . $section->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.section_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="mb-2" for="branch">Central / Field Office</label>
                        <input type="text" wire:model="records.employee_information.branch" id="records.employee_information.branch" class="form-control" >
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.branch'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="mb-2" for="department">Cluster</label>
                        <input type="text" wire:model="records.employee_information.department" id="records.employee_information.department" class="form-control" >
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.department'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                    <div class="col-12 mt-4 mb-3">
                        <h5 class="mb-0 text-uppercase fw-bold pt-4 pb-0 ps-2">Employment Details</h5>
                        <hr>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="mb-2" for="type">Employment Type <span class="text-danger">*</span></label>
                        <select wire:change="handleSalary" wire:model="records.employee_information.type" id="records.employee_information.type" class="form-select">
                            <option value=""> - CHOOSE - </option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $employmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(strtolower($category->id)); ?>"><?php echo e($category->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if($isGovernment): ?>
                        <!--[if BLOCK]><![endif]--><?php if(in_array($records['employee_information']['type'], ['1', '2'])): ?>
                            <div class="col-md-4 mb-3">
                                <label class="mb-2" for="position_id">Position <span class="text-danger">*</span></label>
                                <select wire:change="handleSalary" wire:model.live="records.employee_information.position_id" id="records.employee_information.position_id" class="form-select">
                                    <option value=""> - CHOOSE - </option>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($position->id); ?>"><?php echo e($position->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </select>
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.position_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="mb-2" for="step_id">Tranche Step <span class="text-danger">*</span></label>
                                <select wire:change="handleSalary" wire:model.live="records.employee_information.step_id" id="records.employee_information.step_id" class="form-select">
                                    <option value=""> - CHOOSE - </option>
                                    <!--[if BLOCK]><![endif]--><?php for($i = 1; $i <= 8; $i++): ?>
                                        <option value="<?php echo e($i); ?>"> Step <?php echo e($i); ?></option>
                                    <?php endfor; ?><!--[if ENDBLOCK]><![endif]-->
                                </select>
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.step_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>      
                        <?php elseif($records['employee_information']['type'] == 3): ?>    
                            <div class="col-md-4 mb-3">
                                <label class="mb-2" for="job_completion">Job Order Completion <span class="text-danger">*</span></label>
                                <input type="date" wire:model="records.employee_information.job_completion" id="records.employee_information.job_completion" class="form-control">
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.job_completion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>     
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php else: ?>
                        <div class="col-md-4 mb-3">
                            <label class="mb-2" for="position_id">Position <span class="text-danger">*</span></label>
                            <select wire:change="handleSalary" wire:model.live="records.employee_information.position_id" id="records.employee_information.position_id" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($position->id); ?>"><?php echo e($position->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.position_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="shift_schedule">Shift Schedule <span class="text-danger">*</span></label>
                        <select wire:model="records.employee_information.shift_schedule" wire:change="select_change('section')" id="records.employee_information.shift_schedule" class="form-select">
                            <option value=""> - CHOOSE - </option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $shiftSchedule; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($shift->id); ?>"><?php echo e($shift->name . ' (' . $shift->work_setup . ')'); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.shift_schedule'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div> 
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="employee_schedule">Days Schedule <span class="text-danger">*</span></label>
                        <select wire:model="records.employee_information.employee_schedule" wire:change="select_change('section')" id="records.employee_information.employee_schedule" class="form-select">
                            <option value=""> - CHOOSE - </option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $employeeSchedule; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($schedule->id); ?>"><?php echo e($schedule->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.employee_schedule'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div> 
                    <div class="col-12 mt-4 mb-3">
                        <h5 class="mb-0 text-uppercase fw-bold pt-4 pb-0 ps-2">Salary & Payroll Details</h5>
                        <hr>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="mb-2" for="salary_method">Salary Method <span class="text-danger">*</span></label>
                        <select wire:model="records.employee_information.salary_method" id="records.employee_information.salary_method" class="form-select">
                             <option value=""> - CHOOSE - </option>
                            <option value="cash">Cash</option>
                            <option value="land bank atm">Land Bank ATM</option>
                        </select>
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.salary_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="mb-2" for="salary_type">Salary Type <span class="text-danger">*</span></label>
                        <select wire:model="records.employee_information.salary_type" id="records.employee_information.salary_type" class="form-select">
                            <option value=""> - CHOOSE - </option>
                            <option value="monthly">Monthly Rate</option>
                            <option value="salary">Daily Rate</option>
                        </select>
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.salary_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if($isGovernment): ?>
                        <div class="col-md-3 mb-3">
                            <label class="mb-2" for="salary">Salary Amount <span class="text-danger">*</span></label>
                            <input type="text" wire:model="records.employee_information.salary" id="records.employee_information.salary" class="form-control <?php echo e($records['employee_information']['type'] == 3 ? '' : 'restricted'); ?>" <?php echo e($records['employee_information']['type'] == 3 ? '' : ''); ?>>
                        <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.salary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        </div>
                    <?php else: ?>
                    <div class="col-md-3 mb-3">
                            <label class="mb-2" for="salary">Salary Amount <span class="text-danger">*</span></label>
                            <input type="text" wire:model="records.employee_information.salary" id="records.employee_information.salary" class="form-control">
                        <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.salary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div class="col-md-3 mb-3">
                        <label class="mb-2" for="payroll_account_number">Payroll Account No.</label>
                        <input type="text" wire:model="records.employee_information.payroll_account_number" id="records.employee_information.payroll_account_number" class="form-control">
                        <div class="error-field">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.employee_information.payroll_account_number'];
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
        </div>
        <!--[if BLOCK]><![endif]--><?php if(!empty($records)): ?>
            <hr class="mb-5">
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
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/hris/manual.blade.php ENDPATH**/ ?>