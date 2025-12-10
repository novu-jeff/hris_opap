@extends('layouts.admin', [
    'title' => 'HRIS | All Payroll'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Payroll</h1>
        </div>
    </div>
    <div class="action">
        <ul class="nav nav-pills mb-3" id="employment-type-tab" role="tablist">
            <li class="nav-item d-flex text-uppercase fw-bold" role="presentation">
                @foreach($options as $key => $employment)
                    <a href="{{ route('payroll.index', ['type' => $type, 'employment_type' => $key]) }}"
                    class="nav-link border-2 border-primary {{ $employment_type === $key ? 'active' : '' }}"
                    role="tab" aria-selected="{{ $employment_type === $key ? 'true' : 'false' }}">
                        {{ $employment['name'] }}
                    </a>
                @endforeach
            </li>
        </ul>

       {{-- @if(isset($options[$employment_type]))
            <ul class="nav nav-pills mb-3" id="action-type-tab" role="tablist">
                <li class="nav-item d-flex text-uppercase fw-bold" role="presentation">
                    @foreach($options[$employment_type]['sub'] as $subKey => $subName)
                        <a href="{{ route('payroll.index', ['type' => $subKey, 'employment_type' => $employment_type]) }}"
                        class="nav-link {{ $type === $subKey ? 'active' : '' }}"
                        role="tab" aria-selected="{{ $type === $subKey ? 'true' : 'false' }}">
                            {{ $subName }}
                        </a>
                    @endforeach
                </li>
            </ul>
        @endif--}}
    </div>
    <div class="mt-3">
        @livewire('admin.payroll.index', ['employment_type' => $employment_type, 'type' => $type])
    </div>
</div>
</div>
<style>
    .nav-pills:nth-child(2) .nav-link.active, .nav-pills .show>.nav-link {
        color: #225F8B;
        background-color: transparent;
        border: 2px solid #225F8B;
    }
    .modal .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
        background-color: #225F8B !important;
        color: #fff !important;
    }
</style>
@endsection