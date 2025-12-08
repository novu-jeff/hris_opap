@extends('layouts.admin', [
    'title' => 'HRIS | All Bank Informations'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Bank Information</h1>
            <p>See all related to banking</p>
        </div>
        <div class="actions">
            <a href="{{route('bank-information.create')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.bank-information.index')
    </div>
</div>
</div>
@endsection