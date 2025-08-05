<nav class="sub-navbar navbar navbar-expand-xl position-sticky top-0 bg-light">
    <div class="container px-5">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- HRIS -->
                <li class="nav-item">
                    <a class="nav-link" href="{{route('admin.dashboard')}}">
                        <i class="fa-solid fa-house"></i>
                        Dashboard
                    </a>
                </li>

                <!-- Recruitment -->
                @include('components.admin.sub-navbar-links.recruitment')

                <!-- HRIS -->
                @can('read hris')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('hris.index')}}">
                            <i class="fa-solid fa-users"></i>
                            HRIS
                        </a>
                    </li>
                @endcan

                @include('components.admin.sub-navbar-links.timelogs')

                <!-- Payroll -->
                {{-- <li class="nav-item">
                    <a class="nav-link" href="{{route('payroll.index')}}">
                        <i class="fa-solid fa-dollar-sign"></i>
                        Payroll
                    </a>
                </li> --}}

                <!-- Employee Self Service -->
                @include('components.admin.sub-navbar-links.employee-self-service')

                <!-- Reports -->
                @include('components.admin.sub-navbar-links.reports')
                
                <!-- Settings -->
                @include('components.admin.sub-navbar-links.settings')

                {{-- Others  --}}
                {{-- @include('components.admin.sub-navbar-links.others') --}}

            </ul>
        </div>
    </div>
</nav>