@extends('layouts.admin', [
    'title' => 'HRIS | Edit Administrator'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Edit Administrator</h1>
        </div>
        <div class="action">
            <div class="d-md-flex gap-3">
                <a href="{{route('users.index', ['type' => 'admin'])}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
            </div>
        </div>
    </div>
    <div class="mt-5">
        @livewire('admin.settings.users.admin.edit', [
            'id' => $id
        ])
    </div>
</div>
@endsection