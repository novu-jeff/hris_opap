@php
    $employee = Auth::guard('employee')->user()->load(['personal', 'information.positions']);
@endphp

<nav class="navbar py-3">
    <div class="container d-flex justify-content-lg-center justify-content-xl-between px-4">
        <a class="navbar-brand text-uppercase" href="{{ route('employee.dashboard') }}">
            <img src="{{ asset('/img/' . $provider['client_logo']) }}">            
            @if (config('app.product') === 'government')
                <img src="{{asset('img/bagong-pilipinas.png')}}" alt="" srcset="">
            @endif
        </a>
        <div class="menu d-flex align-items-center gap-5">
            @if (Auth::guard('employee')->user())
                <div class="ms-3 d-none d-md-flex align-items-center gap-1" data-bs-toggle="tooltip" title="Profile">
                    <img class="profile-img" style="width: 40px; height: 40px" src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode($employee->personal->firstname . ' ' . $employee->personal->lastname)}}" alt="Profile Image">
                    <div class="name" style="width: 180px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                        <p class="fw-bold text-uppercase mb-0" style="margin-bottom: -4px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                            {{$employee->personal->firstname . ' ' . $employee->personal->lastname}}
                        </p>
                        <small class="text-uppercase fw-bold text-muted mb-0" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                            {{$employee->information->positions->name ?? 'Employee'}}
                        </small>
                    </div>                     
                </div>  
            @endif
            <div class="d-flex align-items-center gap-4">
                @livewire('notifications')
                @if(!Route::is('employee.dashboard'))
                    <a wire:navigate href="{{ route('employee.dashboard') }}" class="notification ms-4" data-bs-toggle="tooltip" title="Dashboard">
                        <i class="fa-solid fa-house"></i>
                    </a>
                @endif
                <a href="{{ route('employee.logout') }}" class="notification ms-3" data-bs-toggle="tooltip" title="Logout">
                    <i class="fa-solid fa-door-open"></i>
                </a>
            </div>
        </div>        
    </div>
</nav>