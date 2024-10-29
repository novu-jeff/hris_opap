@extends('layouts.admin', [
    'title' => 'HRIS | All Batch Informations'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>All Batches</h1>
        </div>
        <div class="actions">
            <a href="{{route('batch-configuration.create')}}" class="btn btn-primary">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.batch.index')
    </div>
</div>
@endsection