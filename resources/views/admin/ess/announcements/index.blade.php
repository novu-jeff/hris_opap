@extends('layouts.admin', [
    'title' => $title
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>{{$header}}</h1>
            <p>{{$sub}}</p>
        </div>
        <div class="action">
            @if ($action === 'view')
                <div class="d-md-flex gap-3">
                    <a href="{{route('ess.announcements.create')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Add New</a>
                </div>
            @else
                <a href="{{route('ess.announcements.index')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
            @endif
        </div>
    </div>
    <div class="mt-3">
        @if ($action == 'view')
            @livewire('admin.ess.announcements.index')
        @else 
            @livewire('admin.ess.announcements.add', [
                'record_id' => $id ?? null
            ])
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
    $(function() {

        ckeditor();

    });
</script>
@endsection