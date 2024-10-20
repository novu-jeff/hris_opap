@extends('layouts.admin', [
    'title' => 'Symphony | Add Batch'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Add Batch</h1>
        </div>
        <div class="actions">
            <a href="{{route('batch-configuration.index')}}"class="btn btn-primary">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.batch.create')
    </div>
</div>
@endsection
