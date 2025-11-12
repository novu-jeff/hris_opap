@extends('layouts.employee', [
    'title' => 'ESS | Dashboard'
])

@section('content')
<div class="container pb-5">
    @livewire('employee.dashboard')
</div>
@endsection