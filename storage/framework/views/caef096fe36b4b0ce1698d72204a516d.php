<div wire:poll>
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="text-uppercase mb-3">
            <h5 class="mb-2 fw-bold">Branch: <span class="ms-1 text-decoration-underline"><?php echo e($section['branch_name']); ?></span></h5>
            <h5 class="mb-2 fw-bold">Cluster: <span class="ms-1 text-decoration-underline"><?php echo e($section['department_name']); ?></span></h5>
            <h5 class="mb-2 fw-bold">Section: <span class="ms-1 text-decoration-underline"><?php echo e($section['section_name']); ?></span></h5>
            <hr class="mt-3 mb-3">

            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $section['positions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="mt-4 mb-2">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-uppercase fw-bold">Position: <?php echo e($position['position_name']); ?></h5>
                        <h5 class="text-uppercase text-muted fw-bold">
                            (<?php echo e(count($position['employees'])); ?> Employee<?php echo e(count($position['employees']) > 1 ? 's' : ''); ?>)
                        </h5>
                    </div>

                    <div class="row">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $position['employees']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $personal = $employee['personal'] ?? null;
                                $profile = $personal['profile'] ?? null;
                                $fullname = $personal ? ($personal['firstname'] . ' ' . $personal['lastname']) : 'No Name';
                            ?>

                            <div class="col-12 col-md-6 mb-4">
                                <div class="d-md-flex align-items-center gap-3">
                                    <div>
                                        <!--[if BLOCK]><![endif]--><?php if($profile && file_exists(public_path('storage/' . $profile))): ?>
                                            <img src="<?php echo e(asset('storage/' . $profile)); ?>" 
                                                 alt="Profile Photo" 
                                                 style="width: 60px; height: 100px; object-fit: cover; border-radius: 5px;">
                                        <?php else: ?>
                                            <img src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name=<?php echo e(urlencode($fullname)); ?>" 
                                                 alt="Avatar" 
                                                 style="width: 60px; height: 100px; object-fit: cover; border-radius: 5px;">
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <ul class="list-unstyled mb-0 fs-6">
                                        <li>Full Name: <strong><?php echo e(ucwords($fullname)); ?></strong></li>
                                        <li>Email: <strong><?php echo e($employee['account']['email'] ?? $employee['account']['email_id'] ?? 'No Email'); ?></strong></li>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/employee/team.blade.php ENDPATH**/ ?>