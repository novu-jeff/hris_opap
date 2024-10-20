@extends('layouts.admin', [
    'title' => 'Symphony | Add Bank Information'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Add Bank Information</h1>
            <p>Create or link a bank</p>
        </div>
        <div class="actions">
            <a href="{{route('bank-information.index')}}"class="btn btn-primary">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.bank-information.create')
    </div>
</div>
@endsection
