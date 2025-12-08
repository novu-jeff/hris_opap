@extends('layouts.admin', [
    'title' => 'HRIS | ESS Request Time Adjustments'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Request Time Adjustments</h1>
            <p>Manage all adjustments of timelogs</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.ess.time-adjustments.index', [
            'status' => $status
        ])
    </div>
</div>
</div>
@endsection