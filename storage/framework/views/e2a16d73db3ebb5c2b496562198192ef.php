<div>
    <div class="mt-5">
        <video width="100%" height="600" controls>
            <source src="<?php echo e(asset('tutorials/ESS Tutorial.mp4')); ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <hr class="my-4">
        <div class="text-uppercase my-5">
            <h4 class="fw-bold">Frequently Asked Questions (FAQs)</h4>
            <p>Here are the following questions that are frequently asked.</p>
        </div>
        <div class="accordion mt-5" id="employeeGuideAccordion">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $collapseId = "collapse" . ($index + 1);
                    $headingId = "heading" . ($index + 1);
                ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="<?php echo e($headingId); ?>">
                        <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo e($collapseId); ?>" aria-expanded="true" aria-controls="<?php echo e($collapseId); ?>">
                            <?php echo e($record['name']); ?>

                        </button>
                    </h2>
                    <div id="<?php echo e($collapseId); ?>" class="accordion-collapse collapse <?php echo e($index === 0 ? 'show' : ''); ?>" aria-labelledby="<?php echo e($headingId); ?>" data-bs-parent="#employeeGuideAccordion">
                        <div class="accordion-body">
                            <?php echo $record['description']; ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div><?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/employee/tutorial.blade.php ENDPATH**/ ?>