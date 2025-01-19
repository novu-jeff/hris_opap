@extends('layouts.employee', [
    'title' => $title
])

@section('content')
<div class="container pb-5">
    <div class="mt-5 d-lg-flex justify-content-between align-items-start">
        <div class="section-title">
            <h1>{{$header}}</h1>
        </div>
        <div class="action">
            @if ($action === 'index')
                <div class="d-md-flex gap-3">
                    <a href="{{route('employee.dashboard')}}" class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
                </div>
            @endif
        </div>
    </div>
    <div class="mt-3">
        @livewire('employee.tutorial')
    </div>
</div>
@endsection