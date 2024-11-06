@extends('layouts.admin', [
    'title' => 'HRIS | Add Violation'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Add Violation</h1>
        </div>
        <div class="actions">
            <a href="{{route('violation.index')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.violation.create')
    </div>
</div>
@endsection
