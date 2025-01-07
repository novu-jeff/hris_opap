<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css"
        integrity="sha512-pTaEn+6gF1IeWv3W1+7X7eM60TFu/agjgoHmYhAfLEU8Phuf6JKiiE8YmsNC0aCgQv4192s4Vai8YZ6VNM6vyQ=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"
        integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    ></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/sass/home-layout.scss'])

    @livewireStyles
    @livewireScripts
</head>
<body>
    <div id="app">
        @include('components.home.navbar')
        <main>
            <div class="content">
                @yield('content')
            </div>
        </main>
        <div class="footer mt-5">
            <div class="container mt-3 py-5">
                <div class="row">
                    <div class="col-12 col-lg-4 mb-4">
                        <div class="mx-5">
                            <div class="logo">
                                <img src="{{asset('/img/novu-blue.png')}}" alt="logo">
                            </div>
                            <div class="logo-phrase">
                                <p>Transform IT: Unify your data silos</p>
                            </div>
                            <hr class="mt-3 mb-2">
                            <div class="socials">
                                <ul class="list-inline">
                                    <li class="list-inline-item">
                                        <a target="_blank" href="https://novulutions.com/">
                                            <i class="fa-solid fa-earth-asia"></i>
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a target="_blank" href="https://www.facebook.com/novulutionsinc">
                                            <i class="fa-brands fa-facebook"></i>
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a target="_blank" href="https://www.linkedin.com/company/novulutions-inc/">
                                            <i class="fa-brands fa-linkedin"></i>
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a target="_blank" href="https://www.youtube.com/@NovulutionsInc">
                                            <i class="fa-brands fa-youtube"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mb-4">
                        <div class="useful-links">
                            <div>
                                <h5 class="text-muted">Site Links</h5>
                                <ul class="list-unstyled">
                                    <li class="list-unstyled-item">
                                        <a href="{{route('home.index')}}">Find Jobs</a>
                                    </li>
                                    <li class="list-unstyled-item">
                                        <a href="{{route('home.saved')}}">Saved Jobs</a>
                                    </li>
                                    <li class="list-unstyled-item">
                                        <a href="{{route('home.profile')}}">Profile</a>
                                    </li>
                                    <li class="list-unstyled-item">
                                        <a href="{{route('home.login')}}">Login</a>
                                    </li>
                                    <li class="list-unstyled-item">
                                        <a href="{{route('home.register')}}">Register</a>
                                    </li>
                                    <li class="list-unstyled-item">
                                        <a href="{{route('employee.dashboard')}}">Employee</a>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h5 class="text-muted">Services</h5>
                                <ul class="list-unstyled">
                                    <li class="list-unstyled-item">
                                        Novulutions-Nebulon Node
                                    </li>
                                    <li class="list-unstyled-item">
                                        Novulutions Cloud Platform
                                    </li>
                                    <li class="list-unstyled-item">
                                        Corporate Digital Nervous System
                                    </li>
                                    <li class="list-unstyled-item">
                                        SambaNova Systems
                                    </li>
                                    <li class="list-unstyled-item">
                                        Scality
                                    </li>                                    
                                </ul>
                            </div>
                            <div>
                                <h5 class="text-muted">Products</h5>
                                <ul class="list-unstyled">
                                    <li class="list-unstyled-item">
                                        Human Resources Information System
                                    </li>
                                    <li class="list-unstyled-item">
                                        N-Gas
                                    </li>    
                                    <li class="list-unstyled-item">
                                        N-Boss
                                    </li>                            
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="ending text-center mb-0 text-muted mt-5">
                    &copy; 2024. All rights reserved Novulutions Inc.
                </p>
            </div>
        </div class="footer">
    </div>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('script')
</body>
</html> 