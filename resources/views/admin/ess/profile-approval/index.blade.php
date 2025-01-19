@extends('layouts.admin', [
    'title' => 'HRIS | ESS Employee Profile Update Approval'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Employee Profile Update Approval</h1>
            <p>Manage all employees updating their profile</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.ess.profile-approval.index')
    </div>
</div>
@endsection