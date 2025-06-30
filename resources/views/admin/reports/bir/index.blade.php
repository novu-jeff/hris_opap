@extends('layouts.admin', [
    'title' => 'HRIS | DTR'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>BIR Report </h1>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.reports.b-i-r.index')
    </div>
</div>
@endsection