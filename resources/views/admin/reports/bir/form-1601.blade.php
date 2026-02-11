@extends('layouts.admin', [
    'title' => 'HRIS | BIR Form 1601'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
    <div class="container pb-5">
        <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
            <div class="section-title">
                <h1>BIR Form 1601</h1>
                <p class="text-muted mb-0">Summary preview</p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Part I</h5>
                <pre class="bg-light p-3 rounded border mb-4">{{ json_encode($part1 ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

                <h5 class="mb-3">Part II</h5>
                <pre class="bg-light p-3 rounded border mb-0">{{ json_encode($part2 ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection
