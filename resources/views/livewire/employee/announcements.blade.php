<div class="announcements" >
    @if(is_null($record_id))
        <div class="row mb-4 mt-5">
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
        <div class="row">
            @forelse ($records as $item)
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="banner">
                            <img src="{{$item->banner === 'default.jpg' ? asset('img/announcement.jpg') : Storage::url('public/announcements/' . $item->banner)}}" class="card-img-top" alt="News Image">
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title text-clamp clamp-2">{{$item->title}}</h5>
                            <small class="fw-bold text-uppercase text-muted">Posted: {{relative_time($item->created_at)}}</small>
                            <p class="card-text text-clamp clamp-3 mt-3">{{strip_tags($item->content)}}</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 pb-4 pe-4 d-flex justify-content-end">
                            <a href="{{route('employee.announcements.view', ['id' => $item->id])}}" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">Read More</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-primary mb-0 text-uppercase text-center fw-bold">No Announcements Found</div>
                </div>
            @endforelse
        </div>
        <div class="mt-4 w-100">
            {{ $records->links(data: ['scrollTo' => false]) }}
        </div>
    @else
        <hr>
        <div class="mt-5">    
            <div class="title">
                <h1>{{$view->title}}</h1>
                <h5 class="text-muted mt-3">Posted: {{relative_time($view->created_at)}}</h5>
            </div>
            <div class="banner mt-4">
                <img src="{{$view->banner === 'default.jpg' ? asset('img/announcement.jpg') : Storage::url('public/announcements/' . $view->banner)}}" alt="">
            </div>
            <div class="content mt-4">
                {!!$view->content!!}
            </div>
        </div>
    @endif
</div>
