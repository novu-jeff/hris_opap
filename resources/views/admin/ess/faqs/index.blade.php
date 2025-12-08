@extends('layouts.admin', [
    'title' => $title
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>{{$header}}</h1>
            <p>{{$sub}}</p>
        </div>
        <div class="action">
            @if ($action === 'view')
                <div class="d-md-flex gap-3">
                    <a href="{{route('ess.faqs.create')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Add New</a>
                </div>
            @else
                <a href="{{route('ess.faqs.index')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
            @endif
        </div>
    </div>
    <div class="mt-3">
        @if ($action == 'view')
            @livewire('admin.ess.faqs.index')
        @else 
            @livewire('admin.ess.faqs.add', [
                'record_id' => $id ?? null
            ])
        @endif
    </div>
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