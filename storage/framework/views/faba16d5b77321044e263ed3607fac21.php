<?php $__env->startSection('content'); ?>
<div class="container mt-5 pb-5">
    <div class="mt-5">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('home.jobs', [
            'lazy' => true,
            'search_query' => $parameter,
        ]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3816007576-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', [
    'title' => 'HRIS | All Jobs'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/home/index.blade.php ENDPATH**/ ?>