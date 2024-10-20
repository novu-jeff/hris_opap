@extends('layouts.admin', [
    'title' => 'Symphony | Add Cost Center'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Add Cost Center</h1>
            <p>Create new cost center</p>
        </div>
        <div class="actions">
            <a href="{{route('cost-center.index')}}"class="btn btn-primary">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.cost-center.create')
    </div>
</div>
@endsection
