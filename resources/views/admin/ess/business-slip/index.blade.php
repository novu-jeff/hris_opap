@extends('layouts.admin', [
    'title' => 'HRIS | ESS Official Business Slip Application'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Official Business Application</h1>
            <p>Manage all applications</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.ess.business-slip.index', [
            'status' => $status
        ])
    </div>
</div>
@endsection