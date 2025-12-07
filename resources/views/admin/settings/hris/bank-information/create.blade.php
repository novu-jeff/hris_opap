@extends('layouts.admin', [
    'title' => 'HRIS | Add Bank Information'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Add Bank Information</h1>
            <p>Create or link a bank</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.bank-information.create')
    </div>
</div>
</div>
@endsection
