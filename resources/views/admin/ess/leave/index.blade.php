@extends('layouts.admin', [
    'title' => 'HRIS | ESS Leave Application'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Leave Applications</h1>
            <p>Manage all employee's application</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.ess.leave.index', [
            'status' => $status
        ])
    </div>
</div>
@endsection