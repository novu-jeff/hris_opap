<nav class="sub-navbar navbar navbar-expand-lg position-sticky top-0 bg-light">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- HRIS -->
                <li class="nav-item">
                    <a class="nav-link" href="{{route('admin.dashboard')}}">
                        <i class="fa-solid fa-house"></i>
                        Dashboard
                    </a>
                </li>

                <!-- recruitment -->
                @include('components.admin.sub-navbar-links.recruitment')

                <!-- HRIS -->
                <li class="nav-item">
                    <a class="nav-link" href="{{route('hris.index')}}">
                        <i class="fa-solid fa-users"></i>
                        HRIS
                    </a>
                </li>

                @include('components.admin.sub-navbar-links.timelogs')

                <!-- HRIS -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fa-solid fa-dollar-sign"></i>
                        Payroll
                    </a>
                </li>

                <!-- Employee Self Service -->
                @include('components.admin.sub-navbar-links.employee-self-service')

                <!-- Reports -->
                @include('components.admin.sub-navbar-links.reports')
                
                <!-- Settings -->
                @include('components.admin.sub-navbar-links.settings')

            </ul>
        </div>
    </div>
</nav>