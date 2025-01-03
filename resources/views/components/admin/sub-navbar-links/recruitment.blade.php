@canany([
    'read jobs',
    'read applicants'
])
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-user-plus"></i>
        Recruitment
    </a>
    <ul class="dropdown-menu">
        @can('read jobs')
            <li><a class="dropdown-item" href="{{route('job.posts.index')}}">Job Posting</a></li>
        @endcan
        @can('read applicants')
            <li><a class="dropdown-item" href="{{route('job.applicants.index', ['status' => 'pending'])}}">Applicants</a></li>
        @endcan
    </ul>
</li>
@endcanany