@extends('layouts.admin', [
    'title' => 'HRIS | GSIS Billing'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>GSIS Billing</h1>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.gsis.index')
    </div>
</div>
@endsection