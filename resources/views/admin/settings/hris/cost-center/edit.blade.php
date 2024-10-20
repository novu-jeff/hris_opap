@extends('layouts.admin', [
    'title' => 'Symphony | Edit Cost Center Information'
    ])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Edit Cost Center Information</h1>
            <p>Modify or update</p>
        </div>
        <div class="actions">
            <a href="{{route('cost-center.index')}}"class="btn btn-primary">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.cost-center.edit', [
            'id' => $id
        ])
    </div>
</div>
@endsection