@extends('layouts.admin', [
    'title' => 'HRIS | All Logs'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Payroll</h1>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.payroll.index')
    </div>
</div>
@endsection