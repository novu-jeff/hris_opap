@extends('layouts.admin', [
    'title' => 'Symphony | All Cost Centers'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>All Cost Centers</h1>
            <p>See all cost centers</p>
        </div>
        <div class="actions">
            <a href="{{route('cost-center.create')}}" class="btn btn-primary">Create New</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.cost-center.index')
    </div>
</div>
@endsection