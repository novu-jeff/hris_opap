@php
    $employee = Auth::guard('employee')->user()->load(['personal', 'information.positions']);
@endphp

<nav class="navbar py-3">
    <div class="container d-flex justify-content-lg-center justify-content-xl-between px-4">
        <a class="navbar-brand text-uppercase" href="{{ route('employee.dashboard') }}">
            <img src="{{asset('img/client-logo.png')}}" alt="logo">
            <img src="{{asset('img/bagong-pilipinas.png')}}" alt="" srcset="">
        </a>
        <div class="menu d-flex align-items-center gap-5">
            @if (Auth::guard('employee')->user())
                <div class="ms-3 d-none d-md-flex align-items-center gap-1">
                    <img class="profile-img" style="width: 40px; height: 40px" src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode($employee->personal->firstname . ' ' . $employee->personal->lastname)}}" alt="Profile Image">
                    <div class="name">
                        <p class="fw-bold text-uppercase" style="margin-bottom: -4px;">{{$employee->personal->firstname}}</p>
                        <small class="text-uppercase fw-bold text-muted mb-0">{{$employee->information->positions->name ?? 'Employee'}}</small>
                    </div>                     
                </div>  
            @endif
            <div class="d-flex align-items-center gap-4">
                @livewire('notifications')
                @if(!Route::is('employee.dashboard'))
                    <a wire:navigate href="{{ route('employee.dashboard') }}" class="notification ms-4">
                        <i class="fa-solid fa-house"></i>
                    </a>
                @endif
                <a href="{{ route('employee.logout') }}" class="notification ms-3">
                    <i class="fa-solid fa-door-open"></i>
                </a>
            </div>
        </div>        
    </div>
</nav>