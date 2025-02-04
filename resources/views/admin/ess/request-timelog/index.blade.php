@extends('layouts.admin', [
    'title' => 'HRIS | ESS Request Timelogs'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Request Timelogs</h1>
            <p>Manage all timelogs application</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.ess.request-timelog.index', [
            'status' => $status
        ])
    </div>
</div>
@endsection