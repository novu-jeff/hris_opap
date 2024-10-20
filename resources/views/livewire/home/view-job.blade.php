<div class="container">
    <div class="search-jobs">
        <div class="content shadow">
            <div class="search-box">
                <input type="text" name="search" id="search" class="form-control" wire:model='search_query' placeholder="Job, Title, Keyword">
            </div>
            <div class="search-submit">
                <button class="btn btn-primary px-4 py-2 text-uppercase fw-bold" wire:click='search'>Search 
                    <span class="ms-1">
                        <i class="fa-solid fa-magnifying-glass fa-shake"></i>
                    </span>
                </button>
            </div>
        </div>
    </div>
    @if ($search_query)
        <div class="searched-query text-muted">
            <p class="m-0">You're searching for: <span>{{$search_query}}</span></p>
            @if ($search_result_count > 0)
                <p class="m-0">Returned <span>{{$search_result_count}} result/s</span></p>
            @else 
                <p class="m-0">No Result/s Found</span></p>
            @endif
        </div>
    @endif
    <div class="jobs-lists">
        <div class="row">
            @if ($record)
                <div class="col-12 mb-4">
                    <div class="job-info">
                        @if ($record)
                            <div class="card border-0 px-2">
                                <div class="card-header border-0 bg-transparent">
                                    <div class="position-title">
                                        <h4 class="m-0 text-uppercase">{{$record->position}}</h4>
                                    </div>
                                    <div class="company-info">
                                        <p class="m-0 text-uppercase">{{$record->company_name}}</p>
                                        <p class="m-0 text-uppercase">{{$record->location . ' • ' . str_replace('-', ' ', $record->setup) . ' • ' . str_replace('-', ' ', $record->type)}}</p>
                                    </div>
                                    <div class="salary">
                                        <p class="m-0 text-uppercase">{{money_format($record->min_salary) . ' - ' . money_format($record->max_salary)}} per month</p>
                                    </div>
                                    <div class="date-posted">
                                        <p class="m-0">
                                            Posted {{relative_time($record->created_at, 'hours ago')}}
                                        </p>
                                    </div>
                                    <div class="actions">
                                        @if (!in_array($record->id, $applied_jobs_id))
                                            <div class="d-flex gap-3">
                                                <a href="{{ 
                                                    (url()->previous() === url()->current() && !str_contains(url()->previous(), '/job')) 
                                                        ? url('/') 
                                                        : (url()->previous() === url()->current() ? url('/job/applicants/pending') : url()->previous())
                                                }}" class="btn btn-outline-primary d-flex align-items-center px-5 py-2 mb-0">
                                                    Go Back
                                                </a>
                                                
                                                <button class="btn btn-primary" wire:click='apply({{$record->id}})'>Apply Now</button>
                                            </div>
                                        @else
                                            @if ($record->status !== 'hired')
                                                <div class="d-flex gap-3">
                                                    <a href="{{ 
                                                        (url()->previous() === url()->current() && !str_contains(url()->previous(), '/job')) 
                                                            ? url('/') 
                                                            : (url()->previous() === url()->current() ? url('/job/applicants/pending') : url()->previous())
                                                    }}" class="btn btn-outline-primary d-flex align-items-center px-5 py-2 mb-0">
                                                        Go Back
                                                    </a>
                                                    
                                                    <button class="btn btn-primary">Applied Already</button>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <hr class="mx-3">
                                <div class="card-body pt-1 pb-5">
                                    <div class="description">
                                        <small class="text-muted fst-italic fw-bold text-uppercase text-decoration-underline" style="text-underline-offset: 4px">Description</small>
                                        <div class="description-content mt-2">
                                            {!!($record->description)!!}
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
            @else 
                <div class="alert alert-info text-uppercase text-center">No jobs found in this search.</div>
            @endif
        </div>
    </div>
</div>

@script
<script>
Livewire.on('alert', (event) => {
    const alert = JSON.parse(JSON.stringify(event))[0];
    if(alert.status == 'success') {
        Swal.fire({
            icon: "success",
            title: alert.title,
            html: alert.message,
        });  
    } else {
        Swal.fire({
            icon: "error",
            title: alert.title,
            html: alert.message,
        });  
    }
});
</script>
@endscript