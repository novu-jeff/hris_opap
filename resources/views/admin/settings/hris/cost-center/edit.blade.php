@extends('layouts.admin', [
    'title' => 'HRIS | Edit Cost Center Information'
    ])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Edit Cost Center Information</h1>
            <p>Modify or update</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.hris.cost-center.edit', [
            'id' => $id
        ])
    </div>
</div>
</div>  
@endsection