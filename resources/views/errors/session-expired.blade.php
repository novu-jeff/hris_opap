@extends('layouts.employee', [
    'title' => 'Session Expired'
])

@section('content')
<div class="container py-5">
    <div class="text-center">
        <h1 class="display-6 fw-bold text-danger">419</h1>
        <p class="lead">Your session has expired.</p>

        <a href="{{ route('employee.login') }}" class="btn btn-primary mt-3">
            Login Again
        </a>
    </div>
</div>
@endsection
