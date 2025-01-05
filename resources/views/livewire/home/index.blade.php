@extends('layouts.app', [
    'title' => 'HRIS | All Jobs'
])

@section('content')
<div class="container mt-5 pb-5">
    <div class="mt-5">
        @livewire('home.jobs', [
            'lazy' => true,
            'search_query' => $parameter,
        ])
        
    </div>
</div>
@endsection