@extends('layouts.admin', [
    'title' => 'HRIS | All Roles'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Manage Roles</h1>
        </div>
        <div class="action">
            <div class="d-flex gap-3">
                <a href="{{route('users.access.create')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Add New</a>
            </div>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.roles.index')
    </div>
</div>
@endsection