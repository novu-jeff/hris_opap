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
            @if ($action === 'view')
                <a href="{{route('employee.leave.apply')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Apply Now</a>
            @else
                <a href="{{route('employee.leave')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
            @endif
        </div>
    </div>
    <div class="mt-3">
        @if ($action == 'view')
            @livewire('employee.leave.index')
        @else 
            @livewire('employee.leave.apply', [
                'record_id' => $id ?? null
            ])
        @endif
    </div>
</div>
@endsection