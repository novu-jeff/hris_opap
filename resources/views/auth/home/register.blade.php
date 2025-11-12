@extends('layouts.auth', [
    'title' => 'HRIS | Applicant Register'
])

@section('content')
    <div class="register">
        <div class="d-flex">
            <div class="left-side">
                <div class="container mb-5">
                    <div class="logo">
                        <img src="{{ asset('/img/' . $provider['client_logo'])}}">
                    </div>
                    <div class="description mt-5 mx-5">
                        <h3>Create Account</h3>
                        <p>
                            Register now to take the first step toward exciting job opportunities and contribute to a thriving professional community. Join us in shaping your career journey!
                        </p>
                    </div>
                    <div class="actions d-flex justify-content-center gap-4 mt-4">
                        <a wire:navigate href="{{route('home.index')}}" class="btn btn-outline-primary px-5 py-2">Go Home</a>
                        <a wire:navigate href="{{route('home.login')}}" class="btn btn-primary px-5 py-2">Login</a>
                    </div>
                </div>
            </div>
            <div class="right-side py-5">
                <div class="container mx-5">
                    @livewire('home.register')
                </div>
            </div>
        </div>
    </div>
@endsection
