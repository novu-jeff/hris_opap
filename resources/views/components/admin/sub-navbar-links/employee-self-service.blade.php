<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-user-cog"></i>
        Employee Self Service
    </a>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{route('ess.leave')}}">Leave Applications</a></li>
        <li><a class="dropdown-item" href="{{route('ess.obs')}}">Official Business Slip Application</a></li>
        <li><a class="dropdown-item" href="{{route('ess.atro')}}">Authority To Render Overtime Application</a></li>
        <li><a class="dropdown-item" href="{{route('ess.announcements.index')}}">Announcements</a></li>
        <li><a class="dropdown-item" href="{{route('ess.approval-profile.index')}}">Employee Profile Approval</a></li>
        <li><a class="dropdown-item" href="{{route('ess.request-status')}}">Request Status</a></li>
    </ul>
</li>