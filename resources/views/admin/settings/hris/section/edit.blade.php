@extends('layouts.admin', [
    'title' => 'HRIS | Edit Section'
    ])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Edit Section</h1>
            <p>Modify or update sections or offices</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.section.edit', [
            'id' => $id
        ])
    </div>
</div>
</div>
@endsection