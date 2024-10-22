@extends('layouts.auth')

@section('content')
    <div class="login">
        <div class="row d-flex justify-content-center align-items-center py-5">
            <div class="col-12 col-md-5">
                <div class="container">
                    <form id="submit-form">
                        @method('POST')
                        @csrf
                        <div class="card shadow p-3">
                            <div class="card-header bg-transparent py-2 border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button type="button" class="nav-link active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab" aria-controls="pills-login" aria-selected="true">Admin Login</button>
                                        </li>
                                    </ul>     
                                    <div class="logo">
                                        <img src="{{asset('img/logo.png')}}" alt="logo">
                                    </div>
                                </div>                 
                                <div class="note mt-4 mb-3">
                                    By creating an account or signing in, you agree to Symphony's Terms. You also acknowledge our Cookie and Privacy policies. Symphony will send you marketing messages, and you can opt out at any time by following the unsubscribe link in those messages or as described in our terms.
                                </div>                
                            </div>
                            <hr class="my-2">
                            <div class="card-body">
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="pills-login" tabindex="0">
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label for="email" class="mb-2">Email <span class="text-danger">*</span></label>
                                                <input type="text" name="email" id="email" class="form-control">
                                                <div class="error-field"></div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label for="password" class="mb-2">Password <span class="text-danger">*</span></label>
                                                <input type="password" name="password" id="password" class="form-control">
                                                <div class="error-field"></div>
                                            </div>
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

@section('scripts')
<script>
    $(function() {
        $('#submit-form').submit(function(e) {
            e.preventDefault();
            const url = '{{route('admin.login')}}';
            const data = new FormData($(this)[0]);
            post(false, url, data);
        });
    });
</script>
@endsection