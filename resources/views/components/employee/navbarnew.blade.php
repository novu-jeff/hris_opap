@php
    $employee = Auth::guard('employee')->user()->load(['personal', 'information.positions']);

    // Detect real profile photo
    $profilePhoto = null;

    if (!empty($employee->personal->profile)) {
        // Example: employees/EMP-TEST-01/profile.jpg
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
@endphp

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid px-4">
        <!-- BRAND LOGO -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('employee.dashboard') }}">
            <img src="{{ asset('/img/' . $provider['client_logo']) }}" alt="Company Logo" style="height: 50px;">
            @if (config('app.product') === 'government')
                <img src="{{asset('img/bagong-pilipinas.png')}}" alt="Gov Logo" style="height: 50px;">
            @endif
        </a>

        <!-- TOGGLE BUTTON -->
        <button class="navbar-toggler" id="sidebarToggle">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- RIGHT SIDE -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarTopContent">
            <ul class="navbar-nav align-items-center gap-3">
                <!-- Notifications -->
                <li class="nav-item">
                    @livewire('notifications')
                </li>

                <!-- Profile -->
                @if($employee)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                            <!-- FINAL PROFILE PHOTO (REAL OR FALLBACK) -->
                            <img class="rounded-circle border" 
                                 src="{{ $profilePhoto }}" 
                                 alt="Profile" width="40" height="40">

                            <span class="d-none d-lg-inline fw-bold text-uppercase">
                                {{ $employee->personal->firstname }} {{ $employee->personal->lastname }}
                            </span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('employee.profile', ['form' => 'profile']) }}">
                                    <i class="fa-solid fa-user me-2"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('employee.logout') }}">
                                    <i class="fa-solid fa-door-open me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
