@extends('layouts.admin', [
    'title' => 'HRIS | ESS Employee Profile Update Approval'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>View Profile Update Approval</h1>
            <p>View profile updated by users</p>
        </div>
       
    </div>
    <div class="alert alert-info text-muted fw-bold text-uppercase text-center">Please make sure to carefully review the changes or updates submitted by this employee.</div>
    <div class="mt-4">
        @livewire('admin.ess.profile-approval.show', ['employee_no' => $employee_no, 'form' => $form])
    </div>
</div>
</div>
@endsection