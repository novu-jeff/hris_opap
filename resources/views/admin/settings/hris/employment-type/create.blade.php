@extends('layouts.admin', [
    'title' => 'HRIS | Add Employees Status'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Add Employment Type</h1>
        </div>
        <div class="actions">
            <a href="{{route('employment-type.index')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.employment-type.create')
    </div>
</div>
@endsection
