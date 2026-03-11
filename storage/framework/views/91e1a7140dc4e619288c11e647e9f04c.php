<?php $__env->startSection('content'); ?>

<div class="container pb-5">
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('employee.dashboard');

$__html = app('livewire')->mount($__name, $__params, 'lw-487373189-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.employee', [
    'title' => 'ESS | Dashboard'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/employee/dashboard.blade.php ENDPATH**/ ?>