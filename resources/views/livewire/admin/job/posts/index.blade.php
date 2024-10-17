<div class="job">
    <div class="row" wire:poll>
        @foreach ($records as $record)
        <div class="col-12 col-md-4 mb-4">
            <div class="card shadow px-2">
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
                            • {{relative_time($record->created_at, 'hours ago')}}
                        </p>
                    </div>
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
                                    <a class="dropdown-item" href="{{route('job.posts.show', ['post' => $record->id])}}">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <hr class="mx-3">
                <div class="card-body pt-1">
                    <div class="perks">
                        <div>{{money_format($record->min_salary) . ' - ' . money_format($record->max_salary)}} per month</div>
                        <div>{{$record->type}}</div>
                        <div>{{$record->setup}}</div>
                    </div>
                    <div class="description">
                        <small class="text-muted fst-italic fw-bold text-uppercase text-decoration-underline" style="text-underline-offset: 4px">Description</small>
                        <div class="description-content mt-2">
                            {!!see_more(strip_tags($record->description), 400)!!}
                        </div>
                    </div>
                </div>
                <hr class="mx-3">
                <div class="card-footer bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center gap-4 pb-3">
                        <div>
                            <i class="fa-solid fa-user-group"></i>
                            <span class="ms-1">1</span>
                        </div>
                        <div>
                            <i class="fa-solid fa-user-clock"></i>
                            <span class="ms-1">1</span>
                        </div>
                        <div>
                            <i class="fa-solid fa-user-xmark"></i>
                            <span class="ms-1">3</span>
                        </div>
                        <div>
                            <i class="fa-solid fa-user-check"></i>
                            <span class="ms-1">3</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
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
                                    Add or post a new job opportuninity
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>