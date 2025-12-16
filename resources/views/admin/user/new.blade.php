@extends('layouts.admin', [
    'title' => 'HRIS | Add New Administrator'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Add New Administrator</h1>
        </div>
        <div class="action">
            <div class="d-flex gap-3">
                <!-- <a href="{{route('users.index', ['type' => 'admin'])}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a> -->
            </div>
        </div>
    </div>
    <div class="mt-5">
        @livewire('admin.settings.users.admin.add')
    </div>
</div>
</div>
@endsection