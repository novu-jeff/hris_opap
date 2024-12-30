<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Saved Jobs</h1>
            <p>Below are the list of jobs you have saved before.</p>
        </div>
    </div>
    <div class="jobs-lists">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a href="{{route('home.applied')}}" class="nav-link text-center pt-3" id="nav-applied-tab">Applied Jobs</a>
                <a href="{{route('home.saved')}}" class="nav-link text-center pt-3 active" id="nav-saved-tab">Saved Jobs</a>
                <a href="{{route('home.saved')}}" class="nav-link text-center pt-3" id="nav-inactive-tab">Inactive Jobs</a>
            </div>
          </nav>
          <div class="row">
            <div class="col-12">
                <div class="total-applied my-4">
                    <div>
                        <h5 class="m-0">{{!is_null($records) ? $records->count() : '0'}} Saved Job/s Found</h5>
                        <div class="d-flex align-items-center gap-2 mt-5">
                            <label for="entries" class="form-label mb-0">Show entries:</label>
                            <select id="entries" wire:model.change="entries" class="form-select w-auto mt-0">
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
                    </div>
                </div>                                
            </div>
            <div wire:loading>
                Loading <i class="fa-solid fa-spinner fa-spin"></i>
            </div>
            <div wire:loading.remove>
                <div class="col-12 mb-3">
                    @forelse ($records as $record)
                        <div class="col-12 mb-4">
                            <div class="card shadow px-2" style="border-left: 8px solid #225F8B; cursor: pointer">
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
                                                <a href="{{route('home.view-job', ['slug' => $record->job->slug])}}" class="btn btn-outline-primary px-5 py-3 text-uppercase fw-bold">View Job</a>
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
                        <div class="alert alert-info text-uppercase text-center">You have no saved jobs here.</div>
                    @endforelse
                    <div class="mt-4">
                        {{ $records->links(data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>