@extends('layouts.admin', [
    'title' => 'HRIS | All Violations'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>All Violations</h1>
        </div>
        <div class="actions">
            <a href="{{route('violation.create')}}" class="btn btn-primary">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.violation.index')
    </div>
</div>
@endsection