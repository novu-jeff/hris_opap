@extends('layouts.employee', [
    'title' => 'ESS | Dashboard'
])

@section('content')
<div class="container pb-5">
    <div class="mt-5 d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Dashboard</h1>
            <p>Track and monitor your employment records.</p>
        </div>
    </div>
    @livewire('employee.dashboard');
</div>
@endsection

@section('script')
    <script>
        $(function() {
            // Initialize Swiper.js
            new Swiper('.swiper-container', {
                slidesPerView: 4, // Number of visible slides
                spaceBetween: 20, // Space between slides in pixels
                navigation: {
                    nextEl: '.swiper-button-next', // Selector for the "next" button
                    prevEl: '.swiper-button-prev', // Selector for the "previous" button
                },
                pagination: {
                    el: '.swiper-pagination', // Selector for pagination bullets
                    clickable: true, // Allow users to click on bullets to navigate
                },
                freeMode: true, // Enable free scrolling without snapping
            });
        });
    </script>
@endsection