@extends('layouts.admin', [
    'title' => 'Symphony | Add Department Center'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Add Department Center</h1>
            <p>Create new department center</p>
        </div>
        <div class="actions">
            <a href="{{route('department-center.index')}}"class="btn btn-primary">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.department.create')
    </div>
</div>
@endsection
