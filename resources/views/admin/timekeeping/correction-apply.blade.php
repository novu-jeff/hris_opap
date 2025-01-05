@extends('layouts.admin', [
    'title' => 'HRIS | Apply Correction'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Apply Correction to Logs</h1>
        </div>
        <div class="action">
            <div class="d-flex gap-3">
                <a href="{{route('timekeeping.correction')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
            </div>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.timekeeping.correction-apply', ['id' => $id])
    </div>
</div>
@endsection