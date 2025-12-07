@extends('layouts.admin', [
    'title' => 'HRIS | Job Posts'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Manage All Applications</h1>
            <p>View all the applicants and process</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.job.applicant.index', ['status' => $status])
    </div>
</div>
</div>
@endsection