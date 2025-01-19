@extends('layouts.admin', [
    'title' => 'HRIS | ESS Authority to Render Overtime Application'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Authority to Render Overtime Application</h1>
            <p>Manage all ATRO application</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.ess.atro.index', [
            'status' => $status
        ])
    </div>
</div>
@endsection