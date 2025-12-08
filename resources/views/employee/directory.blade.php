@extends('layouts.employee', [
    'title' => $title
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="mt-5 d-lg-flex justify-content-between align-items-start">
        <div class="section-title">
            <h1>{{$header}}</h1>
            <p>{{$sub}}</p>
        </div>
       
    </div>
    <div class="mt-3">
        @livewire('employee.directory')
    </div>
</div>
</div>
@endsection