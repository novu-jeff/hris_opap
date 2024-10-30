@extends('layouts.employee', [
    'title' => $title
])

@section('content')
<div class="container pb-5">
    <div class="mt-5 d-flex justify-content-between align-items-start">
        <div class="section-title">
            <h1>{{$header}}</h1>
            <p>{{$sub}}</p>
        </div>
        <div class="action">
           
        </div>
    </div>
    <div class="mt-3">
        @livewire('employee.profile')
    </div>
</div>
@endsection