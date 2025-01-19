@extends('layouts.admin', [
    'title' => 'HRIS | Add New Employee'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Add New Employee</h1>
            <p></p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.hris.manual')
    </div>
</div>
@endsection