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
              <button class="nav-link active" id="nav-applied-tab" data-bs-toggle="tab" data-bs-target="#nav-applied" type="button" role="tab" aria-controls="nav-applied" aria-selected="true" wire:ignore wire:click='showAppliedJobs'>Applied Jobs</button>
              <button class="nav-link" id="nav-saved-tab" data-bs-toggle="tab" data-bs-target="#nav-saved" type="button" role="tab" aria-controls="nav-saved" aria-selected="false" wire:click='showSavedJobs' wire:ignore>Saved Jobs</button>
              <button class="nav-link" id="nav-inactive-tab" data-bs-toggle="tab" data-bs-target="#nav-inactive" type="button" role="tab" aria-controls="nav-inactive" aria-selected="false" wire:ignore>Inactive Jobs</button>
            </div>
          </nav>
          <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-applied" role="tabpanel" aria-labelledby="nav-applied-tab" tabindex="0" wire:ignore.self>
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-5 col-xl-5 mb-4">
                        <div class="row">
                            <div class="col-12">
                                <div class="total-applied my-4">
                                    <div>
                                        <h5 class="m-0">{{!is_null($records) ? $records->count() : '0'}} Job/s Found</h5>
                                        <select name="sort" id="sort" wire:model="sort_status" wire:change='showRecords' class="form-select">
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
                            @if ($records)
                                @foreach ($records as $record)
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
                                @endforeach
                            @else
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
                            @endif
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
                                        @if ($record->status !== 'hired')
                                            <div class="actions">
                                                <button class="btn btn-outline-primary d-block my-2 px-5 py-3" wire:click='withdraw({{$record->id}})'>Withdraw Application</button>
                                            </div>
                                        @else
                                            <div class="actions">
                                                <a class="w-50 btn btn-outline-primary d-block my-2 px-5 py-3" href="{{route('home.view-job', ['slug' => $record_info->slug])}}">View Job</a>
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
            <div class="tab-pane fade" id="nav-saved" role="tabpanel" aria-labelledby="nav-saved-tab" tabindex="0" wire:ignore.self>
                <div class="row">
                    <div class="col-12">
                        <div class="total-applied my-4">
                            <div>
                                <h5 class="m-0">{{!is_null($saved_jobs) ? $saved_jobs->count() : '0'}} Saved Job/s Found</h5>
                            </div>
                        </div>                                
                    </div>
                    <div class="col-12 mb-3" wire:poll='showSavedJobs'>
                        @if ($saved_jobs)
                            @foreach ($saved_jobs as $saved_job)
                                    <div class="col-12 mb-4">
                                        <div class="card shadow px-2" style="border-left: 8px solid #225F8B; cursor: pointer">
                                            <div class="card-body px-4 py-4">
                                                <div class="w-100 d-flex justify-content-between align-items-start gap-5">
                                                    <div class="w-100">
                                                        <div class="date-posted float-end">
                                                            <p class="m-0">
                                                                Applied {{relative_time($saved_job->created_at, 'hours ago')}}
                                                            </p>
                                                        </div>
                                                        <div class="info">
                                                            <div class="position-title">
                                                                <h4 class="m-0 text-uppercase">{{$saved_job->job->position}}</h4>
                                                            </div>
                                                            <div class="company-info">
                                                                <p class="m-0 text-uppercase">{{$saved_job->job->company_name}}</p>
                                                                <p class="m-0 text-uppercase">{{$saved_job->job->location . ' • ' . str_replace('-', ' ', $saved_job->job->setup . ' • ' . str_replace('-', ' ', $saved_job->job->type))}}</p>
                                                                <p class="m-0 text-uppercase">{{money_format($saved_job->job->min_salary) . ' - ' . money_format($saved_job->job->max_salary)}}</p>
                                                            </div>
                                                            <hr>
                                                            <a href="{{route('home.view-job', ['slug' => $saved_job->job->slug])}}" class="btn btn-outline-primary px-5 py-3">View Job</a>
                                                        </div>
                                                        <div class="status">
                                                            <p class="m-0">
                                                                {!!application_status($saved_job->status)!!} 
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            @endforeach
                        @else
                            <div class="alert alert-info text-uppercase text-center">You have no saved jobs here.</div>
                        @endif
                    </div>
                </div> 
            </div>
          </div>
    </div>
</div>