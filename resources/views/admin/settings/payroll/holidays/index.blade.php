@extends('layouts.admin', [
    'title' => 'HRIS | All Holidays'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>All Holidays</h1>
        </div>
        <div class="actions">
            <a href="{{route('holiday.create')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.payroll.holiday.index')
    </div>
</div>
@endsection