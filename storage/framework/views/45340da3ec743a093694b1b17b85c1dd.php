<form wire:submit.prevent="save" wire:target="save">
    <div class="accordion" id="accordionTabPersonal">
        <div class="accordion-item mb-4">
            <h2 class="accordion-header">
                <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-personal" aria-expanded="false" aria-controls="flush-personal">
                    Personal Information
                </button>
            </h2>
            <div id="flush-personal" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3">
                            <label class="mb-2" for="lastname">Surname</label>
                            <input type="text" wire:model="records.lastname" id="lastname" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.lastname'];
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
                            <label class="mb-2" for="firstname">First Name</label>
                            <input type="text" wire:model="records.firstname" id="firstname" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.firstname'];
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
                            <label class="mb-2" for="middlename">Middle Name</label>
                            <input type="text" wire:model="records.middlename" id="middlename" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.middlename'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>  
                        <div class="col-12 col-md-2 mb-3">
                            <label class="mb-2" for="suffix">Suffix</label>
                            <select wire:model="records.suffix" id="suffix" class="form-select text-uppercase">
                                <option value=""> - CHOOSE - </option>
                                <option value="jr">Jr</option>
                                <option value="sr">Sr</option>
                                <option value="I">I</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                                <option value="V">V</option>
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.suffix'];
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
                            <label class="mb-2" for="birthday">Date of Birth</label>
                            <input type="date" wire:model="records.birthday" id="birthday" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.birthday'];
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
                            <label class="mb-2" for="civil_status">Civil Status</label>
                            <select wire:model="records.civil_status" id="civil_status" class="form-select text-uppercase">
                                <option value=""> - CHOOSE - </option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="divorced">Divorced</option>
                                <option value="separated">Separated</option>
                                <option value="widowed">Widowed</option>
                                <option value="annulled">Annulled</option>
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.civil_status'];
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
                            <label class="mb-2" for="sex">Sex</label>
                            <select wire:model="records.sex" id="sex" class="form-select text-uppercase">
                                <option value=""> - CHOOSE - </option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.sex'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>  
                        <div class="col-12 mb-3">
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label class="mb-2" for="citizenship">Citizenship</label>
                            <select wire:model="records.citizenship" wire:change="select_change('citizenship')"  id="citizenship" class="form-select text-uppercase">
                                <option value=""> - CHOOSE - </option>
                                <option value="filipino">Filipino</option>
                                <option value="dual_citizenship">Dual Citizenship</option>
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.citizenship'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php if($isDualCitizenship): ?>
                            <div class="col-12 col-md-4 mb-3">    
                                <label class="mb-2" for="country">Country (Dual Citizenship)</label>
                                    <select wire:model="records.country" id="citizenship_type" class="form-select text-uppercase">
                                        <option value=""> - CHOOSE - </option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($country['name']['common']); ?>"><?php echo e($country['name']['common']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.country'];
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
                        <div class="col-12 col-md-4 mb-3">
                            <label class="mb-2" for="citizenship_type">Citizenship Type</label>
                            <select wire:model="records.citizenship_type" id="citizenship_type" class="form-select text-uppercase">
                                <option value=""> - CHOOSE - </option>
                                <option value="by_birth">By Birth</option>
                                <option value="by_naturalization">By Naturalization</option>
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.citizenship_type'];
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
                            <label class="mb-2" for="birth_certificate">Birth Certificate - (img/pdf)</label>
                             <input type="file" wire:model="birth_certificate" class="form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.birth_certificate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <!--[if BLOCK]><![endif]--><?php if($birth_certificate): ?>
                                <small>Selected: <?php echo e($birth_certificate->getClientOriginalName()); ?></small>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <!--[if BLOCK]><![endif]--><?php if($hasBirthCert): ?>
                                <a href="<?php echo e(Storage::url($originalData->birth_certificate)); ?>" target="_blank">View existing</a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <!--[if BLOCK]><![endif]--><?php if($isMarried): ?>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="mb-2" for="marriage_certificate">Marriage Certificate - (img/pdf)</label>
                                <input type="file" name="marriage_certificate" id="marriage_certificate" class="form-control">
                                <div class="error-field">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.marriage_certificate'];
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
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item mb-4">
            <h2 class="accordion-header">
                <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-address" aria-expanded="false" aria-controls="flush-address">
                    Address
                </button>
            </h2>
            <div id="flush-address" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col-12 col-md-12 mb-3">
                            <label class="mb-2" for="present_address">Residential Address</label>
                            <input type="text" wire:model="records.present_address" id="present_address" class="form-control text-uppercase" placeholder="House / Block / Lot / Street / Subdivision / Village / Barangay">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.present_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>  
                        <div class="col-12 col-md-6 mb-3">
                            <label class="mb-2" for="present_province">State / Province</label>
                            <input type="text" wire:model="records.present_province" id="present_province" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.present_province'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>  
                        <div class="col-12 col-md-6 mb-3">
                            <label class="mb-2" for="present_city">City / Municipality</label>
                            <input type="text" wire:model="records.present_city" id="present_city" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.present_city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div> 
                        <div class="col-12 mb-3">
                            <hr>
                        </div> 
                        <div class="col-12 col-md-12 mb-3">
                            <label class="mb-2" for="permanent_address">Permanent Address</label>
                            <input type="text" wire:model="records.permanent_address" id="permanent_address" class="form-control text-uppercase" placeholder="House / Block / Lot / Street / Subdivision / Village / Barangay">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.permanent_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>  
                        <div class="col-12 col-md-6 mb-3">
                            <label class="mb-2" for="permanent_province">State / Province</label>
                            <input type="text" wire:model="records.permanent_province" id="permanent_province" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.permanent_province'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>  
                        <div class="col-12 col-md-6 mb-3">
                            <label class="mb-2" for="permanent_city">City / Municipality</label>
                            <input type="text" wire:model="records.permanent_city" id="permanent_city" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.permanent_city'];
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
        </div>
        <div class="accordion-item mb-4">
            <h2 class="accordion-header">
                <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-contact" aria-expanded="false" aria-controls="flush-contact">
                    Contact Information
                </button>
            </h2>
            <div id="flush-contact" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label class="mb-2" for="mobile_number">Mobile No.</label>
                            <input type="text" wire:model="records.mobile_number" id="mobile_number" class="form-control text-uppercase" data-mask="mobile">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.mobile_number'];
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
                            <label class="mb-2" for="tel_no">Telephone No.</label>
                            <input type="text" wire:model="records.tel_no" id="tel_no" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.tel_no'];
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
                            <label class="mb-2" for="email">Email</label>
                            <input type="email" wire:model="records.email" id="email" class="form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.email'];
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
        </div>
        <div class="accordion-item mb-4">
            <h2 class="accordion-header">
                <button class="accordion-button text-uppercase fw-bold" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#flush-government-ids"
                        aria-expanded="false"
                        aria-controls="flush-government-ids">
                    Government ID Numbers
                </button>
            </h2>

            <div id="flush-government-ids" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <div class="row">

                        <div class="col-12 col-md-6 mb-3">
                            <label class="mb-2" for="gsis_no">BP No.</label>
                            <input type="text"
                                wire:model="records.bp_no"
                                id="bp_no"
                                class="form-control">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.bp_no'];
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

                        <div class="col-12 col-md-6 mb-3">
                            <label class="mb-2" for="gsis_no">GSIS No.</label>
                            <input type="text"
                                wire:model="records.gsis_no"
                                id="gsis_no"
                                class="form-control">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.gsis_no'];
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

                        <div class="col-12 col-md-6 mb-3">
                            <label class="mb-2" for="pagibig_no">Pag-IBIG No.</label>
                            <input type="text"
                                wire:model="records.pagibig_no"
                                id="pagibig_no"
                                class="form-control">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.pagibig_no'];
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

                        <div class="col-12 col-md-6 mb-3">
                            <label class="mb-2" for="philhealth_no">PhilHealth No.</label>
                            <input type="text"
                                wire:model="records.philhealth_no"
                                id="philhealth_no"
                                class="form-control">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.philhealth_no'];
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

                        <div class="col-12 col-md-6 mb-3">
                            <label class="mb-2" for="sss_no">SSS No.</label>
                            <input type="text"
                                wire:model="records.sss_no"
                                id="sss_no"
                                class="form-control">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.sss_no'];
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

                        <div class="col-12 col-md-6 mb-3">
                            <label class="mb-2" for="tin_no">TIN</label>
                            <input type="text"
                                wire:model="records.tin_no"
                                id="tin_no"
                                class="form-control">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.tin_no'];
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
                </div>
            </div>
        </div>

        <div class="accordion-item mb-4">
            <h2 class="accordion-header">
                <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-appearance" aria-expanded="false" aria-controls="flush-appearance">
                    Appearance
                </button>
            </h2>
            <div id="flush-appearance" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="mb-2" for="height">Height</label>
                            <input type="text" wire:model="records.height" id="height" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.height'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="mb-2" for="weight">Weight</label>
                            <input type="text" wire:model="records.weight" id="weight" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="mb-2" for="blood_type">Blood Type</label>
                            <input type="text" wire:model="records.blood_type" id="blood_type" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['records.blood_type'];
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
        </div>
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

<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/hris/profile/personal.blade.php ENDPATH**/ ?>