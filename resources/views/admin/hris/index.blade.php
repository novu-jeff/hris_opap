@extends('layouts.admin', [
    'title' => 'HRIS | Employee Records'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Employee Records</h1>
            <p></p>
        </div>
    </div>
    <div class="mt-3">
        @if(!isset($employee_no))
            @livewire('admin.hris.index', ['selectedType' => $employment_type ?? null])
        @else
   
            @livewire('admin.hris.form', ['employee_no' => $employee_no, 'form' => $form])
        @endif
    </div>
</div>
</div>
@endsection