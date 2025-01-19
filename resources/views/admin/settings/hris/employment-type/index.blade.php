@extends('layouts.admin', [
    'title' => 'HRIS | All Employee Statuses'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>All Employment Types</h1>
        </div>
        <div class="actions">
            <a href="{{route('employment-type.create')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.employment-type.index')
    </div>
</div>
@endsection