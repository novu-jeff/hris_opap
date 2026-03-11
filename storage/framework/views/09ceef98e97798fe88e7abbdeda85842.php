<?php $__env->startSection('content'); ?>
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="mt-5 d-lg-flex justify-content-between align-items-start">
        <div class="section-title">
            <h1><?php echo e($header); ?></h1>
            <p><?php echo e($sub); ?></p>
        </div>
        <div class="action">
            <?php if($action === 'index'): ?>
                <div class="d-md-flex gap-3">
                    <!-- <a href="<?php echo e(route('employee.dashboard')); ?>" class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a> -->
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="mt-3">
        <small class="text-uppercase fw-bold">Note: Please make sure to enable your browser's camera</small>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('employee.clock');

$__html = app('livewire')->mount($__name, $__params, 'lw-2491175823-0', $__slots ?? [], get_defined_vars());

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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.employee', [
    'title' => $title
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/employee/clock.blade.php ENDPATH**/ ?>