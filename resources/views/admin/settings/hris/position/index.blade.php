@extends('layouts.admin', [
    'title' => 'HRIS | All Positions'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>All Positions</h1>
        </div>
        <div class="actions">
            <a href="{{route('position.create')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.position.index')
    </div>
</div>
@endsection