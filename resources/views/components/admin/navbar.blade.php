<nav class="navbar navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid px-2 px-lg-4 d-flex align-items-center">

        <!-- LEFT: Sidebar toggle (mobile) -->
        <button class="btn d-lg-none me-2" id="sidebarToggle">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- RIGHT: Always visible -->
        <div id="navbarTopContent"
             class="ms-auto d-flex align-items-center gap-1 gap-lg-3 flex-nowrap">

            <!-- Notifications -->
            <div class="nav-item flex-shrink-0">
                @livewire('notifications')
            </div>

            <!-- Profile Dropdown -->
      
                <div class="nav-item dropdown flex-shrink-0" style="position: relative;">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-1 gap-lg-2"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false" title="{{ Auth::user()->name }}">

                        <!-- Avatar -->
                        <img class="profile-img" style="width: 40px; height: 40px"
                            src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode(Auth::user()->name)}}" 
                            alt="Profile Image">

                        <!-- Name (always visible) -->
                        <span class="fw-bold text-uppercase text-truncate" style="max-width: 100px;">
                             {{ Auth::user()->name }}
                        </span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        
                        <li>
                             <a class="dropdown-item" href="{{ route('home.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        </li>
                    </ul>
                </div>
         

        </div>
    </div>
</nav>
