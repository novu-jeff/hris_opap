<div>
    <div class="latest-announcements">
        <div class="wrapper d-flex gap-5">
            @if($announcements)
                <div class="card bg-primary text-white shadow text-decoration-none text-uppercase fw-bold me-1">
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div class="text-center">
                            <small class="card-title text-clamp clamp-3">Latest Announcements</small>
                            <small>(Scroll Horizontally) <i class="fa-solid fa-arrow-right-long ms-3"></i></small>
                        </div>
                    </div>
                </div>
            @endif
            @foreach($announcements as $announcement)
                <a href="{{route('employee.announcements.view', ['id' => $announcement->id])}}" class="text-decoration-none text-uppercase fw-bold">
                    <div class="card shadow">
                        <div class="card-body d-flex align-items-center">
                            <small class="card-title text-clamp clamp-3">{{$announcement->title}}</small>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>          
    </div>
    @if($announcements)
        <hr class="mt-3">
    @endif
    <div class="dashboard {{$announcements ? 'mt-5' : ''}}">
        <div class="grid">
            <a href="{{route('employee.leave')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/leave.png')}}" class="w-100">
                    <p>Leave Application</p>
                </div>
            </a>
            <a href="{{route('employee.clock')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/clockinout.png')}}" class="w-100">
                    <p>Clock In/Out</p>
                </div>
            </a>
            <a href="{{route('employee.atro')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/overtime.png')}}" class="w-100">
                    <p>Authority to Render Overtime</p>
                </div>
            </a>
            <a href="#" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/payslip.png')}}" class="w-100">
                    <p>Payslip</p>
                </div>
            </a>
            <a href="{{route('employee.request-status')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/request.png')}}" class="w-100">
                    <p>Request Status</p>
                </div>
            </a>
            <a href="{{route('employee.obs.index')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/business-slip.png')}}" class="w-100">
                    <p>Official Business Slip</p>
                </div>
            </a>
            <a href="{{route('employee.dtr')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/dtr.png')}}" class="w-100">
                    <p>Daily Time Record</p>
                </div>
            </a>
            <a href="{{route('employee.directory')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/directory.png')}}" class="w-100">
                    <p>My Directory</p>
                </div>
            </a>
            <a href="{{route('employee.team')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/team.png')}}" class="w-100">
                    <p>My Team</p>
                </div>
            </a>
            <a href="{{route('employee.announcements.index')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/announcement.png')}}" class="w-100">
                    <p>Announcements</p>
                </div>
            </a>
            <a href="{{route('employee.profile')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/profile.png')}}" class="w-100">
                    <p>My Profile</p>
                </div>
            </a>
            <a href="{{route('employee.tutorial')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/tutorial.png')}}" class="w-100">
                    <p>Tutorial</p>
                </div>
            </a>
            <a href="{{route('employee.logout')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/logout.png')}}" class="w-100">
                    <p>Logout</p>
                </div>
            </a>
        </div>
    </div>
</div>
