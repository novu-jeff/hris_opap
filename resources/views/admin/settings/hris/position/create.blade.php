@extends('layouts.admin', [
    'title' => 'HRIS | Add Position'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Add Position</h1>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.position.create')
    </div>
</div>
</div>
@endsection
