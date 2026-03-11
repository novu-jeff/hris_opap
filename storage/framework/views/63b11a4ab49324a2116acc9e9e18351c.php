<div>
    <div class="card mb-4">
        <div class="card-body  px-5">
                <ul class="nav nav-pills mb-3 d-flex justify-content-center gap-3 py-4" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('employee.profile', ['form' => 'personal'])); ?>" class="px-4 py-2 text-uppercase fw-bold nav-link <?php echo e($form == 'personal' ? 'active' : ''); ?>" id="pills-personal-tab" role="tab" aria-controls="pills-personal" aria-selected="<?php echo e($form == 'personal' ? 'true' : 'false'); ?>">I. Personal Information</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('employee.profile', ['form' => 'family'])); ?>" class="px-4 py-2 text-uppercase fw-bold nav-link <?php echo e($form == 'family' ? 'active' : ''); ?>" id="pills-family-tab" role="tab" aria-controls="pills-family" aria-selected="<?php echo e($form == 'family' ? 'true' : 'false'); ?>">II. Family Background (A)</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('employee.profile', ['form' => 'children'])); ?>" class="px-4 py-2 text-uppercase fw-bold nav-link <?php echo e($form == 'children' ? 'active' : ''); ?>" id="pills-children-tab" role="tab" aria-controls="pills-children" aria-selected="<?php echo e($form == 'children' ? 'true' : 'false'); ?>">II. Family Background (B)</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('employee.profile', ['form' => 'education'])); ?>" class="px-4 py-2 text-uppercase fw-bold nav-link <?php echo e($form == 'education' ? 'active' : ''); ?>" id="pills-education-tab" role="tab" aria-controls="pills-education" aria-selected="<?php echo e($form == 'education' ? 'true' : 'false'); ?>">III. Educational Background</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('employee.profile', ['form' => 'civil-service'])); ?>" class="px-4 py-2 text-uppercase fw-bold nav-link <?php echo e($form == 'civil-service' ? 'active' : ''); ?>" id="pills-civil-service-tab" role="tab" aria-controls="pills-civil-service" aria-selected="<?php echo e($form == 'civil-service' ? 'true' : 'false'); ?>">IV. Civil Service Eligibility</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('employee.profile', ['form' => 'employment-history'])); ?>" class="px-4 py-2 text-uppercase fw-bold nav-link <?php echo e($form == 'employment-history' ? 'active' : ''); ?>" id="pills-employment-tab" role="tab" aria-controls="pills-history" aria-selected="<?php echo e($form == 'employment' ? 'true' : 'false'); ?>">V. Work Experience</a>
                </li>
                    <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('employee.profile', ['form' => 'other-works'])); ?>" class="px-4 py-2 text-uppercase fw-bold nav-link <?php echo e($form == 'other-works' ? 'active' : ''); ?>" id="pills-other-works-tab" role="tab" aria-controls="pills-others" aria-selected="<?php echo e($form == 'other-works' ? 'true' : 'false'); ?>">VI. Voluntary Work or Involvement in Civic / Non-Government / People / Voluntary Organizations</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('employee.profile', ['form' => 'trainings'])); ?>" class="px-4 py-2 text-uppercase fw-bold nav-link <?php echo e($form == 'trainings' ? 'active' : ''); ?>" id="pills-trainings-tab" role="tab" aria-controls="pills-trainings" aria-selected="<?php echo e($form == 'trainings' ? 'true' : 'false'); ?>">VII. Learning and Development (L&D) Interventions / Training Programs Attended</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('employee.profile', ['form' => 'skills'])); ?>" class="px-4 py-2 text-uppercase fw-bold nav-link <?php echo e($form == 'skills' ? 'active' : ''); ?>" id="pills-skills-tab" role="tab" aria-controls="pills-skills" aria-selected="<?php echo e($form == 'skills' ? 'true' : 'false'); ?>">VIII. Skills or Hobbies</a>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <?php
                    $viewForms = [
                        'personal' => 'employee.profile.personal',
                        'family' => 'employee.profile.family',
                        'children' => 'employee.profile.children',
                        'education' => 'employee.profile.education',
                        'employment-history' => 'employee.profile.employment',
                        'civil-service' => 'employee.profile.civil-service',
                        'trainings' => 'employee.profile.trainings',
                        'other-works' => 'employee.profile.other-works',
                        'skills' => 'employee.profile.skills',
                    ];

                    $view = $viewForms[$form];
                ?>
                <hr class="pt-2">
                <div class="mt-4">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split($view);

$__html = app('livewire')->mount($__name, $__params, 'lw-1843524921-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/employee/profile.blade.php ENDPATH**/ ?>