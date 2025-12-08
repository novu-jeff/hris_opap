@extends('layouts.admin', [
    'title' => 'HRIS | Add Section'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Add Section</h1>
            <p>Create new sections or offices</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.section.create')
    </div>
</div>
</div>
@endsection
