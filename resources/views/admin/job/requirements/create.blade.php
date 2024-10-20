@extends('layouts.admin', [
    'title' => 'Symphony | Add Requirements'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">
            <h1>Add New Requirements</h1>
            <p>Create new requirements to be posted</p>
        </div>
        <div class="actions">
            <a href="{{route('job.requirements.index')}}"class="btn btn-primary">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.job.requirements.create')
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