@extends('layouts.admin', [
    'title' => 'Symphony | User Management'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>All Users</h1>
        </div>
        <div class="actions">
            {{-- <a href="{{route('users')}}" class="btn btn-primary">Create New</a> --}}
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.users.index', [
            'type' => $type
        ])
    </div>
</div>
@endsection