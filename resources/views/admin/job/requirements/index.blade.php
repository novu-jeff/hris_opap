@extends('layouts.admin', [
    'title' => 'HRIS | All Requirements'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Job Requirements</h1>
            <p>Manage all requirements</p>
        </div>
        <div class="actions">
            <a href="{{route('job.requirements.create')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.job.requirements.index')
    </div>
</div>
@endsection