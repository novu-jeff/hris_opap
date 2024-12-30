<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Jobs Applied</h1>
            <p>Below are the list of jobs you have applied</p>
        </div>
    </div>
    <div class="jobs-lists">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a href="{{route('home.applied')}}" class="nav-link text-center pt-3 active" id="nav-applied-tab">Applied Jobs</a>
                <a href="{{route('home.saved')}}" class="nav-link text-center pt-3" id="nav-saved-tab">Saved Jobs</a>
                <a href="{{route('home.saved')}}" class="nav-link text-center pt-3" id="nav-inactive-tab">Inactive Jobs</a>
            </div>
          </nav>
          <div class="row">
            <div class="col-12 col-md-12 col-lg-5 col-xl-5 mb-4">
                <div class="row">
                    <div class="col-12">
                        <div class="total-applied my-4">
                            <div>
                                <h5 class="m-0">{{!is_null($records) ? $records->total() : '0'}} Job/s Found</h5>
                                <div class="d-flex align-items-center gap-2 mt-5">
                                    <label for="entries" class="form-label mb-0">Show entries:</label>
                                    <select id="entries" wire:model.change="entries" class="form-select w-25 mt-0">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="30">30</option>
                                        <option value="40">40</option>
                                        <option value="50">50</option>
                                        <option value="60">60</option>
                                        <option value="70">70</option>
                                        <option value="80">80</option>
                                        <option value="90">90</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                                <select name="sort" id="sort" wire:model.change="sort_status" class="form-select">
                                    <option value="">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="reviewed">Application Reviewed</option>
                                    <option value="interview">Interview</option>
                                    <option value="placement">Placement</option>
                                    <option value="onboarding">On Boarding</option>
                                    <option value="hired">Hired</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>                                
                    </div>
                    <div wire:loading>
                        Loading <i class="fa-solid fa-spinner fa-spin"></i>
                    </div>
                    <div wire:loading.remove>
                        @forelse ($records as $record)
                            <div class="col-12 mb-4">
                                <div class="card shadow px-2 {{$record_info != null && $record_info->id === $record->job_id ? 'active' : ''}}" wire:click="show_more({{$record->job_id}})" style="border-left: 8px solid #225F8B; cursor: pointer">
                                    <div class="card-body px-4 py-4">
                                        <div class="w-100 d-flex justify-content-between align-items-start gap-5">
                                            <div class="w-100">
                                                <div class="date-posted float-end">
                                                    <p class="m-0">
                                                        Applied {{relative_time($record->created_at, 'hours ago')}}
                                                    </p>
                                                </div>
                                                <div class="info">
                                                    <div class="position-title">
                                                        <h4 class="m-0 text-uppercase">{{$record->job->position}}</h4>
                                                    </div>
                                                    <div class="company-info">
                                                        <p class="m-0 text-uppercase">{{$record->job->company_name}}</p>
                                                        <p class="m-0 text-uppercase">{{$record->job->location . ' • ' . str_replace('-', ' ', $record->job->setup . ' • ' . str_replace('-', ' ', $record->job->type))}}</p>
                                                        <p class="m-0 text-uppercase">{{money_format($record->job->min_salary) . ' - ' . money_format($record->job->max_salary)}}</p>
                                                    </div>
                                                    <hr>
                                                </div>
                                                <div class="status">
                                                    <p class="m-0">
                                                        {!!application_status($record->status)!!} 
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="alert alert-info text-uppercase text-center">You have no applications applied yet</div>
                                </div>
                                <div class="col-12 mb-3">
                                    <a wire:navigate href="{{route('home.index')}}" class="text-decoration-none">
                                        <div class="card create">
                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                <div class="text-center">
                                                    <div class="icon text-center">
                                                        <i class="fa-solid fa-plus"></i>
                                                    </div>
                                                    <div class="label">
                                                        <div>
                                                            Start Applying Now!
                                                        </div>
                                                        <div>
                                                            Explore and apply to your favorite jobs effortlessly, without the need for in-person applications.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforelse
                        <div class="mt-4">
                            {{ $records->links(data: ['scrollTo' => false]) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-7 col-xl-7 mb-4">
                <div class="job-info" style="margin-top: 80px">
                    @if ($record_info)
                        <div class="card px-2">
                            <div class="card-header border-0 bg-transparent">
                                <div class="position-title">
                                    <h4 class="m-0 text-uppercase">{{$record_info->position}}</h4>
                                </div>
                                <div class="company-info">
                                    <p class="m-0 text-uppercase">{{$record_info->company_name}}</p>
                                    <p class="m-0 text-uppercase">{{$record_info->location . ' • ' . str_replace('-', ' ', $record_info->setup) . ' • ' . str_replace('-', ' ', $record_info->type)}}</p>
                                </div>
                                <div class="salary">
                                    <p class="m-0 text-uppercase">{{money_format($record_info->min_salary) . ' - ' . money_format($record_info->max_salary)}} per month</p>
                                </div>
                                <div class="date-posted">
                                    <p class="m-0">
                                        Posted {{relative_time($record_info->created_at, 'hours ago')}}
                                    </p>
                                </div>
                                @if ($record->status != 'hired')
                                    <div class="actions">
                                        <button class="btn btn-outline-primary d-block my-2 px-5 py-3" wire:click='withdraw({{$record_info->id}})'>Withdraw Application</button>
                                    </div>
                                @else
                                    <div class="actions">
                                        <a class="w-50 btn btn-outline-primary d-block my-2 px-5 py-3 text-uppercase fw-bold" href="{{route('home.view-job', ['slug' => $record_info->slug])}}">View Job</a>
                                    </div>
                                @endif
                            </div>
                            <hr class="mx-3">
                            <div class="card-body pt-1 pb-5">
                                <div class="description">
                                    <small class="text-muted fst-italic fw-bold text-uppercase text-decoration-underline" style="text-underline-offset: 4px">Description</small>
                                    <div class="description-content mt-2">
                                        {!!see_more($record_info->description)!!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="choose-first px-5">
                            <div class="d-flex gap-3">
                                <div>
                                    <i class="fa-solid fa-arrow-left-long"></i>
                                </div>
                                <div>
                                    <h4>Choose a job first</h4>
                                    <p>Displays all informations here</p>
                                </div>
                            </div>
                            <div class="banner">
                                <img src="{{asset('img/choice.svg')}}" alt="banner">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>