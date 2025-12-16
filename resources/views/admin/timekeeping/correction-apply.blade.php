@extends('layouts.admin', [
    'title' => 'HRIS | Apply Correction'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Apply Correction to Logs</h1>
        </div>
        <div class="action">
            <div class="d-md-flex gap-3">
                <!-- <a href="{{route('timekeeping.index')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a> -->
            </div>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.timekeeping.correction-apply', ['bsd_no' => $bsd_no, 'date' => $date])
    </div>
</div>
</div>
@endsection