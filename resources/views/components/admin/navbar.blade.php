

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid px-4">
       
          <!-- Left side: Hamburger -->
        <div class="hamburger-wrapper me-3" id="sidebarToggle">
            <div class="hamburger"></div>
        </div>

        

        <!-- RIGHT SIDE -->
        <div class="collapse navbar-collapse justify-content-end d-none d-lg-flex"id="navbarTopContent">
            <ul class="navbar-nav align-items-center gap-3">
                <!-- Notifications -->
                <li class="nav-item">
                      @livewire('notifications')
                </li>

                
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                            <img class="profile-img" style="width: 40px; height: 40px"
                            src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode(Auth::user()->name)}}" 
                            alt="Profile Image">

                            <div class="d-flex flex-column">
                                <span class="d-none d-lg-inline fw-bold text-uppercase">
                                    {{ Auth::user()->name }}
                                </span>
                                <small class="text-uppercase fw-bold text-muted mb-0"
                                    style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                                        {{ Auth::user()->getRoleNames()->first() }}
                                </small>
                            </div>
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
                    </li>
               
            </ul>
        </div>
    </div>
</nav>
