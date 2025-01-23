<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0">
    <title>Print Download</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/sass/home-layout.scss'])
</head>
<body>
    <div class="content">
        @yield('content')
    </div>
    @yield('script')
</body>
</html>