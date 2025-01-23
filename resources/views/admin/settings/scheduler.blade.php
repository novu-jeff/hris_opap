@extends('layouts.admin', [
    'title' => 'Settings | Schedule Tasks'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Scheduler</h1>
            <p>Set or Modify Scheduled Tasks</p>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.scheduler')
    </div>
</div>
@endsection