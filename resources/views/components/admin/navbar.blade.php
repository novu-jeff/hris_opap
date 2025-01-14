<nav class="navbar navbar-dark text-dark bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
            <img src="{{asset('img/logo.png')}}" alt="" srcset="" style="width: 120px;">
        </a>
        <div class="d-flex align-items-center gap-5">
            <div class="dropdown ms-3 d-none d-md-flex align-items-start gap-1 dropdown-toggle" data-bs-toggle="dropdown" style="cursor: pointer">
                <img class="profile-img" style="width: 40px; height: 40px" src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode(Auth::user()->name)}}" alt="Profile Image">
                <div class="name">
                    <p class="fw-bold text-uppercase" style="margin-bottom: -4px;">{{Auth::user()->name}}</p>
                    <small class="text-uppercase fw-bold text-muted mb-0">{{ Auth::user()->getRoleNames()->first() }}</small>
                </div>   
                <ul class="dropdown-menu mt-3" aria-labelledby="dropdownMenuButton">
                    <li>
                        <a class="dropdown-item" href="{{ route('home.logout') }}"
                            onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>    
            @livewire('notifications')
        </div>
    </div>
</nav>