<?php $__env->startSection('content'); ?>
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="mt-5 d-lg-flex justify-content-between align-items-start">
        <div class="section-title">
            <h1><?php echo e($header); ?></h1>
            <p><?php echo e($sub); ?></p>
        </div>
        <div class="action">
            <?php if($action === 'view'): ?>
                <div class="d-md-flex gap-3 ">
                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('write apply-time-adjustments')): ?>
                        <a href="<?php echo e(route('employee.time-adjustments.apply')); ?>" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Apply Now</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                
            <?php endif; ?>
        </div>
    </div>
    <div class="mt-3">
        <?php if($action == 'view'): ?>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('employee.time-adjustments.index');

$__html = app('livewire')->mount($__name, $__params, 'lw-1681850781-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php else: ?> 
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('employee.time-adjustments.apply', [
                'record_id' => $id ?? null
            ]);

$__html = app('livewire')->mount($__name, $__params, 'lw-1681850781-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php endif; ?>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.employee', [
    'title' => $title
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/employee/time-adjustments.blade.php ENDPATH**/ ?>