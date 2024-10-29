@extends('layouts.admin', [
    'title' => 'HRIS | All Department Centers'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>All Department Centers</h1>
            <p>See all department centers</p>
        </div>
        <div class="actions">
            <a href="{{route('department-center.create')}}" class="btn btn-primary">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.department.index')
    </div>
</div>
@endsection