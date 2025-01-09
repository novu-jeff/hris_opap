@extends('layouts.admin', [
    'title' => $title
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>{{$header}}</h1>
            <p>{{$sub}}</p>
        </div>
        <div class="action">
            @if ($action === 'send')
                <a href="{{route('ess.request-status')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
            @endif
        </div>
    </div>
    <div class="mt-3">
        @if ($action == 'view')
            @livewire('admin.ess.request-status.index')
        @elseif($action == 'send')
            @livewire('admin.ess.request-status.chatbox', [
                'employee_no' => $employee_no
            ])
        @endif
    </div>
</div>
@endsection
