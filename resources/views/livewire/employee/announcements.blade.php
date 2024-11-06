<div class="announcements" wire:poll>
    @if(is_null($record_id))
        <div class="row">
            @forelse ($records as $item)
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <img src="{{asset('img/loginbackground.jpg')}}" class="card-img-top" alt="News Image">
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
                    <div class="alert alert-info mb-0 text-uppercase text-center fw-bold">No Announcements</div>
                </div>
            @endforelse
        </div>
    @else

        <hr>
        <div class="mt-5">    
            <div class="title">
                <h1>{{$records->title}}</h1>
                <h5 class="text-muted mt-3">Posted: {{relative_time($records->created_at)}}</h5>
            </div>
            <div class="banner mt-4">
                <img src="{{Storage::url('public/announcements/' . $records->banner)}}" alt="">
            </div>
            <div class="content mt-4">
                {!!$records->content!!}
            </div>
        </div>
    @endif
</div>
