@extends('layouts.admin', [
    'title' => 'HRIS | All Shift Schedules'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Manage Shift Schedule</h1>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.shift-schedule.index')
    </div>
</div>
@endsection