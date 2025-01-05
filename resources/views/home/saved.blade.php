@extends('layouts.app', [
    'title' => 'HRIS | All Saved Jobs'
])

@section('content')
<div class="container mt-5 pb-5">
    <div class="mt-5">
        @livewire('home.saved')
    </div>
</div>
@endsection