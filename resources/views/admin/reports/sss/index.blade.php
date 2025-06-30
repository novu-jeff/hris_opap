@extends('layouts.admin', [
    'title' => 'Reports | SSS',
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>SSS Report </h1>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.reports.s-s-s.index')
    </div>
</div>
@endsection