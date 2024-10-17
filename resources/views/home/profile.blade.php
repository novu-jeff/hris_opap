@extends('layouts.app', [
    'title' => 'Profile | ' . 
    strtoupper(Auth::user()->firstname . ' ' . Auth::user()->lastname)
])

@section('content')
<div class="container mt-5 pb-5">
    <div class="mt-5">
        @livewire('home.profile')
    </div>
</div>
@endsection