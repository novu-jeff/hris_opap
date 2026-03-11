<?php $__env->startSection('content'); ?>
    <div class="register">
        <div class="d-flex">
            <div class="left-side">
                <div class="container mb-5">
                    <div class="logo">
                        <img src="<?php echo e(asset('/img/' . $provider['client_logo'])); ?>">
                    </div>
                    <div class="description mt-5 mx-5">
                        <h3>Create Account</h3>
                        <p>
                            Register now to take the first step toward exciting job opportunities and contribute to a thriving professional community. Join us in shaping your career journey!
                        </p>
                    </div>
                    <div class="actions d-flex justify-content-center gap-4 mt-4">
                        <a wire:navigate href="<?php echo e(route('home.index')); ?>" class="btn btn-outline-primary px-5 py-2">Go Home</a>
                        <a wire:navigate href="<?php echo e(route('home.login')); ?>" class="btn btn-primary px-5 py-2">Login</a>
                    </div>
                </div>
            </div>
            <div class="right-side py-5">
                <div class="container mx-5">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('home.register');

$__html = app('livewire')->mount($__name, $__params, 'lw-584595268-0', $__slots ?? [], get_defined_vars());

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', [
    'title' => 'HRIS | Applicant Register'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/auth/home/register.blade.php ENDPATH**/ ?>