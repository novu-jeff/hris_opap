<nav class="navbar py-3">
    <div class="container d-flex justify-content-lg-center justify-content-xl-between px-4">
        <a class="navbar-brand text-uppercase" href="{{ url('/') }}">
            <img src="{{asset('img/logo.png')}}" alt="logo">
        </a>
        <div class="d-flex align-items-center gap-5 pt-3">
            <ul class="navbar-nav">
                <div class="close-icon d-lg-none">
                    <i class="fa-solid fa-xmark"></i>
                </div>
                <li class="nav-item">
                    <a wire:navigate href="{{route('home.index')}}" class="nav-link 
                    {{ request()->routeIs('home.index') || 
                        request()->routeIs('home.search.*') ||
                        request()->routeIs('home.view-job') ? 'active' : '' 
                    }}">
                        Find Jobs
                    </a>
                </li>
                <li class="nav-item">
                    <a wire:navigate href="{{route('home.applied')}}" class="nav-link {{request()->routeIs('home.applied') || request()->routeIs('home.applied.*') == 'home.applied' ? 'active' : ''}}">
                        My Jobs
                    </a>
                </li>
                <li class="nav-item">
                    <a wire:navigate href="#" class="nav-link">
                        Help Center
                    </a>
                </li>
                @if (!Auth::guard('applicants')->user())
                    <li class="nav-item">
                        <a wire:navigate href="{{route('login')}}" class="btn btn-primary py-2 px-4">
                            Login
                        </a>
                    </li>
                @endif
            </ul>
            @if (Auth::guard('applicants')->user())
                <div class="dropdown ms-3 d-none d-md-flex align-items-center gap-1 dropdown-toggle" data-bs-toggle="dropdown">
                    <img class="profile-img" src="{{
                                Auth::guard('applicants')->user()->image ? Storage::url('applicant/users/'.Auth::guard('applicants')->user()->id.'/'.Auth::guard('applicants')->user()->image) : 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=10'
                            }}" alt="Profile Image">
                    <div class="email">{{ trimEmail(Auth::guard('applicants')->user()->email ?? 'Guest')}}</div>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="{{route('home.profile.index')}}" wire:navigate>Profile</a></li>
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