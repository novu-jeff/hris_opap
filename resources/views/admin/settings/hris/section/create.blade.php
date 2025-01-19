@extends('layouts.admin', [
    'title' => 'HRIS | Add Section (Offices)'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Add Section (Offices)</h1>
            <p>Create new sections or offices</p>
        </div>
        <div class="actions">
            <a href="{{route('section.index')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.section.create')
    </div>
</div>
@endsection
