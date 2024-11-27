@extends('layouts.admin', [
    'title' => 'HRIS | Job Posts'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Job Posts</h1>
            <p>See the latest jobs available</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.job.posts.index')
    </div>
</div>
@endsection