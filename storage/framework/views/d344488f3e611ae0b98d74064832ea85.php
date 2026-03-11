<?php $__env->startSection('content'); ?>
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>All Positions</h1>
        </div>
        <div class="actions">
            <a href="<?php echo e(route('position.create')); ?>" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.settings.hris.position.index');

$__html = app('livewire')->mount($__name, $__params, 'lw-465263143-0', $__slots ?? [], get_defined_vars());

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
<?php echo $__env->make('layouts.admin', [
    'title' => 'HRIS | All Positions'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/admin/settings/hris/position/index.blade.php ENDPATH**/ ?>