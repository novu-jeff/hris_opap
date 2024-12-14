@extends('layouts.admin', [
    'title' => 'HRIS | All Shift Schedules'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Add New Shift Schedule</h1>
        </div>
        <div class="action">
            <div class="d-flex gap-3">
                <a href="{{route('shift-schedule.index')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
            </div>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.shift-schedule.create')
    </div>
</div>
@endsection