<div wire:ignore>
    <div class="accordion" id="accordionExample">
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recordIndex => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <!--[if BLOCK]><![endif]--><?php if(isset($record['branch_id'])): ?>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingBranch<?php echo e($record['branch_id']); ?>">
                        <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBranch<?php echo e($record['branch_id']); ?>" aria-expanded="<?php echo e($recordIndex === 0 ? 'true' : 'false'); ?>" aria-controls="collapseBranch<?php echo e($record['branch_id']); ?>">
                            <?php echo e($record['branch_name']); ?>

                        </button>
                    </h2>
                    <div id="collapseBranch<?php echo e($record['branch_id']); ?>" class="accordion-collapse collapse <?php echo e($recordIndex === 0 ? 'show' : ''); ?>" aria-labelledby="headingBranch<?php echo e($record['branch_id']); ?>" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $record['departments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingDepartment<?php echo e($department['department_id']); ?>">
                                        <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDepartment<?php echo e($department['department_id']); ?>" aria-expanded="false" aria-controls="collapseDepartment<?php echo e($department['department_id']); ?>">
                                            <?php echo e($department['department_name']); ?>

                                        </button>
                                    </h2>
                                    <div id="collapseDepartment<?php echo e($department['department_id']); ?>" class="accordion-collapse collapse show" aria-labelledby="headingDepartment<?php echo e($department['department_id']); ?>" data-bs-parent="#collapseBranch<?php echo e($record['branch_id']); ?>">
                                        <div class="accordion-body">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $department['sections']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="headingSection<?php echo e($section['section_id']); ?>">
                                                        <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSection<?php echo e($section['section_id']); ?>" aria-expanded="false" aria-controls="collapseSection<?php echo e($section['section_id']); ?>">
                                                            <div class="d-flex justify-content-between w-100 pe-4">
                                                                <div>
                                                                    <?php echo e($section['section_name']); ?> 
                                                                </div>
                                                                <?php 
                                                                    $empCount = !empty($section['employees']) ? count($section['employees']) : 0;
                                                                ?>
                                                                <div class="text-muted">
                                                                    <?php echo e($empCount); ?> Employee<?php echo e($empCount > 1 ? 's' : ''); ?>

                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapseSection<?php echo e($section['section_id']); ?>" class="accordion-collapse collapse <?php echo e($recordIndex === 0 ? 'show' : ''); ?>" aria-labelledby="headingSection<?php echo e($section['section_id']); ?>" data-bs-parent="#collapseDepartment<?php echo e($department['department_id']); ?>">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                               <!--[if BLOCK]><![endif]--><?php if(!empty($section['employees'])): ?>
                                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $section['employees']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    
                                                                    <div class="col-12 col-md-6 mb-4">
                                                                        <div class="d-lg-flex align-items-center justify-content-center justify-content-lg-start gap-3">
                                                                            <div class="mb-3 mb-lg-0">
                                                                                <?php
    $fname = data_get($employee, 'personal.firstname', 'Unknown');
    $lname = data_get($employee, 'personal.lastname', '');
?>
                                                                                <img style="width: 80px; height: 80px;"
     src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name=<?php echo e(urlencode($fname . ' ' . $lname)); ?>">
                                                                            </div>
                                                                            <ul class="list-unstyled mb-0">
                                                                                <li>Employee No: <strong><?php echo e($employee['employee_no']); ?></strong></li>
                                                                                <li>Full Name: <strong><?php echo e(ucwords($fname . ' ' . $lname)); ?></strong></li>
                                                                                <li>Email: <strong><?php echo e(data_get($employee, 'account.email', 'N/A')); ?></strong></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>
            <?php elseif(isset($record['group_name'])): ?>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingUnassigned">
                        <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUnassigned" aria-expanded="false" aria-controls="collapseUnassigned">
                            <?php echo e($record['group_name']); ?>

                        </button>
                    </h2>
                    <div id="collapseUnassigned" class="accordion-collapse collapse show" aria-labelledby="headingUnassigned" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="row">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $record['employees']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    
                                    <div class="col-12 col-md-6 mb-4">
                                        <div class="d-lg-flex align-items-center justify-content-center justify-content-lg-start gap-3">
                                            <div class="mb-3 mb-lg-0">
                                                <img style="width: 80px; height: 80px;"
                                                    src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name=<?php echo e(urlencode($employee['personal']['firstname'] . ' ' . $employee['personal']['lastname'])); ?>">              
                                            </div>
                                            <ul class="list-unstyled mb-0">
                                                <li>Employee No: <strong><?php echo e($employee['employee_no']); ?></strong></li>
                                                <li>Full Name: <strong><?php echo e(ucwords($employee['personal']['firstname'] . ' ' . $employee['personal']['lastname'])); ?></strong></li>
                                                <li>Email: <strong><?php echo e($employee['account']['email']); ?></strong></li>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/employee/directory.blade.php ENDPATH**/ ?>