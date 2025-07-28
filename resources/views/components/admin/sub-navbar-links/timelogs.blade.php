<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-clock"></i>
        Timekeeping
    </a>
    <ul class="dropdown-menu">
        @if(config('app.allow_upload_timelogs'))
            @can('write timelogs')
                <li><a class="dropdown-item" href="{{route('timekeeping.upload')}}">Add Time Logs</a></li>
            @endcan
        @endif
    </ul>
</li>