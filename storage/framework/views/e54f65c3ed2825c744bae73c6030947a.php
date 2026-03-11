<div>
    
    <div class="modal fade" wire:ignore.self id="applicant_info" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Applicant Information</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>ID:</strong> <?php echo e($applicant_information->id ?? null); ?>

                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>First Name:</strong> <?php echo e($applicant_information->firstname ?? null); ?>

                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Middle Name:</strong> <?php echo e($applicant_information->middlename ?? null); ?>

                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Last Name:</strong> <?php echo e($applicant_information->lastname ?? null); ?>

                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Phone No:</strong> <?php echo e($applicant_information->phone_no ?? null); ?>

                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Tel No:</strong> <?php echo e($applicant_information->tel_no ?? null); ?>

                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Sex:</strong> <?php echo e($applicant_information->sex ?? null); ?>

                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Birthday:</strong> <?php echo e($applicant_information->birthday ?? null); ?>

                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Civil Status:</strong> <?php echo e($applicant_information->civil_status ?? null); ?>

                                </div>
                                <div class="col-12 mb-3">
                                    <hr>
                                </div>
                                <div class="col-md-12 mb-3 text-uppercase">
                                    <strong>Address:</strong> <?php echo e($applicant_information->address ?? null); ?>

                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Province:</strong> <?php echo e($applicant_information->province ?? null); ?>

                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>City:</strong> <?php echo e($applicant_information->city ?? null); ?>

                                </div>
                                <div class="col-12 mb-3">
                                    <hr>
                                </div>
                                <div class="col-md-12 mb-3 text-uppercase">
                                    <strong>Resume:</strong> 
                                    <!--[if BLOCK]><![endif]--><?php if($applicant_information && !is_null($applicant_information->resume)): ?>
                                        <a target="_blank" href="<?php echo e(Storage::url('applicant/users/' . $applicant_information->id . '/' . $applicant_information->resume)); ?>" class="text-lowercase">
                                            <?php echo e($applicant_information->resume); ?>

                                        </a>
                                    <?php else: ?>
                                        <span class="text-lowercase">No resume uploaded</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div class="col-md-12 mb-3 text-uppercase">
                                    <!--[if BLOCK]><![endif]--><?php if(!is_null($applicant_information) && !$applicant_information->skills->isEmpty()): ?>
                                        <strong>Skills:</strong> 
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <div class="skill-content pt-2 pb-3">
                                        <!--[if BLOCK]><![endif]--><?php if(!is_null($applicant_information) && !$applicant_information->skills->isEmpty()): ?>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $applicant_information->skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="skill-box">
                                                    <?php echo e($skill->skills->name); ?>

                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php if(!empty($application_information->level)): ?>
                                    <div class="col-12 mb-3">
                                        <hr>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 text-uppercase  d-flex justify-content-end">
                            <div class="profile" style="width: 150px; height: 150px;">
                                <img src="<?php echo e(isset($applicant_information) && !is_null($applicant_information) && !is_null($applicant_information->image)
                                        ? Storage::url('applicant/users/' . $applicant_information->id . '/' . $applicant_information->image)
                                        : 'https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name=' . urlencode(($applicant_information->firstname ?? '') . ' ' . ($applicant_information->lastname ?? ''))); ?>" class="w-100 h-100" style="width: 150px; height: 150px; object-fit:cover" alt="" srcset="">                                                             
                            </div>                            
                        </div>
                        <!--[if BLOCK]><![endif]--><?php if(!empty($applicant_information->level)): ?>
                            <div class="col-md-12 mb-3 text-uppercase">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <strong>Highest School Attaintment:</strong> <?php echo e($applicant_information->level ?? null); ?>

                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <strong>School Name:</strong> <?php echo e($applicant_information->school_name ?? null); ?>

                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <strong>Course:</strong> <?php echo e($applicant_information->course ?? null); ?>

                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <strong>From:</strong> <?php echo e($applicant_information->started ?? null); ?>

                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <strong>To:</strong> <?php echo e($applicant_information->finished ?? null); ?>

                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" wire:ignore.self id="select_interview" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Choose Interview</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!--[if BLOCK]><![endif]--><?php if(!empty($interview)): ?>
                        <div class="row">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $interview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-12">
                                    <div class="d-flex gap-2 align-items-start">
                                        <div>
                                            <input type="checkbox" wire:model='selected_interview.<?php echo e($item->id); ?>'  class="form-check">
                                        </div>
                                        <div>
                                            <h6 class="mt-1 mb-0 text-uppercase"><?php echo e($item->name); ?></h6>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">No Interviews created, please add first.</div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <!--[if BLOCK]><![endif]--><?php if(!empty($interview)): ?>
                    <div class="modal-footer d-flex justify-content-end">
                        <button class="btn btn-primary" wire:loading.attr="disabled" wire:click="set_interview(false)">
                            <span wire:loading.remove>Proceed</span>    
                            <span wire:loading>Proceeding <i class="fa-solid fa-spinner fa-spin"></i>
                        </button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

    
    <div class="modal fade" wire:ignore.self id="applicant_responses" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">View Responses</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!--[if BLOCK]><![endif]--><?php if(!is_null($applicant_responses)): ?>
                        <div class="accordion" id="accordionExample">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $applicant_responses['interview']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $response): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="accordion-item mb-3 shadow-sm border-1">
                                    <h2 class="accordion-header" id="heading<?php echo e($index); ?>">
                                        <button class="accordion-button text-uppercase <?php echo e($index == 0 ? '' : 'collapsed'); ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo e($index); ?>" aria-expanded="<?php echo e($index == 0 ? 'true' : 'false'); ?>" aria-controls="collapse<?php echo e($index); ?>">
                                            <?php echo e($response['details']['name']); ?>

                                        </button>
                                    </h2>
                                    <div id="collapse<?php echo e($index); ?>" class="accordion-collapse collapse <?php echo e($index == 0 ? 'show' : ''); ?>" aria-labelledby="heading<?php echo e($index); ?>" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <!--[if BLOCK]><![endif]--><?php if(empty($response['items'])): ?>
                                                <div class="mt-3 mb-4">
                                                    <h6 class="text-danger text-uppercase">No interview items available.</h6>
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $response['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemIndex => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-12 mb-4 text-uppercase">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="count d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; color: white; background-color: #225F8B">
                                                            <?php echo e($itemIndex + 1); ?>

                                                        </div>
                                                        <div class="question w-100">
                                                            <h6 class="mb-0"><?php echo e($item['question']); ?></h6>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <!--[if BLOCK]><![endif]--><?php if($item['response_type'] == 'simple'): ?>
                                                            <div class="col-12 mb-3">
                                                                <input type="text" class="form-control text-uppercase restricted" value="<?php echo e($item['answers'][0]['answer'] ?? ''); ?>" placeholder="Applicant's Answer" readonly>
                                                            </div>
                                                        <?php elseif($item['response_type'] == 'explanatory'): ?>
                                                            <div class="col-12 mb-3">
                                                                <textarea class="form-control text-uppercase restricted" rows="5" placeholder="Applicant's Answer" readonly><?php echo e($item['answers'][0]['answer'] ?? ''); ?></textarea>
                                                            </div>
                                                        <?php elseif($item['response_type'] == 'checkbox'): ?>
                                                            <div class="col-12 mb-3">
                                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $item['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionIndex => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <div class="ms-5 form-check d-flex align-items-center gap-3">
                                                                        <input 
                                                                            type="checkbox" 
                                                                            class="form-check-input" 
                                                                            id="checkbox-<?php echo e($index); ?>-<?php echo e($optionIndex); ?>" 
                                                                            value="<?php echo e($option['name']); ?>" 
                                                                            style="width: 1.5em; height: 1.5em"
                                                                            <?php if(in_array($option['id'], array_column($item['answers']->toArray(), 'answer'))): ?>
                                                                                checked
                                                                            <?php endif; ?>
                                                                            disabled
                                                                            >
                                                                        <label class="form mt-1 mb-0" for="checkbox-<?php echo e($index); ?>-<?php echo e($optionIndex); ?>">
                                                                            <?php echo e($option['name']); ?>

                                                                        </label>
                                                                    </div>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                            </div>
                                                        <?php elseif($item['response_type'] == 'radio'): ?>
                                                            <div class="col-12 mb-3">
                                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $item['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionIndex => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <div class="ms-5 form-check d-flex align-items-center gap-3">
                                                                        <input 
                                                                            type="radio" 
                                                                            class="form-check-input" 
                                                                            id="radio-<?php echo e($index); ?>-<?php echo e($optionIndex); ?>" 
                                                                            value="<?php echo e($option['id']); ?>" 
                                                                            style="width: 1.5em; height: 1.5em"
                                                                            <?php if(in_array($option['id'], array_column($item['answers']->toArray(), 'answer'))): ?>
                                                                                checked
                                                                            <?php endif; ?>
                                                                            disabled
                                                                            >
                                                                        <label class="form mt-1 mb-0" for="radio-<?php echo e($index); ?>-<?php echo e($optionIndex); ?>">
                                                                            <?php echo e($option['name']); ?>

                                                                        </label>
                                                                    </div>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                            </div>
                                                        <?php elseif($item['response_type'] == 'file'): ?>
                                                            <div class="col-12 mb-3">
                                                                <input type="file" class="form-control" disabled>
                                                            </div>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" wire:ignore.self id="applicant_job_offer" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Send Job Offer</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="mb-2" for="subject">Subject <span class="text-danger">*</span></label>
                            <input type="text" wire:model="job_offer.subject" id="subject" class="form-control text-uppercase">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['job_offer.subject'];
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
                            <label class="mb-2" for="starting_date">Starting Date <span class="text-danger">*</span></label>
                            <input type="date" wire:model.live="job_offer.starting_date" id="job_offer.starting_date" class="form-control">
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['job_offer.starting_date'];
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
                            <label class="mb-2" for="salary">Salary <span class="text-danger">*</span></label>
                            <input type="number" wire:model.live="job_offer.salary" id="job_offer.salary" min="<?php echo e($job_offer['min_salary'] ?? 0); ?>" max="<?php echo e($job_offer['max_salary'] ?? 0); ?>" class="form-control">
                            <input type="range" wire:model.live="job_offer.salary" id="job_offer.salary" min="<?php echo e($job_offer['min_salary'] ?? 0); ?>" max="<?php echo e($job_offer['max_salary'] ?? 0); ?>" class="form-range">
                            <div class="mt-2">
                                <p class="fw-bold text-uppercase mb-0">&#8369; <?php echo e(number_format($job_offer['salary'] ?? 0, 2)); ?></p>
                            </div>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['job_offer.salary'];
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
                            <label class="mb-2" for="body">Body <span class="text-danger">*</span></label>
                            <div wire:ignore>
                                <textarea wire:model="job_offer.body" id="ckeditor" class="form-control text-uppercase"></textarea>
                            </div>
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['job_offer.body'];
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
                            <label class="mb-2" for="attachment">Attachments <span class="text-danger">*</span></label>
                            <input type="file" wire:model="job_offer.attachment" id="attachment" class="form-control text-uppercase">
                            <!--[if BLOCK]><![endif]--><?php if(isset($job_offer['attachment_preview'])): ?>
                                <iframe src="<?php echo e($job_offer['attachment_preview']); ?>" width="100%" height="500px" class="mt-3"></iframe>                                                    
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <div class="error-field">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['job_offer.attachment'];
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
                <div class="modal-footer d-flex justify-content-end">
                    <button class="btn btn-primary" wire:loading.attr="disabled" wire:click="send_offer(true)">
                        <span wire:loading.remove>Send Offer</span>    
                        <span wire:loading>Sending Offer <i class="fa-solid fa-spinner fa-spin"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" wire:ignore.self id="applicant_requirements" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Requirements Checklist</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <!--[if BLOCK]><![endif]--><?php if(!empty($requirements)): ?>
                        <div class="row">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $requirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-12 col-md-6 mb-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <input type="checkbox" class="form-check-input" <?php echo e($selected_requirements->contains('requirement_id', $item->id) ? 'checked' : ''); ?>  disabled>
                                        </div>
                                        <div>
                                            <h6 class="mt-1 mb-0 text-uppercase"><?php echo e($item->name); ?></h6>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <?php
                                            $selectedReq = $selected_requirements->firstWhere('requirement_id', $item->id);
                                        ?>
                                        <!--[if BLOCK]><![endif]--><?php if($selectedReq): ?>
                                            <a href="javascript:void(0)" wire:click='download_requirement(<?php echo e($selectedReq->id); ?>)'><?php echo e($selectedReq->attachment); ?></a>
                                        <?php else: ?>
                                            <p class="text-uppercase">No attachment</p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">No Interviews created, please add first.</div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item text-uppercase fw-bold" role="presentation">
                    <a href="<?php echo e(route('job.applicants.index', ['status' => 'pending'])); ?>" class="nav-link <?php echo e($status == 'pending' ? 'active' : ''); ?>" id="pills-pending-tab"  aria-selected="true">Pending</a>
                </li>
                <li class="nav-item text-uppercase fw-bold" role="presentation">
                    <a href="<?php echo e(route('job.applicants.index', ['status' => 'interview'])); ?>" class="nav-link <?php echo e($status == 'interview' ? 'active' : ''); ?>" id="pills-interview-tab" type="button" role="tab" aria-controls="pills-interview" aria-selected="false">Interview</a>
                </li>
                <li class="nav-item text-uppercase fw-bold" role="presentation">
                    <a href="<?php echo e(route('job.applicants.index', ['status' => 'placement'])); ?>" class="nav-link <?php echo e($status == 'placement' ? 'active' : ''); ?>" id="pills-placement-tab" type="button" role="tab" aria-controls="pills-placement" aria-selected="false">Placement</a>
                </li>
                <li class="nav-item text-uppercase fw-bold" role="presentation">
                    <a href="<?php echo e(route('job.applicants.index', ['status' => 'onboarding'])); ?>" class="nav-link <?php echo e($status == 'onboarding' ? 'active' : ''); ?>" id="pills-onboarding-tab"type="button" role="tab" aria-controls="pills-onboarding" aria-selected="false">Onboarding</a>
                </li>
                <li class="nav-item text-uppercase fw-bold" role="presentation">
                    <a href="<?php echo e(route('job.applicants.index', ['status' => 'hired'])); ?>" class="nav-link <?php echo e($status == 'hired' ? 'active' : ''); ?>" id="pills-hired-tab"  type="button" role="tab" aria-controls="pills-hired" aria-selected="false">Hired</a>
                </li>
                <li class="nav-item text-uppercase fw-bold" role="presentation">
                    <a href="<?php echo e(route('job.applicants.index', ['status' => 'rejected'])); ?>" class="nav-link <?php echo e($status == 'rejected' ? 'active' : ''); ?>" id="pills-rejected-tab"  type="button" role="tab" aria-controls="pills-rejected" aria-selected="false">Rejected</a>
                </li>
            </ul>
            <div class="mt-4">
                <div class="row mb-4">
                    <div class="col-md-6 d-flex align-items-center gap-2">
                        <label for="entries" class="form-label mb-0">Show entries:</label>
                        <select id="entries" wire:model.live="entries" class="form-select w-auto">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="40">40</option>
                            <option value="50">50</option>
                            <option value="60">60</option>
                            <option value="70">70</option>
                            <option value="80">80</option>
                            <option value="90">90</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
                        <label for="search" class="form-label mb-0">Search:</label>
                        <input id="search" wire:model.live="search" type="text" class="form-control w-50" placeholder="Search something...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Company</th>
                                <th>Position</th>
                                <!--[if BLOCK]><![endif]--><?php if($status == 'placement'): ?>
                                    <th>
                                        Job Offer Status
                                    </th>
                                    <th>
                                        Signed Job Offer
                                    </th>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <th>Date Applied</th>
                                <th style="max-width: 200px;">Action</th>
                            </tr>
                        </thead>                
                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr data-id="<?php echo e($record->id); ?>">
                                    <td><?php echo e($record->applicant_no); ?></td>
                                    <td><?php echo e($record->job->company_name); ?></td>
                                    <td><?php echo e($record->job->position); ?></td>
                                    <!--[if BLOCK]><![endif]--><?php if($status == 'placement'): ?>
                                        <td>
                                            <?php echo e($records[0]->offer ? 'Offer Sent' : 'Pending For Offer'); ?>

                                        </td>
                                        <td>
                                            <?php echo e($records[0]->offer 
                                            ?
                                            'Received'
                                            : 
                                            'Waiting For Signature'); ?>

                                        </td>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <td><?php echo e(format_date($record->created_at, 'day_date_string')); ?></td>
                                    <td>
                                        <a target="_blank" href="<?php echo e(route('home.view-job', ['slug' => $record->job->slug])); ?>" class="btn btn-info mx-1">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                        <button class="btn btn-success mx-1" wire:click="view_applicant(<?php echo e($record->id); ?>)">
                                            <span wire:loading.remove wire:target="view_applicant(<?php echo e($record->id); ?>)">
                                                <i class="fa-solid fa-person"></i>
                                            </span>
                                            <span wire:loading wire:target="view_applicant(<?php echo e($record->id); ?>)">
                                                <i class="fa-solid fa-spinner fa-spin"></i>
                                            </span>
                                        </button>
                                        <!--[if BLOCK]><![endif]--><?php if($status == 'interview'): ?>
                                            <button wire:click="view_responses(<?php echo e($record->id); ?>)" class="btn btn-primary mx-1">
                                                <span wire:loading.remove wire:target="view_responses(<?php echo e($record->id); ?>)">
                                                    <i class="fa-solid fa-reply"></i>
                                                </span>
                                                <span wire:loading wire:target="view_responses(<?php echo e($record->id); ?>)">
                                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                                </span>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <!--[if BLOCK]><![endif]--><?php if($status != 'rejected' && $status != 'hired'): ?>
                                            <button wire:click="set_action('rejected', <?php echo e($record->id); ?>)" class="btn btn-danger mx-1">
                                                <span wire:loading.remove wire:target="set_action('rejected', <?php echo e($record->id); ?>)">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </span>
                                                <span wire:loading wire:target="set_action('rejected', <?php echo e($record->id); ?>)">
                                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                                </span>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <!--[if BLOCK]><![endif]--><?php if($status === 'placement' && $record->isSignedJobOffer): ?>
                                            <button wire:click="download_offer(<?php echo e($record->id); ?>)" class="btn btn-primary mx-1">
                                                <span wire:loading.remove wire:target="download_offer(<?php echo e($record->id); ?>)">
                                                    <i class="fa-solid fa-signature"></i>
                                                </span>
                                                <span wire:loading wire:target="download_offer(<?php echo e($record->id); ?>)">
                                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                                </span>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <!--[if BLOCK]><![endif]--><?php if($status === 'placement' && is_null($record->offer)): ?>
                                            <button wire:click="send_offer(false, <?php echo e($record->id); ?>)" class="btn btn-primary mx-1">
                                                <span wire:loading.remove wire:target="send_offer(false, <?php echo e($record->id); ?>)">
                                                    <i class="fa-regular fa-paper-plane"></i>
                                                </span>
                                                <span wire:loading wire:target="send_offer(false, <?php echo e($record->id); ?>)">
                                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                                </span>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <!--[if BLOCK]><![endif]--><?php if($status === 'onboarding'): ?>
                                            <button wire:click="set_checklist(false, <?php echo e($record->id); ?>)" class="btn btn-primary mx-1">
                                                <span wire:loading.remove wire:target="set_checklist(false, <?php echo e($record->id); ?>)">
                                                    <i class="fa-solid fa-list-check"></i>
                                                </span>
                                                <span wire:loading wire:target="set_checklist(false, <?php echo e($record->id); ?>)">
                                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                                </span>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <!--[if BLOCK]><![endif]--><?php if($status == 'hired'): ?> 
                                            <button wire:key="navigate-<?php echo e($record->id); ?>" wire:click="set_action('navigate', '<?php echo e($record->applicant->id); ?>')" class="btn btn-primary">
                                                <span wire:loading.remove wire:target="set_action('navigate', '<?php echo e($record->applicant->id); ?>')">
                                                    <i class="fa-solid fa-briefcase"></i>
                                                </span>
                                                <span wire:loading wire:target="set_action('navigate', '<?php echo e($record->applicant->id); ?>')">
                                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                                </span>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <!--[if BLOCK]><![endif]--><?php if(in_array($status, ['pending', 'interview', 'placement', 'onboarding'])): ?>
                                            <!--[if BLOCK]><![endif]--><?php if($status == 'interview' && $record->isInterviewResponded || $status == 'placement' && $record->isSignedJobOffer ): ?>
                                                <button wire:click="set_action('process', <?php echo e($record->id); ?>)" class="btn btn-primary mx-1">
                                                    <span wire:loading.remove wire:target="set_action('process', <?php echo e($record->id); ?>)">
                                                        <i class="fa-solid fa-arrow-right"></i>
                                                    </span>
                                                    <span wire:loading wire:target="set_action('process', <?php echo e($record->id); ?>)">
                                                        <i class="fa-solid fa-spinner fa-spin"></i>
                                                    </span>
                                                </button>  
                                            <?php elseif($status == 'onboarding' || $status == 'pending'): ?>
                                                <button wire:click="set_action('process', <?php echo e($record->id); ?>)" class="btn btn-primary mx-1">
                                                    <span wire:loading.remove wire:target="set_action('process', <?php echo e($record->id); ?>)">
                                                        <i class="fa-solid fa-arrow-right"></i>
                                                    </span>
                                                    <span wire:loading wire:target="set_action('process', <?php echo e($record->id); ?>)">
                                                        <i class="fa-solid fa-spinner fa-spin"></i>
                                                    </span>
                                                </button>  
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <button wire:click="set_action('delete', <?php echo e($record->id); ?>)" class="btn btn-danger mx-1">
                                                <span wire:loading.remove wire:target="set_action('delete', <?php echo e($record->id); ?>)">
                                                    <i class="fa-solid fa-trash"></i>
                                                </span>
                                                <span wire:loading wire:target="set_action('delete', <?php echo e($record->id); ?>)">
                                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                                </span>
                                            </button>           
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                                </tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <?php echo e($records->links(data: ['scrollTo' => false])); ?>

                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/job/applicant/index.blade.php ENDPATH**/ ?>