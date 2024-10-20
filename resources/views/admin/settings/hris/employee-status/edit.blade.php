@extends('layouts.admin', [
    'title' => 'Symphony | Edit Employee Status'
    ])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Edit Employee Status</h1>
        </div>
        <div class="actions">
            <a href="{{route('employee-status.index')}}"class="btn btn-primary">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.employee-status.edit', [
            'id' => $id
        ])
    </div>
</div>
@endsection