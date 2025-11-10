<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="apple-touch-icon" sizes="180x180" href="{{$provider['favicon']}}/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{{$provider['favicon']}}/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{{$provider['favicon']}}/favicon-16x16.png">
    <link rel="manifest" href="{{$provider['favicon']}}/site.webmanifest">
    
    <title>{{ $title }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/sass/auth.scss'])
    @livewireStyles
    @livewireScripts
</head>
<body>

    <div id="auth-app">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
@yield('scripts')
</html>