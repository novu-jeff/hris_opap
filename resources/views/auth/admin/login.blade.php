@extends('layouts.auth', [
    'title' => 'HRIS | Admin Login'
])

@section('content')
    <div class="login d-flex justify-content-center align-items-center">
        <div class="row py-5 d-flex justify-content-center align-items-center w-100">
            <div class="col-12 col-md-10 col-lg-5">
                <div class="container">
                    <form method="POST" action="{{route('admin.login')}}">
                        @method('POST')
                        @csrf
                        <div class="card shadow p-3">
                            <div class="card-header bg-transparent py-2 border-0">
                                <div class="d-flex justify-content-between align-items-center">    
                                    <div class="logo">
                                        <img src="{{asset('img/logo.png')}}" alt="logo">
                                    </div>
                                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button type="button" class="nav-link active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab" aria-controls="pills-login" aria-selected="true">Admin Login</button>
                                        </li>
                                    </ul> 
                                </div>                 
                                <div class="note mt-4 mb-3">
                                    By creating an account or signing in, you agree to Novulutions' HRIS Terms. You also acknowledge our Cookie and Privacy policies. Novulutions' HRIS will send you marketing messages, and you can opt out at any time by following the unsubscribe link in those messages or as described in our terms.
                                </div>                
                            </div>
                            <hr class="my-2">
                            <div class="card-body">
                                <div class="row">
                                    @if (session()->has('error'))
                                        <div class="alert alert-danger mb-3 text-uppercase fw-medium text-center fs-6">{{session('error')}}</div>
                                    @endif
                                    <div class="col-12 mb-3">
                                        <label for="email" class="mb-2">Email <span class="text-danger">*</span></label>
                                        <input type="text" name="email" id="email" class="form-control" placeholder="E-ID">
                                        <div class="error-field mt-1">
                                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="password" class="mb-2">Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••">
                                        <div class="error-field mt-1">
                                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 d-flex gap-2 justify-content-end">
                                <button class="btn btn-primary px-4 py-2">Proceed</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
