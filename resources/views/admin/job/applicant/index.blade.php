@extends('layouts.admin', [
    'title' => 'Symphony | Job Posted'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Manage All Applications</h1>
            <p>View all the applicants and process</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.job.applicant.index', ['status' => $status])
    </div>
</div>
@endsection