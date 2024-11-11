<nav class="navbar py-3">
    <div class="container d-flex justify-content-lg-center justify-content-xl-between px-4">
        <a class="navbar-brand text-uppercase" href="{{ url('/') }}">
            <img src="{{asset('img/logo.png')}}" alt="logo">
        </a>
        <div class="d-flex align-items-center gap-5 pt-3">
            @if (Auth::guard('employee')->user())
                @php
                    $folder = strtolower(Auth::user()->firstname . '_' . Auth::user()->lastname . '_' . Auth::user()->id);
                @endphp
                <div class="dropdown ms-3 d-none d-md-flex align-items-center gap-1 dropdown-toggle" data-bs-toggle="dropdown">
                    <img class="profile-img" src="{{
                        Auth::guard('employee')->user()->image ? Storage::url('public/users/employee/'. $folder . '/'. Auth::guard('employee')->user()->image) : 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=10'
                    }}" alt="Profile Image">
                    <div class="email">{{ trimEmail(Auth::guard('employee')->user()->email ?? 'Guest')}}</div>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item" href="{{ route('home.logout') }}"
                                onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('home.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>  
            @endif
            <div class="hamburger-wrapper">
                <div class="hamburger"></div>
            </div>
        </div>        
    </div>
</nav>