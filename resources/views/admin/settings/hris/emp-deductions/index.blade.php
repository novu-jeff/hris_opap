@extends('layouts.admin', [
    'title' => 'HRIS | Deductions'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>{{$header->name}}</h1>
        </div>
    </div>
    <div class="mt-5">
        @livewire('admin.settings.hris.emp-deductions.index', ['id' => $id])
    </div>
</div>
@endsection