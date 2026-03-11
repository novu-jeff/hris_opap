<?php
    $employee = Auth::guard('employee')->user()->load(['personal', 'information.positions']);

    // Detect real profile photo
    $profilePhoto = null;
    if (!empty($employee->personal->profile)) {
        $storagePath = 'storage/' . $employee->personal->profile;
        if (file_exists(public_path($storagePath))) {
            $profilePhoto = asset($storagePath);
        }
    }

    // Fallback UI Avatar
    if (!$profilePhoto) {
        $profilePhoto = "https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name="
                        . urlencode($employee->personal->firstname . ' ' . $employee->personal->lastname);
    }
?>

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

$__html = app('livewire')->mount($__name, $__params, 'lw-1731800560-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>

            <!-- Profile Dropdown -->
            <?php if($employee): ?>
                <div class="nav-item dropdown flex-shrink-0" style="position: relative;">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-1 gap-lg-2"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">

                        <!-- Avatar -->
                        <img src="<?php echo e($profilePhoto); ?>"
                             class="rounded-circle border"
                             width="36"
                             height="36"
                             alt="Profile">

                        <!-- Name (always visible) -->
                        <span class="fw-bold text-uppercase "> 
                            <?php echo e($employee->personal->firstname); ?> <?php echo e($employee->personal->lastname); ?>

                        </span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item"
                               href="<?php echo e(route('employee.profile', ['form' => 'profile'])); ?>">
                                <i class="fa-solid fa-user me-2"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="<?php echo e(route('employee.logout')); ?>">
                                <i class="fa-solid fa-door-open me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>

        </div>
    </div>
</nav>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/components/employee/navbarnew.blade.php ENDPATH**/ ?>