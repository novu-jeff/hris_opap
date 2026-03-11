<nav class="navbar navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid px-2 px-lg-4 d-flex align-items-center">

        <!-- LEFT: Sidebar toggle (mobile) -->
        <button class="btn d-lg-none me-2" id="sidebarToggle">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- RIGHT: Always visible -->
        <div id="navbarTopContent"
             class="ms-auto d-flex align-items-center gap-1 gap-lg-3 flex-nowrap">

            <!-- Notifications -->
            <div class="nav-item flex-shrink-0">
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('notifications');

$__html = app('livewire')->mount($__name, $__params, 'lw-3225833175-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>

            <!-- Profile Dropdown -->
      
                <div class="nav-item dropdown flex-shrink-0" style="position: relative;">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-1 gap-lg-2"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false" title="<?php echo e(Auth::user()->name); ?>">

                        <!-- Avatar -->
                        <img class="profile-img" style="width: 40px; height: 40px"
                            src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name=<?php echo e(urlencode(Auth::user()->name)); ?>" 
                            alt="Profile Image">

                        <!-- Name (always visible) -->
                        <span class="fw-bold text-uppercase ">
                             <?php echo e(Auth::user()->name); ?>

                        </span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        
                        <li>
                             <a class="dropdown-item" href="<?php echo e(route('home.logout')); ?>"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="<?php echo e(route('admin.logout')); ?>" method="POST" class="d-none">
                            <?php echo csrf_field(); ?>
                        </form>
                        </li>
                    </ul>
                </div>
         

        </div>
    </div>
</nav>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/components/admin/navbar.blade.php ENDPATH**/ ?>