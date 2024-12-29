@extends('layouts.app', [
    'title' => 'HRIS | View Job'
])

@section('content')
<div class="container mt-5 pb-5">
    <div class="mt-5">
        @livewire('home.view-job', ['slug' => $slug])
    </div>
</div>
@endsection