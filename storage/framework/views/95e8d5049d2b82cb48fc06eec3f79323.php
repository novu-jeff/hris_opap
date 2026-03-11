<nav class="navbar py-3">
    <div class="container d-flex justify-content-lg-center justify-content-xl-between px-4">
        <a class="navbar-brand text-uppercase" href="<?php echo e(url('/')); ?>">
            <img src="<?php echo e(asset('/img/' . $provider['client_logo'])); ?>">            
            <?php if(config('app.product') === 'government'): ?>
                <img src="<?php echo e(asset('img/bagong-pilipinas.png')); ?>" alt="" srcset="">
            <?php endif; ?>
        </a>
        <div class="d-flex align-items-center gap-5 pt-3">
            <ul class="navbar-nav">
                <div class="close-icon d-lg-none">
                    <i class="fa-solid fa-xmark"></i>
                </div>
                <?php if(!Route::is('password.request') && !Route::is('password.reset')): ?>
                    <li class="nav-item">
                        <a wire:navigate href="<?php echo e(route('home.index')); ?>" class="nav-link 
                        <?php echo e(request()->routeIs('home.index') || 
                            request()->routeIs('home.search.*') ||
                            request()->routeIs('home.view-job') ? 'active' : ''); ?>">
                            Find Jobs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a wire:navigate href="<?php echo e(route('home.applied')); ?>" class="nav-link <?php echo e(request()->routeIs('home.applied') || request()->routeIs('home.applied.*') == 'home.applied' ? 'active' : ''); ?>">
                            My Jobs
                        </a>
                    </li>
                    <?php if(!Auth::guard('applicant')->user()): ?>
                        <li class="nav-item">
                            <a wire:navigate href="<?php echo e(route('home.login')); ?>" class="btn btn-primary text-light py-2 px-4">
                                Login
                            </a>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item">
                        <a wire:navigate href="<?php echo e(route('employee.login')); ?>" class="btn btn-primary py-2 px-4">
                            Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <?php if(Auth::guard('applicant')->user() && !Route::is('password.request') && !Route::is('password.reset')): ?>
                <?php
                    $folder = strtolower(Auth::user()->firstname . '_' . Auth::user()->lastname . '_' . Auth::user()->id);
                ?>
                <div class="dropdown ms-3 d-none d-md-flex align-items-center gap-1 dropdown-toggle" data-bs-toggle="dropdown">
                    <img class="profile-img" src="<?php echo e(Auth::guard('applicant')->user()->image ? Storage::url('public/users/applicant/'. $folder . '/'. Auth::guard('applicant')->user()->image) : 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=10'); ?>" alt="Profile Image">  
                    <div class="name me-3">
                        <p class="fw-bold text-uppercase" style="margin-bottom: -4px;"><?php echo e(ucwords(Auth::guard('applicant')->user()->firstname . ' ' . Auth::guard('applicant')->user()->lastname ?? 'Guest')); ?></p>
                        <small class="text-uppercase fw-bold text-muted mb-0">Applicant</small>
                    </div>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="<?php echo e(route('home.profile')); ?>" wire:navigate>Profile</a></li>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('home.logout')); ?>"
                                onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                <?php echo e(__('Logout')); ?>

                            </a>

                            <form id="logout-form" action="<?php echo e(route('home.logout')); ?>" method="POST" class="d-none">
                                <?php echo csrf_field(); ?>
                            </form>
                        </li>
                    </ul>
                </div>  
            <?php endif; ?>
            <div class="hamburger-wrapper">
                <div class="hamburger"></div>
            </div>
            <?php echo $__env->make('components.home.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>        
    </div>
</nav><?php /**PATH /var/www/html/oppapru_hris/resources/views/components/home/navbar.blade.php ENDPATH**/ ?>