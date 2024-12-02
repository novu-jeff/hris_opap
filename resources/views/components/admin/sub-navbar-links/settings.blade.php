<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-cogs"></i>
        Settings
    </a>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{route('company.index')}}">Company Information</a></li>
        <li class="nav-item dropstart">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Recruitment
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{route('job.interview.index')}}#">Assessment</a></li>
                <li><a class="dropdown-item" href="{{route('job.requirements.index')}}">Requirements</a></li>
            </ul>
        </li>
        <li class="nav-item dropstart">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                User Management
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{route('users.index', ['type' => 'applicants'])}}">Users</a></li>
                <li><a class="dropdown-item" href="#">Access Management</a></li>
            </ul>
        </li>
        <li class="nav-item dropstart">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                HRIS
            </a>
            <ul class="dropdown-menu">
                <li class="nav-item dropstart">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Location Management
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{route('branch.index')}}">Branches</a></li>
                        <li><a class="dropdown-item" href="{{route('department.index')}}">Departments</a></li>
                        <li><a class="dropdown-item" href="{{route('section.index')}}">Sections</a></li>
                    </ul>
                </li>
                <li><a class="dropdown-item" href="{{route('bank-information.index')}}">Bank Information</a></li>
                <li><a class="dropdown-item" href="{{route('batch-configuration.index')}}">Batch Configuration</a></li>
                <li><a class="dropdown-item" href="{{route('employee-status.index')}}">Employee Status</a></li>
                <li><a class="dropdown-item" href="{{route('position.index')}}">Positions</a></li>
                <li><a class="dropdown-item" href="{{route('violation.index')}}">Violations</a></li>
                <li><a class="dropdown-item" href="{{route('leave.index')}}">Leaves</a></li>
                <li><a class="dropdown-item" href="{{route('gsis.index')}}">GSIS Billings</a></li>
                <li><a class="dropdown-item" href="{{route('other-earnings.index')}}">Earnings</a></li>
                <li><a class="dropdown-item" href="{{route('other-deductions.index')}}">Deductions</a></li>
            </ul>
        </li>
        <li class="nav-item dropstart">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Timekeeping
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Shift Schedule</a></li>
                <li><a class="dropdown-item" href="#">Employee Schedule</a></li>
            </ul>
        </li>
        <li class="nav-item dropstart">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Payroll
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Holidays</a></li>
                <li><a class="dropdown-item" href="#">Earnings</a></li>
                <li><a class="dropdown-item" href="#">Deduction</a></li>
                <li><a class="dropdown-item" href="#">Payroll Period</a></li>
                <li><a class="dropdown-item" href="#">Payroll Configuration</a></li>
            </ul>
        </li>
    </ul>
</li>