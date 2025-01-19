@extends('layouts.admin', [
    'title' => 'HRIS | All Logs with discrepancy'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Time logs with discrepancy</h1>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.timekeeping.correction', [
            'year' => $year ?? null,
            'day' => $day ?? null,
            'month' => $month ?? null,
            'setup' => $setup ?? null
        ])
    </div>
</div>
@endsection