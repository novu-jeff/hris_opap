@extends('layouts.admin', [
    'title' => 'HRIS | Edit Position'
    ])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Edit Position</h1>
        </div>
        <div class="actions">
            <a href="{{route('position.index')}}"class="btn btn-primary">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.position.edit', [
            'id' => $id
        ])
    </div>
</div>
@endsection