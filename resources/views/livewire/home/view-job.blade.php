<div class="container">
    <div class="search-jobs">
        <div>
            <div class="content shadow {{$isEmptySearch ? 'error'  : '' }}">
                <div class="search-box">
                    <input type="text" name="search" id="search" class="form-control" wire:model.defer='search_query' placeholder="Job, Title, Keyword" value="{{$search_query ?? ''}}">
                </div>
                <div class="search-submit">
                    <button class="btn btn-primary px-4 py-2 text-uppercase fw-bold" wire:click='find'>Search 
                        <span class="ms-1">
                            <i class="fa-solid fa-magnifying-glass fa-shake"></i>
                        </span>
                    </button>
                </div>
            </div>
            @if ($isEmptySearch)
                <div class="error-field" style="color: red; text-transform: uppercase; font-size: 11px; font-weight: 600; margin-top: 8px;">Try searching something...</div>
            @endif
        </div>
    </div>
    <div>
        @if ($search_result && $search_term && !$isEmptySearch)
            <div class="searched-query text-muted mt-5" wire:ignore>
                <p class="m-0">You're searching for: <span>{{$search_result['parameter']}}</span></p>
                <p class="m-0">Returned <span>{{$search_result['total']}} result/s</span></p>
            </div>
            <hr class="mt-4">
        @endif
    </div>
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
    <div class="d-flex justify-content-between">
        @if(!is_null($nextAndPrev['prev']))
            <a wire:navigate href="{{$nextAndPrev['prev']}}" class="text-uppercase fw-bold text-primary">
                <i class="fa-solid fa-arrow-left-long me-2"></i> Previous
            </a>
        @endif
        @if(!is_null($nextAndPrev['next']))
            <a wire:navigate href="{{$nextAndPrev['next']}}" class="text-uppercase fw-bold text-primary">
                Next <i class="fa-solid fa-arrow-right-long ms-2"></i>
            </a>
        @endif
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