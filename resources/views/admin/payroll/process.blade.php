@extends('layouts.admin', [
    'title' => 'HRIS | Payroll for ' . $payroll_date
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Payroll Computation</h1>
        </div>
    </div>
    <div class="action">
        <div class="d-md-flex justify-content-end gap-3">
            <a href="{{route('payroll.index')}}" class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.payroll.process', ['payroll_id' => $id])
    </div>
</div>
@endsection
