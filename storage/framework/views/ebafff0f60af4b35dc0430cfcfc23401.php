<?php $__env->startSection('content'); ?>
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1><?php echo e($header); ?></h1>
            <p><?php echo e($sub); ?></p>
        </div>
        <div class="action">
            <?php if($action === 'view'): ?>
                <div class="d-md-flex gap-3">
                    <a href="<?php echo e(route('ess.faqs.create')); ?>" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Add New</a>
                </div>
            <?php else: ?>
                <!-- <a href="<?php echo e(route('ess.faqs.index')); ?>" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a> -->
            <?php endif; ?>
        </div>
    </div>
    <div class="mt-3">
        <?php if($action == 'view'): ?>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.ess.faqs.index');

$__html = app('livewire')->mount($__name, $__params, 'lw-2576205582-0', $__slots ?? [], get_defined_vars());

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
[$__name, $__params] = $__split('admin.ess.faqs.add', [
                'record_id' => $id ?? null
            ]);

$__html = app('livewire')->mount($__name, $__params, 'lw-2576205582-1', $__slots ?? [], get_defined_vars());

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

<?php $__env->startSection('script'); ?>
<script>
    $(function() {

        ckeditor();

    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', [
    'title' => $title
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/admin/ess/faqs/index.blade.php ENDPATH**/ ?>