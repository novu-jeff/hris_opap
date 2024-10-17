@extends('layouts.app', [
    'title' => 'Symphony | All Jobs'
])

@section('content')
<div class="container mt-5 pb-5">
    <div class="mt-5">
        @livewire('home.applied')
    </div>
</div>
@endsection