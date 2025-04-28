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
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.2.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.2.0/mapbox-gl.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link
          rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"
          />

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/sass/home-layout.scss', 'resources/sass/employee-layout.scss', 'resources/sass/chat.scss'])

    @yield('style')

    @livewireStyles
    @livewireScripts
</head>
<body>
    <div id="app">

        <div class="scroll-top">
            <i class="fa-solid fa-arrow-up fa-bounce"></i>
        </div>

        @livewire('employee.new-employee')

        @include('components.employee.navbar')
        <main>
            <div class="container">
                <div class="content">
                    @yield('content')
                </div>
            </div>
        </main>
        <div class="footer mt-5">
            <div class="container mt-3 py-5">
                <div class="row">
                    <div class="col-12 col-lg-6 mb-4">
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
                    <div class="col-12 col-lg-6 mb-9 text-center text-lg-start">
                        <h5 class="text-muted">Employee Navigation Links</h5>
                        <div class="useful-links d-block d-md-flex mt-4 mt-md-0">
                            <div>
                                <ul class="list-unstyled">
                                    <li class="list-unstyled-item">
                                        <a href="{{route('employee.leave')}}">Apply Leave</a>
                                    </li>
                                    <li class="list-unstyled-item">
                                        <a href="{{route('employee.atro')}}">Apply Authority to render overtime</a>
                                    </li>
                                    <li class="list-unstyled-item">
                                        <a href="{{route('employee.obs.index')}}">Apply Official BusinessSlip</a>
                                    </li>
                                    <li class="list-unstyled-item">
                                        <a href="#">My Payslip</a>
                                    </li>
                                    <li class="list-unstyled-item">
                                        <a href="{{route('employee.messages')}}">Request Status</a>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <ul class="list-unstyled">
                                    <li class="list-unstyled-item">
                                        <a href="{{route('employee.team')}}">My Team</a>
                                    </li>
                                    <li class="list-unstyled-item">
                                        <a href="{{route('employee.directory')}}">My directory</a>
                                    </li>
                                    <li class="list-unstyled-item">
                                        <a href="{{route('employee.announcements.index')}}">Announcements</a>
                                    </li>
                                    <li class="list-unstyled-item">
                                        <a href="{{route('employee.profile')}}">My Profile</a>
                                    </li>
                                    <li class="list-unstyled-item">
                                        <a href="{{route('employee.tutorial')}}">Tutorial</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="ending text-center mb-0 text-muted mt-5">
                    &copy; 2025. All rights reserved Novulutions Inc.
                </p>
            </div>
        </div class="footer">
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @yield('script')
</body>
</html> 