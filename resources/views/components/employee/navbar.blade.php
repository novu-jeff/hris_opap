@php
    $employee = Auth::guard('employee')->user()->load(['personal', 'information.positions']);
@endphp

<nav class="navbar py-3">
    <div class="container d-flex justify-content-lg-center justify-content-xl-between px-4">
        <a class="navbar-brand text-uppercase" href="{{ route('employee.dashboard') }}">
            <img src="{{asset('img/logo.png')}}" alt="logo">
        </a>
        <div class="d-flex align-items-center gap-5 pt-3">
            @if (Auth::guard('employee')->user())
                @php
                    $folder = strtolower(Auth::user()->firstname . '_' . Auth::user()->lastname . '_' . Auth::user()->id);
                @endphp
                <div class="ms-3 d-none d-md-flex align-items-center gap-1">
                    <img class="profile-img" src="{{
                        Auth::guard('employee')->user()->image ? Storage::url('public/users/employee/'. $folder . '/'. Auth::guard('employee')->user()->image) : 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=10'
                    }}" alt="Profile Image">
                    <div class="name">
                        <p class="fw-bold text-uppercase" style="margin-bottom: -4px;">{{$employee->personal->firstname . ' ' . $employee->personal->lastname}}</p>
                        <small class="text-uppercase fw-bold text-muted mb-0">{{$employee->information->positions->name}}</small>
                    </div>                     
                </div>  
            @endif
            <div class="hamburger-wrapper">
                <div class="hamburger"></div>
            </div>
        </div>        
    </div>
</nav>