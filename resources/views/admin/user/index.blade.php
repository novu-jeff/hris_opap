@extends('layouts.admin', [
    'title' => 'HRIS | User Management'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>All Users</h1>
        </div>
        <div class="actions">
            {{-- <a href="{{route('users')}}"  class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Create New</a> --}}
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.users.index', [
            'type' => $type
        ])
    </div>
</div>
@endsection