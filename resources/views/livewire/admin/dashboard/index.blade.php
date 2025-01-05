<div class="dashboard">
    <div class="row mt-5">
        <div class="row">
            <div class="col-12 col-md-7">
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header bg-primary text-white px-4">
                            <h5 class="my-2 text-uppercase fw-bold">Employees</h5>
                        </div>
                        <div class="card-body px-3">
                            <div class="swiper-container">
                                <div class="swiper-wrapper d-flex">
                                    @forelse($stats['employee'] as $types)
                                        <div class="swiper-slide text-uppercase bg-info p-3 rounded-3 text-white">
                                            <p class="mb-0 fw-bold">{{$types['employment_type']}}</p>
                                            <hr>
                                            <h1>{{$types['employee_count']}}</h1>
                                        </div>
                                    @empty
                                        <div class="w-100 text-uppercase bg-info p-3 rounded-3 text-white">
                                            No employment types to show
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            @if(count($stats['employee']) > 3) 
                                <div class="float-end">
                                    <small class="text-uppercase text-muted fw-bold d-flex gap-2 align-items-center">
                                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                        Swipe left or right to view more
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header bg-primary text-white px-4 d-flex justify-content-between">
                            <h5 class="my-2 text-uppercase fw-bold">Clock In & Out</h5>
                            <h5 class="my-2 text-uppercase fw-bold">{{ \Carbon\Carbon::now()->format('F d, Y') }}</h5>
                        </div>
                        <div class="card-body px-3 d-flex">
                            <div class="d-flex gap-3 w-100">
                                <div class="w-100 text-uppercase bg-info p-3 rounded-3 text-white">
                                    <p class="mb-0 fw-bold">Clocked In</p>
                                    <hr>
                                    <h1>{{$stats['clockinout']['clockin']}}</h1>
                                </div>
                                <div class="w-100 text-uppercase bg-info p-3 rounded-3 text-white">
                                    <p class="mb-0 fw-bold">In Progress</p>
                                    <hr>
                                    <h1>{{$stats['clockinout']['inprogress']}}</h1>
                                </div>
                                <div class="w-100 text-uppercase bg-info p-3 rounded-3 text-white">
                                    <p class="mb-0 fw-bold">Clocked Out</p>
                                    <hr>
                                    <h1>{{$stats['clockinout']['clockout']}}</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header bg-primary text-white px-4">
                            <h5 class="my-2 text-uppercase fw-bold">Leave Applications</h5>
                        </div>
                        <div class="card-body px-3 d-flex">
                            <div class="d-flex gap-3 w-100">
                                <div class="w-100 text-uppercase bg-secondary p-3 rounded-3 text-white">
                                    <p class="mb-0 fw-bold">Pending</p>
                                    <hr>
                                    <h1>{{$stats['leave']['pending']}}</h1>
                                    <div class="float-end">
                                        <a href="{{route('ess.leave', ['status' => 'pending'])}}" class="text-white">View</a>
                                    </div>
                                </div>
                                <div class="w-100 text-uppercase bg-success p-3 rounded-3 text-white">
                                    <p class="mb-0 fw-bold">Granted</p>
                                    <hr>
                                    <h1>{{$stats['leave']['granted']}}</h1>
                                    <div class="float-end">
                                        <a href="{{route('ess.leave', ['status' => 'granted'])}}" class="text-white">View</a>
                                    </div>
                                </div>
                                <div class="w-100 text-uppercase bg-danger p-3 rounded-3 text-white">
                                    <p class="mb-0 fw-bold">Rejected</p>
                                    <hr>
                                    <h1>{{$stats['leave']['rejected']}}</h1>
                                    <div class="float-end">
                                        <a href="{{route('ess.leave', ['status' => 'rejected'])}}" class="text-white">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header bg-primary text-white px-4">
                            <h5 class="my-2 text-uppercase fw-bold">OBS Applications</h5>
                        </div>
                        <div class="card-body px-3 d-flex">
                            <div class="d-flex gap-3 w-100">
                                <div class="w-100 text-uppercase bg-secondary p-3 rounded-3 text-white">
                                    <p class="mb-0 fw-bold">Pending</p>
                                    <hr>
                                    <h1>{{$stats['obs']['pending']}}</h1>
                                    <div class="float-end">
                                        <a href="{{route('ess.obs', ['status' => 'pending'])}}" class="text-white">View</a>
                                    </div>
                                </div>
                                <div class="w-100 text-uppercase bg-success p-3 rounded-3 text-white">
                                    <p class="mb-0 fw-bold">Granted</p>
                                    <hr>
                                    <h1>{{$stats['obs']['granted']}}</h1>
                                    <div class="float-end">
                                        <a href="{{route('ess.obs', ['status' => 'granted'])}}" class="text-white">View</a>
                                    </div>
                                </div>
                                <div class="w-100 text-uppercase bg-danger p-3 rounded-3 text-white">
                                    <p class="mb-0 fw-bold">Rejected</p>
                                    <hr>
                                    <h1>{{$stats['obs']['rejected']}}</h1>
                                    <div class="float-end">
                                        <a href="{{route('ess.obs', ['status' => 'rejected'])}}" class="text-white">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header bg-primary text-white px-4">
                            <h5 class="my-2 text-uppercase fw-bold">ATRO Applications</h5>
                        </div>
                        <div class="card-body px-3 d-flex">
                            <div class="d-flex gap-3 w-100">
                                <div class="w-100 text-uppercase bg-secondary p-3 rounded-3 text-white">
                                    <p class="mb-0 fw-bold">Pending</p>
                                    <hr>
                                    <h1>{{$stats['atro']['pending']}}</h1>
                                    <div class="float-end">
                                        <a href="{{route('ess.atro', ['status' => 'pending'])}}" class="text-white">View</a>
                                    </div>
                                </div>
                                <div class="w-100 text-uppercase bg-success p-3 rounded-3 text-white">
                                    <p class="mb-0 fw-bold">Granted</p>
                                    <hr>
                                    <h1>{{$stats['atro']['granted']}}</h1>
                                    <div class="float-end">
                                        <a href="{{route('ess.atro', ['status' => 'granted'])}}" class="text-white">View</a>
                                    </div>
                                </div>
                                <div class="w-100 text-uppercase bg-danger p-3 rounded-3 text-white">
                                    <p class="mb-0 fw-bold">Rejected</p>
                                    <hr>
                                    <h1>{{$stats['atro']['rejected']}}</h1>
                                    <div class="float-end">
                                        <a href="{{route('ess.atro', ['status' => 'rejected'])}}" class="text-white">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-5">
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white px-4">
                                <h5 class="my-2 text-uppercase fw-bold">Recruitment</h5>
                            </div>
                            <div class="card-body px-4">
                                <div class="row">
                                    <div class="col-12 mb-3 col-md-6">
                                        <a href="{{route('job.applicants.index', ['status' => 'pending'])}}" class="nav-link">
                                            <div class="mb-0 alert alert-info w-100 text-uppercase fw-bold">Pending: {{$stats['recruitment']['pending']}}</div>
                                        </a>
                                    </div>
                                    <div class="col-12 mb-3 col-md-6">
                                        <a href="{{route('job.applicants.index', ['status' => 'interview'])}}" class="nav-link">
                                            <div class="mb-0 alert alert-warning w-100 text-uppercase fw-bold">Interview: {{$stats['recruitment']['interview']}}</div>
                                        </a>
                                    </div>
                                    <div class="col-12 mb-3 col-md-6">
                                        <a href="{{route('job.applicants.index', ['status' => 'placement'])}}" class="nav-link">
                                            <div class="mb-0 alert alert-secondary w-100 text-uppercase fw-bold">Placement: {{$stats['recruitment']['placement']}}</div>
                                        </a>
                                    </div>
                                    <div class="col-12 mb-3 col-md-6">
                                        <a href="{{route('job.applicants.index', ['status' => 'onboarding'])}}" class="nav-link">
                                            <div class="mb-0 alert alert-primary w-100 text-uppercase fw-bold">Onboarding: {{$stats['recruitment']['onboarding']}}</div>
                                        </a>
                                    </div>
                                    <div class="col-12 mb-3 col-md-6">
                                        <a href="{{route('job.applicants.index', ['status' => 'hired'])}}" class="nav-link">
                                            <div class="mb-0 alert alert-success w-100 text-uppercase fw-bold">Hired: {{$stats['recruitment']['hired']}}</div>
                                        </a>
                                    </div>
                                    <div class="col-12 mb-3 col-md-6">
                                        <a href="{{route('job.applicants.index', ['status' => 'rejected'])}}" class="nav-link">
                                            <div class="mb-0 alert alert-danger w-100 text-uppercase fw-bold">Rejected: {{$stats['recruitment']['rejected']}}</div>
                                        </a>
                                    </div>
                                </div>
                                <div class="float-end">
                                    <small class="text-uppercase text-muted fw-bold d-flex gap-2 align-items-center">
                                        <i class="fa-regular fa-hand-pointer"></i>
                                        Click to view more
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white px-4 d-flex justify-content-between">
                                @if(!empty($stats['gsis_billing']['billing_month']))
                                    <h5 class="my-2 text-uppercase fw-bold">
                                        LATEST GSIS BILLING 
                                    </h5>
                                    <h5 class="my-2 text-uppercase fw-bold">
                                        ({{ $stats['gsis_billing']['billing_month'] }})
                                    </h5>
                                @else
                                    <h5 class="my-2 text-uppercase fw-bold">
                                        LATEST GSIS BILLING
                                    </h5>
                                @endif
                            </div>
                            <div class="card-body">
                                @if(!empty($stats['gsis_billing']['items']) && count($stats['gsis_billing']['items']) > 0)
                                    <table class="table text-uppercase fw-bold w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th>BP No</th>
                                                <th>CRN No</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stats['gsis_billing']['items'] as $billing)
                                                <tr>
                                                    <td>{{ $billing['bp_no'] ?? 'N/A' }}</td>
                                                    <td>{{ $billing['crn_no'] ?? 'N/A' }}</td> 
                                                    <td>₱{{ number_format($billing['ps'], 2) ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <small class="text-uppercase text-muted">No GSIS Billing Found.</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white px-4">
                                <h5 class="my-2 text-uppercase fw-bold">Other Earnings</h5>
                            </div>
                            <div class="card-body px-4 d-flex">
                                @if(count($stats['earnings']) > 0)
                                    <ul class="text-uppercase fw-bold list-unstyled">
                                        @foreach($stats['earnings'] as $earnings)
                                            <li style="font-size: 12px">{{$earnings['name']}} <i class="fa fa-check text-primary fs-6 ms-1" aria-hidden="true"></i></li>
                                        @endforeach
                                    </ul>
                                @else
                                    <small class="text-uppercase text-muted">No Earnings Found.</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white px-4">
                                <h5 class="my-2 text-uppercase fw-bold">Other Deductions</h5>
                            </div>
                            <div class="card-body px-4 d-flex">
                                @if(count($stats['deductions']) > 0)
                                    <ul class="text-uppercase fw-bold list-unstyled">
                                        @foreach($stats['deductions'] as $deductions)
                                            <li style="font-size: 12px">{{$deductions['name']}} <i class="fa fa-check text-primary fs-6 ms-1" aria-hidden="true"></i></li>
                                        @endforeach
                                    </ul>
                                @else
                                    <small class="text-uppercase text-muted">No Earnings Found.</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>