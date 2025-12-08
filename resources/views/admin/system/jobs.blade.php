@extends('layouts.admin', [
    'title' => 'HRIS | System Jobs'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1 class="fw-bold">System Batch Jobs Monitoring</h1>
            <p class="text-muted mb-0">View all system batch jobs</p>
        </div>
    </div>
    @livewire('admin.system.jobs', ['batch_id' => $batch_id])
</div>
</div>
@endsection