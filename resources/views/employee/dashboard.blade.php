@extends('layouts.employee', [
    'title' => 'ESS | Dashboard'
])

@section('content')
<div class="container pb-5">
    <div class="mt-5 d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Dashboard</h1>
            <p>Track and monitor your employment records.</p>
        </div>
    </div>
    <div class="dashboard mt-3">
        <div class="grid">
            <a href="{{route('employee.leave')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/leave.png')}}" alt="leave" class="w-100">
                    <p>Leave Application</p>
                </div>
            </a>
            <a href="{{route('employee.clock')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/clockinout.png')}}" alt="leave" class="w-100">
                    <p>Clock In/Out</p>
                </div>
            </a>
            <a href="{{route('employee.atro')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/overtime.png')}}" alt="leave" class="w-100">
                    <p>Authority to Render Overtime</p>
                </div>
            </a>
            <a href="#" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/payslip.png')}}" alt="leave" class="w-100">
                    <p>Payslip</p>
                </div>
            </a>
            {{-- <a href="{{route('employee.request-status')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/request.png')}}" alt="leave" class="w-100">
                    <p>Request Status</p>
                </div>
            </a> --}}
            <a href="{{route('employee.obs.index')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/business-slip.png')}}" alt="leave" class="w-100">
                    <p>Official Business Slip</p>
                </div>
            </a>
            <a href="{{route('employee.directory')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/directory.png')}}" alt="leave" class="w-100">
                    <p>My Directory</p>
                </div>
            </a>
            <a href="{{route('employee.team')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/team.png')}}" alt="leave" class="w-100">
                    <p>My Team</p>
                </div>
            </a>
            <a href="{{route('employee.announcements.index')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/announcement.png')}}" alt="leave" class="w-100">
                    <p>Announcements</p>
                </div>
            </a>
            <a href="{{route('employee.logout')}}" class="nav-link">
                <div class="item">
                    <img src="{{asset('/img/logout.png')}}" alt="leave" class="w-100">
                    <p>Logout</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection