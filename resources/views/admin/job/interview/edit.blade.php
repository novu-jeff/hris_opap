@extends('layouts.admin', [
    'title' => 'HRIS | Edit Interview'
    ])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Edit Interview</h1>
            <p>Edit interview to be posted</p>
        </div>
        <div class="actions">
            <a href="{{route('job.interview.index')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.job.interview.edit', [
            'id' => $id
        ])
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