@extends('layouts.admin', [
    'title' => 'HRIS | ESS Employee Profile Update Approval'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>View Profile Update Approval</h1>
            <p>View profile updated by users</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.ess.profile-approval.edit', ['employee_no' => $employee_no])
    </div>
</div>
@endsection