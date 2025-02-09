@extends('layouts.admin', [
    'title' => 'HRIS | Leave Card'
])

@if($action == 'view-card')
    @section('content')
        <div class="container pb-5">
            <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
                <div class="section-title">
                    <h1>Leave Card</h1>
                </div>
                <div class="actions">
                    @if($action)
                        <a href="{{route('leave.show', ['leave' => $id])}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
                    @else
                        <a href="{{route('leave.index')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
                    @endif
                </div>
            </div>
            <div class="mt-3">
                @livewire('admin.settings.hris.leave.view-card', [
                    'id' => $id,
                    'employee_no' => $employee,
                    'action' => $action
                ])
            </div>
        </div>
    @endsection
@else
    @section('content')
        <div class="container pb-5">
            <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
                <div class="section-title">
                    <h1>Add Credits</h1>
                </div>
                <div class="actions">
                    <a href="{{route('leave.index')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
                </div>
            </div>
            <div class="mt-3">
                @livewire('admin.settings.hris.leave.show', [
                    'id' => $id
                ])
            </div>
        </div>
    @endsection
@endif