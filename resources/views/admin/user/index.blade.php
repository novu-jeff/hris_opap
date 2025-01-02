@extends('layouts.admin', [
    'title' => 'HRIS | User Management'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>All </h1>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.users.index', [
            'type' => $type
        ])
    </div>
</div>
@endsection