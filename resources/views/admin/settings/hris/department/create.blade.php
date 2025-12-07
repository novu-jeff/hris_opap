@extends('layouts.admin', [
    'title' => 'HRIS | Add Cluster'
])

@section('content')
<div class="main-content flex-grow-1 p-4">  
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Add Cluster</h1>
            <p>Create new cluster</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.department.create')
    </div>
</div>
</div>
@endsection
