<?php $__env->startSection('content'); ?>

<div class="container pb-5">
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.dashboard.index');

$__html = app('livewire')->mount($__name, $__params, 'lw-3820387984-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(function() {
            var swiperOptions = {
            slidesPerView: 3,
            spaceBetween: 20, 
            pagination: {
                el: '.swiper-pagination',
                clickable: false, 
            },
            freeMode: true, 
            };

            function updateSwiperOptions() {
                if (window.matchMedia("(min-width: 0px) and (max-width: 992px)").matches) {
                    swiperOptions.slidesPerView = 1;
                } else {
                    swiperOptions.slidesPerView = 3;
                }
                new Swiper('.swiper-container', swiperOptions);
            }

            updateSwiperOptions();

            $(window).resize(function() {
            updateSwiperOptions();
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', [
    'title' => 'HRIS | Dashboard'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>