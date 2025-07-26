<nav class="navbar navbar-dark text-dark bg-light shadow-sm">
    <div class="container px-4">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('/img/' . $provider['client_logo']) }}">            
            @if (config('app.product') === 'government')
                <img src="{{asset('img/bagong-pilipinas.png')}}" alt="" srcset="">
            @endif
        </a>
        <div class="d-flex align-items-center gap-5">
            <div class="dropdown ms-3 d-none d-md-flex align-items-start gap-1 dropdown-toggle" data-bs-toggle="dropdown" style="cursor: pointer">
                <img class="profile-img" style="width: 40px; height: 40px" src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode(Auth::user()->name)}}" alt="Profile Image">
                <div class="name">
                    <p class="fw-bold text-uppercase mb-0" style="margin-bottom: -4px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                        {{Auth::user()->name}}
                    </p>
                    <small class="text-uppercase fw-bold text-muted mb-0" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                        {{ Auth::user()->getRoleNames()->first() }}
                    </small>
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
            <div>
                <div class="hamburger-wrapper">
                    <div class="hamburger"></div>
                </div>
            </div>
            @livewire('notifications')
            @include('components.admin.sidebar')
        </div>
    </div>
</nav>