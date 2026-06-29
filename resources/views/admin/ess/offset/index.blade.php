@extends('layouts.admin', [
    'title' => 'HRIS | ESS Authority to Render Offsetting'
])

@section('content')
<div class="main-content flex-grow-1 p-4">

    <div class="container pb-5">

        <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">

            <div class="section-title">
                <h1>Authority to Render Offsetting</h1>
                <p>Manage all offset applications</p>
            </div>

        </div>

        <div class="mt-3">

            @livewire('admin.ess.offset.index', [
                'status' => $status ?? 'pending'
            ])

        </div>

    </div>

</div>
@endsection