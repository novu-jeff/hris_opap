

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
            @if($view->attachments->isNotEmpty())
                <hr>
                <div class="attachments mt-4">
                    <h5>Downloadable Attachments</h5>
                    <ul class="list-unstyled text-uppercase mt-3">
                        @foreach($view->attachments as $attachment)
                            <li class="list-unstyled-item mb-2">
                                <a class="d-flex align-items-center gap-2 text-decoration-none" href="{{Storage::url('public/announcements/' . $attachment->file)}}" download>
                                    <i class="fa-solid fa-download"></i>
                                    {{$attachment->name}}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <p style="cursor: pointer; font-size: 14px;">
                <span class="text-uppercase fw-bold text-muted">Seen By:</span> 
                <span 
                    class="text-capitalize seen-by-content" 
                >
                    @foreach(collect($seenBy) as $user)
                        <span 
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            title="{{ \Carbon\Carbon::parse($user['timestamp'])->format('F d, Y \a\t h:i A')}}">
                            {{ $user['name'] }}
                        </span>{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </span>
            </p>            
        </div>
        <div class="mt-5">
            <div class="float-start">
                @if(!is_null($nextAndPrev['prev']))
                    <a wire:navigate href="{{$nextAndPrev['prev']}}" class="text-uppercase fw-bold text-primary">
                        <i class="fa-solid fa-arrow-left-long me-2"></i> Previous
                    </a>
                @endif
            </div>
            <div class="float-end">
                @if(!is_null($nextAndPrev['next']))
                    <a wire:navigate href="{{$nextAndPrev['next']}}" class="text-uppercase fw-bold text-primary">
                        Next <i class="fa-solid fa-arrow-right-long ms-2"></i>
                    </a>
                @endif  
            </div>
        </div>
    @endif
</div>


@section('script')
    <script>
        $(function () {
            $('.seen-by-content').each(function () {
                const $this = $(this);
                const fullText = $this.text().trim();

                if (fullText.length > 1000) {
                    const shortText = fullText.substring(0, 1000) + '... ';
                    
                    const moreLink = $('<a href="#" class="see-more text-primary" style="font-size: 15px;">See more</a>');
                    const lessLink = $('<a href="#" class="see-less text-primary" style="display:none; font-size: 15px;">See less</a>');

                    // Store full and short text versions
                    const shortSpan = $('<span class="short-text"></span>').text(shortText);
                    const fullSpan = $('<span class="full-text" style="display:none;"></span>').text(fullText + ' ');

                    $this.empty().append(shortSpan).append(moreLink).append(fullSpan).append(lessLink);

                    $this.on('click', '.see-more', function (e) {
                        e.preventDefault();
                        $this.find('.short-text, .see-more').hide();
                        $this.find('.full-text, .see-less').show();
                    });

                    $this.on('click', '.see-less', function (e) {
                        e.preventDefault();
                        $this.find('.full-text, .see-less').hide();
                        $this.find('.short-text, .see-more').show();
                    });
                }
            });
        });
    </script>
@endsection