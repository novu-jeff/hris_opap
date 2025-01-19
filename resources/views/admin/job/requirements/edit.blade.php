@extends('layouts.admin', [
    'title' => 'HRIS | Edit Requirements'
    ])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Edit Requirements</h1>
            <p>Edit requirements to be posted</p>
        </div>
        <div class="actions">
            <a href="{{route('job.requirements.index')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.job.requirements.edit', [
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