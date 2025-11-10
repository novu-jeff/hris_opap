@extends('layouts.admin', [
    'title' => 'HRIS | Central / Field Offices'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Central / Field Offices</h1>
            <p>See all locations</p>
        </div>
        <div class="actions">
            <a href="{{route('branch.create')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.branch.index')
    </div>
</div>
@endsection