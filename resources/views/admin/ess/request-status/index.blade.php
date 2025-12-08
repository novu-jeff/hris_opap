@extends('layouts.admin', [
    'title' => $title
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>{{$header}}</h1>
            <p>{{$sub}}</p>
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
</div>
@endsection
