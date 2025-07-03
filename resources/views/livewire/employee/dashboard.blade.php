<div>
    <div class="company-information mt-5 text-uppercase">
        <h2 class="fw-bold">{{$companyInfo->name}}</h2>
        <h5 class="fw-medium">{{$companyInfo->address}}</h5>
        <h5 class="fw-medium">{{$companyInfo->type->name . ' • ' . $companyInfo->contact}}</h5>
    </div>
    <hr class="mt-4 mb-4">
    <div class="d-lg-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Dashboard</h1>
            <p>Track and monitor your employment records.</p>
        </div>
    </div>
    <div class="latest-announcements">
        <div class="wrapper d-flex gap-4">
            @forelse($announcements as $announcement)
                <a href="{{route('employee.announcements.view', ['id' => $announcement->id])}}" class="text-decoration-none text-uppercase fw-bold">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <small class="card-title text-clamp clamp-3">{{$announcement->title}}</small>
                        </div>
                    </div>
                </a>
            @empty
                <div class="alert alert-primary w-100 d-flex align-items-center justify-content-center text-uppercase fw-medium">
                    No Announcements Yet
                </div>
            @endforelse
        </div>          
    </div>
    <div class="applications mt-5">
        <div class="row">
            @foreach ($applications as $application)
                <div class="col-12 col-md-3">
                    <a href="{{route($application['route'])}}" class="text-decoration-none">
                        <div class="card mb-4 shadow" style="cursor: pointer">
                            <div class="card-body d-flex align-items-center p-4">
                                <div>
                                    <h1 class="fw-bold">{{$application['count']}}</h1>
                                    <h6 class="card-title text-uppercase fw-bold nowrap mt-3">{{$application['title']}}</h6>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
        @endforeach
        </div>
    </div>
    @if($announcements)
        <hr class="mt-3">
    @endif
    <div class="dashboard {{$announcements ? 'mt-5' : ''}}">
        <div class="row">
            @canany(['read apply-leave', 'write apply-leave'])
                @if($isForRCOnly)
                    <div class="col-12 col-md-6 col-xl-4 mb-4">
                        <a href="{{ route('employee.leave') }}" class="nav-link">
                            <div class="item">
                                <img src="{{ asset('/img/leave.png') }}" class="w-100">
                                <p>Leave Application</p>
                            </div>
                        </a>
                    </div>
                @endif
            @endcanany
        
            @canany(['read clock-in-out', 'write clock-in-out'])
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.clock') }}" class="nav-link">
                <div class="item">
                    <img src="{{ asset('/img/clockinout.png') }}" class="w-100">
                    <p>Clock In/Out</p>
                </div>
                </a>
            </div>
            @endcanany

            @canany(['read remaining-credit', 'write remaining-credit'])
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.credit') }}" class="nav-link">
                <div class="item">
                    <img src="{{ asset('/img/remaining-credit.png') }}" class="w-100">
                    <p>Leave Credits</p>
                </div>
                </a>
            </div>
            @endcanany
        
            @canany(['read apply-atro', 'write apply-atro'])
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.atro') }}" class="nav-link">
                <div class="item">
                    <img src="{{ asset('/img/overtime.png') }}" class="w-100">
                    <p>Authority to Render Overtime</p>
                </div>
                </a>
            </div>
            @endcanany

            @canany(['read apply-request-timelog', 'write apply-request-timelog'])
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.request-timelog') }}" class="nav-link">
                <div class="item">
                    <img src="{{ asset('/img/request-log.png') }}" class="w-100">
                    <p>Request Timelog</p>
                </div>
                </a>
            </div>
            @endcanany
        
            @canany(['read payslip', 'write payslip'])
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.payslip') }}" class="nav-link">
                <div class="item">
                    <img src="{{ asset('/img/payslip.png') }}" class="w-100">
                    <p>Payslip</p>
                </div>
                </a>
            </div>
            @endcanany
        
            @canany(['read employee-messages', 'write employee-messages'])
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.messages') }}" class="nav-link">
                <div class="item">
                    <img src="{{ asset('/img/message.png') }}" class="w-100">
                    <p>Contact Us</p>
                </div>
                </a>
            </div>
            @endcanany
        
            @canany(['read apply-obs', 'write apply-obs'])
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.obs.index') }}" class="nav-link">
                <div class="item">
                    <img src="{{ asset('/img/business-slip.png') }}" class="w-100">
                    <p>Official Business Application</p>
                </div>
                </a>
            </div>
            @endcanany
        
            @canany(['read employee-dtr', 'write employee-dtr'])
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.dtr') }}" class="nav-link">
                <div class="item">
                    <img src="{{ asset('/img/dtr.png') }}" class="w-100">
                    <p>Daily Time Record</p>
                </div>
                </a>
            </div>
            @endcanany
        
            @canany(['read my-directory', 'write my-directory'])
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.directory') }}" class="nav-link">
                <div class="item">
                    <img src="{{ asset('/img/directory.png') }}" class="w-100">
                    <p>My Directory</p>
                </div>
                </a>
            </div>
            @endcanany
        
            @canany(['read my-team', 'write my-team'])
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.team') }}" class="nav-link">
                <div class="item">
                    <img src="{{ asset('/img/team.png') }}" class="w-100">
                    <p>My Team</p>
                </div>
                </a>
            </div>
            @endcanany
        
            @canany(['read employee-announcements', 'write employee-announcements'])
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.announcements.index') }}" class="nav-link">
                <div class="item">
                    <img src="{{ asset('/img/announcement.png') }}" class="w-100">
                    <p>Announcements</p>
                </div>
                </a>
            </div>
            @endcanany
        
            @canany(['read my-profile', 'write my-profile'])
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.profile') }}" class="nav-link">
                <div class="item">
                    <img src="{{ asset('/img/profile.png') }}" class="w-100">
                    <p>My Profile</p>
                </div>
                </a>
            </div>
            @endcanany
        
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.tutorial') }}" class="nav-link">
                    <div class="item">
                    <img src="{{ asset('/img/tutorial.png') }}" class="w-100">
                    <p>Tutorial</p>
                    </div>
                </a>
            </div>
        
            <div class="col-12 col-md-6 col-xl-4 mb-4">
                <a href="{{ route('employee.logout') }}" class="nav-link">
                    <div class="item">
                    <img src="{{ asset('/img/logout.png') }}" class="w-100">
                    <p>Logout</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
