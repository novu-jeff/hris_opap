<?php $__env->startSection('content'); ?>
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Manage All Tranches</h1>
        </div>
        <div class="action">
            <div class="d-md-flex gap-3">
                <a href="<?php echo e(route('tranches.create')); ?>" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Add New</a>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.settings.tranches.index');

$__html = app('livewire')->mount($__name, $__params, 'lw-179720338-0', $__slots ?? [], get_defined_vars());

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

<style>
    .table-light td {
    font-weight: 600;
    }
 
</style>
<?php echo $__env->make('layouts.admin', [
    'title' => 'HRIS | All Tranches'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/admin/settings/tranches/index.blade.php ENDPATH**/ ?>