@extends('layouts.admin', [
    'title' => 'HRIS | Edit Bank Information'
    ])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Edit Bank Information</h1>
            <p>Edit details of bank</p>
        </div>
        <div class="actions">
            <a href="{{route('bank-information.index')}}"class="btn btn-primary">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.bank-information.edit', [
            'id' => $id
        ])
    </div>
</div>
@endsection