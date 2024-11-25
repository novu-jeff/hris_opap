@extends('layouts.admin', [
    'title' => 'HRIS | All Leave Types'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>All Leave Types</h1>
        </div>
        <div class="actions">
            <a href="{{route('leave.create')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.leave.index')
    </div>
</div>
@endsection