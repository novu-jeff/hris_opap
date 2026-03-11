<form wire:submit.prevent='register' enctype="multipart/form-data">
    <div class="card shadow p-3">
        <div class="card-header bg-transparent py-2 border-0">
            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button type="button" wire:click="setActiveTab('personal')" class="nav-link <?php echo e($activeTab == 'personal' ? 'active' : ''); ?>" id="pills-personal-tab" data-bs-toggle="pill" data-bs-target="#pills-personal" type="button" role="tab" aria-controls="pills-personal" aria-selected="true">Personal Information</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button type="button" wire:click="setActiveTab('account')" class="nav-link <?php echo e($activeTab == 'account' ? 'active' : ''); ?>" id="pills-account-tab" data-bs-toggle="pill" data-bs-target="#pills-account" type="button" role="tab" aria-controls="pills-account" aria-selected="false">Account Information</button>
                </li>
            </ul>                  
            <div class="note my-3">
                By creating an account or signing in, you agree to <?php echo e($provider['company']); ?> HRIS Terms. You also acknowledge our Cookie and Privacy policies. Novulutions' HRIS will send you marketing messages, and you can opt out at any time by following the unsubscribe link in those messages or as described in our terms.
            </div>                
        </div>
        <hr class="my-2">
        <div class="card-body">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade <?php echo e($activeTab == 'personal' ? 'show active' : ''); ?>" id="pills-personal" role="tabpanel" aria-labelledby="pills-personal" tabindex="0">
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label for="firstname" class="mb-2">First Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.personal.firstname" id="firstname" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.personal.firstname'];
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
                            <label for="middlename" class="mb-2">Middle Name</label>
                            <input type="text" wire:model="fields.personal.middlename" id="middlename" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.personal.middlename'];
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
                            <label for="lastname" class="mb-2">Last Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.personal.lastname" id="lastname" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.personal.lastname'];
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
                            <label for="phone_no" class="mb-2">Mobile No. <span class="text-danger">*</span></label>
                            <input type="number" wire:model="fields.personal.phone_no" id="phone_no" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.personal.phone_no'];
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
                            <label for="tel_no" class="mb-2">Telephone No.</label>
                            <input type="text" wire:model="fields.personal.tel_no" id="tel_no" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.personal.tel_no'];
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
                            <label for="sex" class="mb-2">Sex <span class="text-danger">*</span></label>
                            <select wire:model="fields.personal.sex" id="sex" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="not to say">Prefer not to say</option>
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.personal.sex'];
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
                            <label for="birthday" class="mb-2">BirthDay <span class="text-danger">*</span></label>
                            <input type="date" wire:model="fields.personal.birthday" id="birthday" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.personal.birthday'];
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
                            <label for="civil_status" class="mb-2">Civil Status <span class="text-danger">*</span></label>
                            <select wire:model="fields.personal.civil_status" id="civil_status" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="seperated">Legally Separated</option>
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.personal.civil_status'];
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
                            <label for="address" class="mb-2">Address <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.personal.address" id="address" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.personal.address'];
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
                            <label for="province" class="mb-2">Province <span class="text-danger">*</span></label>
                            <select wire:model="fields.personal.province" id="province" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <option value="metro manila">Metro Manila</option>
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.personal.province'];
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
                            <label for="city" class="mb-2">City <span class="text-danger">*</span></label>
                            <select wire:model="fields.personal.city" id="city" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <option value="taguig city">Taguig City</option>
                            </select>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.personal.city'];
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
                <div class="tab-pane fade <?php echo e($activeTab == 'account' ? 'show active' : ''); ?>" id="pills-account" role="tabpanel" aria-labelledby="pills-account" tabindex="0">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="resume" class="mb-2">Attach Resume <span class="text-danger">*</span></label>
                            <input type="file" wire:model="resumeFile" id="resume" accept=".pdf,.doc,.docx" class="form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['resumeFile'];
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
                        <div class="col-12 mb-3">
                            <label for="email" class="mb-2">Email Address <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.account.email" id="email" class="form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.account.email'];
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
                            <label for="password" class="mb-2">Password <span class="text-danger">*</span></label>
                            <input type="password" wire:model="fields.account.password" id="password" class="form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.account.password'];
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
                            <label for="password" class="mb-2">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" wire:model="fields.account.confirm_password" id="confirm_password" class="form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['fields.account.confirm_password'];
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
        <div class="card-footer bg-transparent border-0 d-flex gap-2 justify-content-end">
            <button class="btn btn-primary px-4 py-2">Proceed</button>
        </div>
    </div>
</form>

<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/home/register.blade.php ENDPATH**/ ?>