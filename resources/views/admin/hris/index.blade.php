@extends('layouts.admin', [
    'title' => 'HRIS | Employee Records'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>HRIS • Employee Records</h1>
            <p></p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.hris.index')
    </div>
</div>
@endsection