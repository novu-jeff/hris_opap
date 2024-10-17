<nav class="navbar navbar-dark bg-primary shadow-sm ">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <div class="d-flex align-items-center">
            <img class="profile-img" src="https://tse4.mm.bing.net/th?id=OIP.XcrLeJQyO0375j0Q8WA0kQHaE8&pid=Api&P=0&h=180" alt="Profile Image">
            <div class="email">{{ Auth::user()->email ?? 'test@gmail.com'}}</div>
            <div class="dropdown ms-3">
                <button class="drop-button d-flex align-items-center justify-content-center dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"></button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>  
        </div>        
    </div>
</nav>