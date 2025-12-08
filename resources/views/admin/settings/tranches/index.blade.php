@extends('layouts.admin', [
    'title' => 'HRIS | All Tranches'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Manage All Tranches</h1>
        </div>
        <div class="action">
            <div class="d-md-flex gap-3">
                <a href="{{route('tranches.create')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Add New</a>
            </div>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.tranches.index')
    </div>
</div>
</div>
@endsection