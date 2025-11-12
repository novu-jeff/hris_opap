@extends('layouts.admin', [
    'title' => 'HRIS | ESS Leave Application'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Payslip Request</h1>
            <p>Manage all payslip request</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.ess.payslip-request.index', [
            'status' => $status
        ])
    </div>
</div>
@endsection