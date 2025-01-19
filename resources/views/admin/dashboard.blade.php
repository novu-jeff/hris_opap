@extends('layouts.admin', [
    'title' => 'HRIS | Dashboard'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Dashboard</h1>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.dashboard.index')
    </div>
</div>
@endsection

@section('script')
    <script>
        $(function() {
            var swiperOptions = {
            slidesPerView: 3,
            spaceBetween: 20, 
            pagination: {
                el: '.swiper-pagination',
                clickable: false, 
            },
            freeMode: true, 
            };

            function updateSwiperOptions() {
                if (window.matchMedia("(min-width: 0px) and (max-width: 992px)").matches) {
                    swiperOptions.slidesPerView = 1;
                } else {
                    swiperOptions.slidesPerView = 3;
                }
                new Swiper('.swiper-container', swiperOptions);
            }

            updateSwiperOptions();

            $(window).resize(function() {
            updateSwiperOptions();
            });
        });
    </script>
@endsection