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
        @php
            $menuItems = [
                [
                    'label' => 'Announcements',
                    'route' => route('employee.announcements.index'),
                    'img' => '/img/announcement.png',
                    'permission' => ['read employee-announcements', 'write employee-announcements'],
                ],
                [
                    'label' => 'Authority to Render Overtime',
                    'route' => route('employee.atro'),
                    'img' => '/img/overtime.png',
                    'permission' => ['read apply-atro', 'write apply-atro'],
                ],
                [
                    'label' => 'Clock In/Out',
                    'route' => route('employee.clock'),
                    'img' => '/img/clockinout.png',
                    'permission' => ['read clock-in-out', 'write clock-in-out'],
                ],
                [
                    'label' => 'Contact Us',
                    'route' => route('employee.messages'),
                    'img' => '/img/message.png',
                    'permission' => ['read employee-messages', 'write employee-messages'],
                ],
                [
                    'label' => 'Daily Time Record',
                    'route' => route('employee.dtr'),
                    'img' => '/img/dtr.png',
                    'permission' => ['read employee-dtr', 'write employee-dtr'],
                ],
                [
                    'label' => 'Leave Application',
                    'route' => route('employee.leave'),
                    'img' => '/img/leave.png',
                    'permission' => ['read apply-leave', 'write apply-leave'],
                    'condition' => $isForRCOnly
                ],
                [
                    'label' => 'Leave Credits',
                    'route' => route('employee.credit'),
                    'img' => '/img/remaining-credit.png',
                    'permission' => ['read remaining-credit', 'write remaining-credit'],
                ],
                [
                    'label' => 'My Directory',
                    'route' => route('employee.directory'),
                    'img' => '/img/directory.png',
                    'permission' => ['read my-directory', 'write my-directory'],
                ],
                [
                    'label' => 'My Profile',
                    'route' => route('employee.profile'),
                    'img' => '/img/profile.png',
                    'permission' => ['read my-profile', 'write my-profile'],
                ],
                [
                    'label' => 'My Team',
                    'route' => route('employee.team'),
                    'img' => '/img/team.png',
                    'permission' => ['read my-team', 'write my-team'],
                ],
                [
                    'label' => 'Official Business Application',
                    'route' => route('employee.obs.index'),
                    'img' => '/img/business-slip.png',
                    'permission' => ['read apply-obs', 'write apply-obs'],
                ],
                [
                    'label' => 'Payslip',
                    'route' => route('employee.payslip'),
                    'img' => '/img/payslip.png',
                    'permission' => ['read payslip', 'write payslip'],
                ],
                [
                    'label' => 'Time Adjustments',
                    'route' => route('employee.time-adjustments'),
                    'img' => '/img/time-adjustments.png',
                    'permission' => ['read apply-time-adjustments', 'write apply-time-adjustments'],
                ],
                [
                    'label' => 'Tutorial',
                    'route' => route('employee.tutorial'),
                    'img' => '/img/tutorial.png',
                    'permission' => null,
                ],
                [
                    'label' => 'Logout',
                    'route' => route('employee.logout'),
                    'img' => '/img/logout.png',
                    'permission' => null,
                ],
            ];

            // Sort alphabetically by label
            usort($menuItems, fn($a, $b) => strcmp($a['label'], $b['label']));
        @endphp
        <div class="row">
            @foreach ($menuItems as $item)
                @php
                    $canView = true;
                    if (isset($item['permission'])) {
                        $canView = auth()->user()->can($item['permission'][0]) || auth()->user()->can($item['permission'][1]);
                    }
                    if (isset($item['condition'])) {
                        $canView = $canView && $item['condition'];
                    }
                @endphp

                @if ($canView)
                    <div class="col-12 col-md-6 col-xl-4 mb-4">
                        <a href="{{ $item['route'] }}" class="nav-link" data-bs-toggle="tooltip" title="{{ $item['label'] }}">
                            <div class="item">
                                <img src="{{ asset($item['img']) }}" class="w-100">
                                <p>{{ $item['label'] }}</p>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
