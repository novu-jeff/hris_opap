<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e($provider['favicon']); ?>/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e($provider['favicon']); ?>/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e($provider['favicon']); ?>/favicon-16x16.png">
    <link rel="manifest" href="<?php echo e($provider['favicon']); ?>/site.webmanifest">

    <title><?php echo e($title); ?></title>

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
    <?php echo app('Illuminate\Foundation\Vite')(['resources/sass/app.scss', 'resources/js/app.js', 'resources/sass/home-layout.scss']); ?>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</head>
<body>
    
    <div class="scroll-top">
        <i class="fa-solid fa-arrow-up fa-bounce"></i>
    </div>

    <div id="app">
        <?php echo $__env->make('components.home.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <main>
            <div class="content">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>
        <div class="footer mt-5">
            <div class="container mt-3 py-5">
                <div class="row">
                    <div class="col-12 text-center">

                        <div class="logo">
                            <img src="<?php echo e(asset('/img/' . $provider['client_logo'])); ?>">
                            <img src="<?php echo e(asset('/img/' . $provider['logo'])); ?>">
                        </div>

                        <div class="logo-phrase mt-3">
                            <p><?php echo e($provider['tagline']); ?></p>
                        </div>

                        <hr class="mt-3 mb-2 mx-auto" style="width:150px;">

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

                        <p class="ending text-center mb-0 text-muted mt-5">
                            &copy; 2025. Powered by <?php echo e($provider['company']); ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php echo $__env->yieldContent('script'); ?>
</body>
</html> <?php /**PATH /var/www/html/oppapru_hris/resources/views/layouts/app.blade.php ENDPATH**/ ?>