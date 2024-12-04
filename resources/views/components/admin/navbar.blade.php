<nav class="navbar navbar-dark text-dark bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
            <img src="{{asset('img/logo.png')}}" alt="" srcset="" style="width: 120px;">
        </a>
        <div class="dropdown ms-3 d-none d-md-flex align-items-center gap-1 dropdown-toggle" data-bs-toggle="dropdown" style="cursor: pointer">
            <img class="profile-img" src="https://tse4.mm.bing.net/th?id=OIP.XcrLeJQyO0375j0Q8WA0kQHaE8&pid=Api&P=0&h=180" alt="Profile Image">
            <div class="email text-dark">{{ Auth::user()->email ?? 'Guest'}}</div>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
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
    </div>
</nav>