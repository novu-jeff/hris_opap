@extends('layouts.admin', [
    'title' => 'HRIS | Deductions'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>{{$header->name}}</h1>
        </div>
        <div class="actions">
            <a href="{{route('other-deductions.index')}}" class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.emp-deductions.index', ['id' => $id])
    </div>
</div>
@endsection