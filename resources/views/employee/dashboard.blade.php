@extends('layouts.employee', [
    'title' => 'ESS | Dashboard'
])

@section('content')
<div class="container pb-5">
    <div class="mt-5 d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Dashboard</h1>
            <p>Track and monitor your employment records.</p>
        </div>
    </div>
    @livewire('employee.dashboard')
</div>
@endsection