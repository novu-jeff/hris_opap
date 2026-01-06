<nav class="navbar navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid d-flex justify-content-between align-items-center px-2 px-lg-4">

        <!-- LEFT: Sidebar toggle -->
        <button class="btn d-lg-none me-2" id="sidebarToggle">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- RIGHT SIDE: Notifications + Profile -->
        <div class="d-flex align-items-center gap-2 gap-lg-3 flex-wrap" style="overflow: visible;">

            <!-- Notifications -->
            <div class="flex-shrink-0">
                @livewire('notifications')
            </div>

            <!-- Profile Dropdown -->
            <div class="dropdown flex-shrink-0" style="position: relative;">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                    <img class="rounded-circle border"
                         style="width: 36px; height: 36px"
                         src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode(Auth::user()->name)}}"
                         alt="Profile">

                    <div class="d-flex flex-column">
                        <span class="fw-bold text-uppercase text-truncate" style="max-width: 100px;">
                            {{ Auth::user()->name }}
                        </span>
                        <small class="text-uppercase fw-bold text-muted mb-0 text-truncate" style="max-width: 100px;">
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
            </div>

        </div>
    </div>
</nav>
