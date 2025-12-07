@extends('layouts.admin', [
    'title' => 'HRIS | Add Cost Center'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Add Cost Center</h1>
            <p>Create new cost center</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.cost-center.create')
    </div>
</div>
</div>
@endsection
