@extends('layouts.admin', [
    'title' => 'Symphony | Job Posted'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>All Applicants</h1>
            <p>See all the applicants</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.job.applicant.index', ['status' => $status])
    </div>
</div>
@endsection