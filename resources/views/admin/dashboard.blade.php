@extends('layouts.admin', [
    'title' => 'HRIS | Dashboard'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
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
            new Swiper('.swiper-container', {
                slidesPerView: 3,
                spaceBetween: 20, 
                pagination: {
                    el: '.swiper-pagination',
                    clickable: false, 
                },
                freeMode: true, 
            });
        });
    </script>
@endsection