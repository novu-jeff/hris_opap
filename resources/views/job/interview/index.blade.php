@extends('layouts.admin', [
    'title' => 'Symphony | Job Posted'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>All Interview Questions</h1>
            <p>See all the interview questions</p>
        </div>
        <div class="actions">
            <a href="{{route('job.interview.create')}}" class="btn btn-primary">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.job.interview.index')
    </div>
</div>
@endsection