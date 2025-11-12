@extends('layouts.employee', [
    'title' => $title
])

@section('content')
<div class="container pb-5">
    <div class="mt-5 d-lg-flex justify-content-between align-items-start">
        <div class="section-title">
            <h1>{{$header}}</h1>
            <p>{{$sub}}</p>
        </div>
        <div class="action">
            @if ($action === 'view')
                <div class="d-md-flex gap-3 ">
                    <a href="{{route('employee.dashboard')}}" class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
                    @can('write apply-time-adjustments')
                        <a href="{{route('employee.time-adjustments.apply')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Apply Now</a>
                    @endcan
                </div>
            @else
                <a href="{{route('employee.time-adjustments')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
            @endif
        </div>
    </div>
    <div class="mt-3">
        @if ($action == 'view')
            @livewire('employee.time-adjustments.index')
        @else 
            @livewire('employee.time-adjustments.apply', [
                'record_id' => $id ?? null
            ])
        @endif
    </div>
</div>
@endsection