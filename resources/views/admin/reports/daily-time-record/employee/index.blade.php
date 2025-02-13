@extends('layouts.admin', [
    'title' => 'HRIS | All Shift Schedules'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Daily Time Record - <span  class="text-primary">{{ $month . ' ' . $year }}</span></h1>
        </div>
        <div class="actions">
            <a href="{{ route('reports.dtr') }}" class="btn btn-outline-danger text-uppercase px-5 py-3 fw-medium">Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.reports.daily-time-record.employee.index', ['month' => $month, 'year' => $year])
    </div>
</div>
@endsection

