@extends('layouts.admin', [
    'title' => 'HRIS | Edit Section (Offices)'
    ])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Edit Section (Offices)</h1>
            <p>Modify or update sections or offices</p>
        </div>
        <div class="actions">
            <a href="{{route('section.index')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.section.edit', [
            'id' => $id
        ])
    </div>
</div>
@endsection