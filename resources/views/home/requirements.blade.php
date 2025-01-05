@extends('layouts.app', [
    'title' => 'HRIS | Upload Requirements'
])

@section('content')
<div class="container mt-5 pb-5">
    <div class="mt-5">
        @livewire('home.requirements', [
            'job_id' => $job_id,
        ])
    </div>
</div>
@endsection