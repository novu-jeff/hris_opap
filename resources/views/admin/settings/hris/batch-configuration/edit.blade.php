@extends('layouts.admin', [
    'title' => 'HRIS | Edit Batch Information'
    ])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Edit Batch Information</h1>
        </div>
        <div class="actions">
            <a href="{{route('batch-configuration.index')}} class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.batch.edit', [
            'id' => $id
        ])
    </div>
</div>
@endsection