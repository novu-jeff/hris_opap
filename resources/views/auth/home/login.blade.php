@extends('layouts.auth', [
    'title' => 'HRIS | Applicant Login'
])

@section('content')
    <div class="login">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-10 col-lg-5">
                <div class="container">
                    @livewire('home.login')
                </div>
            </div>
        </div>
    </div>
@endsection
