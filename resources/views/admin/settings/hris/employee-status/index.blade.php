@extends('layouts.admin', [
    'title' => 'HRIS | All Employee Statuses'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>All Employee Statuses</h1>
        </div>
        <div class="actions">
            <a href="{{route('employee-status.create')}}" class="btn btn-primary">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.employee-status.index')
    </div>
</div>
@endsection