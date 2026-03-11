<?php $__env->startSection('content'); ?>
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Payroll</h1>
        </div>
    </div>
    <div class="action">
        <ul class="nav nav-pills mb-3" id="employment-type-tab" role="tablist">
            <li class="nav-item d-flex text-uppercase fw-bold" role="presentation">
                <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $employment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('payroll.index', ['type' => $type, 'employment_type' => $key])); ?>"
                    class="nav-link border-2 border-primary <?php echo e($employment_type === $key ? 'active' : ''); ?>"
                    role="tab" aria-selected="<?php echo e($employment_type === $key ? 'true' : 'false'); ?>">
                        <?php echo e($employment['name']); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </li>
        </ul>

       
    </div>
    <div class="mt-3">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.payroll.index', ['employment_type' => $employment_type, 'type' => $type]);

$__html = app('livewire')->mount($__name, $__params, 'lw-342575953-0', $__slots ?? [], get_defined_vars());

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
<style>
    .nav-pills:nth-child(2) .nav-link.active, .nav-pills .show>.nav-link {
        color: #225F8B;
        background-color: transparent;
        border: 2px solid #225F8B;
    }
    .modal .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
        background-color: #225F8B !important;
        color: #fff !important;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', [
    'title' => 'HRIS | All Payroll'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/admin/payroll/index.blade.php ENDPATH**/ ?>