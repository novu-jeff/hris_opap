<div class="container">
    <div class="search-jobs">
        <div>
            <div class="content shadow {{$search_result && $search_result['is_empty_parameter'] ? 'error' : '' }}">
                <div class="search-box">
                    <input type="text" name="search" id="search" class="form-control" wire:model.prevent='search_query' placeholder="Job, Title, Keyword" value="{{$search_query ?? ''}}">
                </div>
                <div class="search-submit">
                    <button class="btn btn-primary px-4 py-2 text-uppercase fw-bold" wire:click='search'>Search 
                        <span class="ms-1">
                            <i class="fa-solid fa-magnifying-glass fa-shake"></i>
                        </span>
                    </button>
                </div>
            </div>
            @if ($search_result && $search_result['is_empty_parameter'])
            <div class="error-field" style="color: red; text-transform: uppercase; font-size: 11px; font-weight: 600; margin-top: 8px;">Try searching something...</div>
            @endif
        </div>
    </div>
    <div>
        @if ($search_result)
        <div class="searched-query text-muted">
            @if (!$search_result['is_empty_parameter'])
                <p class="m-0">You're searching for: <span>{{$search_result['parameter']}}</span></p>
                <p class="m-0">Returned <span>{{$search_result['count']}} result/s</span></p>
            @endif
        </div>
    @endif
    </div>
    <div class="jobs-lists" {{$search_query ?? "wire:poll='showRecords'"}}>
        <div class="row">
            @if ($records)
                <div class="col-12 col-md-12 col-lg-5 col-xl-5 mb-4">
                    <div class="row">
                        @foreach ($records as $record)
                            <div class="col-12 mb-4">
                                <div class="card shadow px-2 {{$record_info != null && $record_info->id === $record->id ? 'active' : ''}}" wire:click="show_more({{$record->id}})">
                                    <div class="card-header border-0 bg-transparent">
                                        <div class="position-title">
                                            <h4 class="m-0 text-uppercase">{{$record->position}}</h4>
                                        </div>
                                        <div class="company-info">
                                            <p class="m-0 text-uppercase">{{$record->company_name}}</p>
                                            <p class="m-0 text-uppercase">{{$record->location}}</p>
                                        </div>
                                        <div class="date-posted">
                                            <p class="m-0">
                                                Posted {{relative_time($record->created_at, 'hours ago')}}
                                            </p>
                                        </div>
                                        <div class="actions" wire:ignore>
                                            <div class="dropdown">
                                                <button class="btn btn-transparent btn-dropdown d-flex align-items-start justify-content-center" type="button" id="menu-{{$record->id}}" data-bs-toggle="dropdown" aria-expanded="true">
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2" wire:navigate href="{{route('home.view-job', ['slug' => $record->slug])}}">
                                                            <i class="fa-solid fa-eye"></i>
                                                            <span>
                                                                View Info 
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2 copy-link" href="javascript:void(0)" data-target="{{route('home.view-job', ['slug' => $record->slug])}}">
                                                            <i class="fa-solid fa-link"></i>
                                                            <span>
                                                                Copy Link 
                                                            </span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="mx-3">
                                    <div class="card-body pt-1 pb-5">
                                        <div class="perks">
                                            <div>{{money_format($record->min_salary) . ' - ' . money_format($record->max_salary)}} per month</div>
                                            <div>{{str_replace('-', ' ', $record->type)}}</div>
                                            <div>{{str_replace('-', ' ', $record->setup)}}</div>
                                        </div>
                                        <div class="description">
                                            <small class="text-muted fst-italic fw-bold text-uppercase text-decoration-underline" style="text-underline-offset: 4px">Description</small>
                                            <div class="description-content mt-2">
                                                {!!see_more(strip_tags($record->description), 400)!!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if (!$search_query)
                        <div x-intersect="$wire.showRecords()">
                            Load by scrolling
                        </div>
                    @else
                        <div class="d-flex justify-content-center mt-4">
                            <div>
                                <p class="mb-2 fst-italic">No more jobs found</p>
                                <a wire:navigate href="{{route('home.index')}}" class="btn btn-primary">Load All Jobs</a>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-12 col-md-12 col-lg-7 col-xl-7 mb-4" wire:poll='showAppliedJobs'>
                    <div class="job-info">
                        @if ($record_info)
                            <div class="card px-2" wire:click="show_more({{$record_info->id}})">
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
                                    <div class="actions d-flex gap-3 justify-content-start">
                                        @if (!in_array($record_info->id, $applied_job_ids))
                                            @if (!in_array($record_info->id, $saved_job_ids))
                                                <button class="btn btn-primary d-flex align-items-center gap-2" wire:click='save_job({{$record_info->id}})' href="javascript:void(0)">
                                                    <i class="fa-solid fa-thumbtack"></i>
                                                    <span>
                                                        Save Job
                                                    </span>
                                                </button>
                                            @else
                                                <button class="btn btn-primary d-flex align-items-center gap-2" wire:click='save_job({{$record_info->id}})' href="javascript:void(0)">
                                                    <i class="fa-solid fa-xmark"></i>
                                                    <span>
                                                        Unsave Job
                                                    </span>
                                                </button>
                                            @endif
                                            <button class="btn btn-outline-primary" wire:click='apply({{$record_info->id}})'>Apply Now</button>
                                        @else
                                            <button class="btn btn-primary">Applied Already</button>
                                        @endif
                                    </div>
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
            @elseif(!$records && !empty($search_query))
                <div class="alert alert-info text-uppercase text-center">No jobs found in this search.</div>
            @else
                <div class="alert alert-warning text-uppercase text-center">No jobs are currently posted. Please contact administrator `support@blitzdev.com.ph`.</div>
            @endif
        </div>
    </div>
</div>

@script
<script>
    
    $(function() {

        copy_link();

        $wire.on('navigateToSearch',function(event) {
            const data = JSON.parse(JSON.stringify(event))[0];
            if(event) {
                history.pushState(null, '', '/search/' + event); 
            } else {
                history.pushState(null, '', '/');
            }
            $wire.search(true)
        });
        
    })

</script>
@endscript