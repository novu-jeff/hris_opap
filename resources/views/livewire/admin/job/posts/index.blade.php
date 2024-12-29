<div class="job">
    <div class="row mb-4">
        <div class="col-md-6 d-flex align-items-center gap-2">
            <label for="entries" class="form-label mb-0">Show entries:</label>
            <select id="entries" wire:model.live="entries" class="form-select w-auto">
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
        <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
            <label for="search" class="form-label mb-0">Search:</label>
            <input id="search" wire:model.live="search" type="text" class="form-control w-50" placeholder="Search something...">
        </div>
    </div>
    <div class="row" wire:poll>
        @if(empty($search))
            <div class="col-12 col-md-4 mb-4">
                <a href="{{route('job.posts.create')}}" class="text-decoration-none">
                    <div class="card create">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div class="text-center">
                                <div class="icon text-center">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                                <div class="label">
                                    <div>
                                        Create New
                                    </div>
                                    <div>
                                        Add or post a new job opportunity
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif
        @forelse ($records as $record)
            <div class="col-12 col-md-4 mb-4">
                <div class="card shadow px-2">
                    <a href="{{route('home.view-job', ['slug' => $record->slug])}}" class="nav-link" wire:ignore.self>
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
                        </div>
                    </a>
                    <div class="actions">
                        <div class="dropdown" wire:ignore>
                            <button class="btn btn-transparent d-flex align-items-start justify-content-center" type="button" id="menu" data-bs-toggle="dropdown" aria-expanded="true">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="menu" data-bs-popper="static">
                                <li>
                                    <a class="dropdown-item" href="{{route('job.posts.edit', ['post' => $record->id])}}">Update</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" wire:click="remove(true, {{$record->id}})" class="dropdown-item">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <hr class="mx-3">
                    <a href="{{route('home.view-job', ['slug' => $record->slug])}}" class="nav-link" wire:ignore.self>
                        <div class="card-body pt-1">
                            <div class="perks">
                                <div>{{money_format($record->min_salary) . ' - ' . money_format($record->max_salary)}} per month</div>
                                @if(!is_null($record->employment_type_id))
                                <div>{{$record->employment_type->name}}</div>
                                @endif
                                <div>{{$record->setup}}</div>
                                <div>{{$record->slots . ' Slots'}}</div>
                            </div>
                            <div class="description">
                                <small class="text-muted fst-italic fw-bold text-uppercase text-decoration-underline" style="text-underline-offset: 4px">Description</small>
                                <div class="description-content mt-2 pb-4">
                                    {!!see_more(strip_tags($record->description), 400)!!}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @empty
            <div class="alert alert-primary text-center text-uppercase fw-medium">No data was found</div>
        @endforelse
    </div>
    <div class="mt-4">
        {{ $records->links(data: ['scrollTo' => false]) }}
    </div>
</div>