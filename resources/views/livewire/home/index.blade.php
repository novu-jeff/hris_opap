@extends('layouts.app', [
    'title' => 'Symphony | All Jobs'
])

@section('content')
<div class="container mt-5 pb-5">
    <div class="mt-5">
        @livewire('home.jobs', [
            'lazy' => true,
            'search_query' => $search,
        ])
        
    </div>
</div>
@endsection