@extends('layouts.admin', [
    'title' => 'Settings | Company Information'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Update Company Information</h1>
            <p>Modify or update company related informations</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.company.index')
    </div>
</div>
</div>
@endsection