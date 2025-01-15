@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center my-5">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header pt-4 px-4 bg-transparent border-0">
                    <h6 class="text-uppercase fw-bold mb-0">{{ __('Change Password') }}</h6>
                    <p class="mt-2 text-muted mb-0">Please provide a new and strong password.</p>
                    <hr>
                </div>
                <div class="card-body px-4">
                    @if(!is_null($token))
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email }}">

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('New Password') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="••••••••">
                                <div class="error mt-2">
                                    @error('password')
                                        <span class="text-danger" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="••••••••">
                            </div>

                            <div class="mt-5 mb-4">
                                <button type="submit" class="w-100 btn btn-primary px-5 py-3 text-uppercase fw-bold">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info text-uppercase fw-bold text-center">{{__('auth.passwords.token')}}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
