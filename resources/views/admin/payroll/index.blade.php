@extends('layouts.admin', [
    'title' => 'HRIS | All Payroll'
])

@section('content')
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

        @if(isset($options[$employment_type]))
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
        @endif


        <div class="d-md-flex justify-content-end gap-3">
            <div class="dropdown">
                <button class="btn btn-primary text-uppercase px-5 py-3 fw-medium dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Generate Payroll
                </button>
                <ul class="dropdown-menu dropdown-menu-lg-start mt-2 text-uppercase">
                    <li><a class="dropdown-item fw-bold text-muted" style="font-size: 13px" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#newSalaryPayroll">Salary</a></li>
                    <li><a class="dropdown-item fw-bold text-muted" style="font-size: 13px" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#newOtherPayroll">Mid Year</a></li>
                    <li><a class="dropdown-item fw-bold text-muted" style="font-size: 13px" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#newOtherPayroll">Year End</a></li>
                    <li><a class="dropdown-item fw-bold text-muted" style="font-size: 13px" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#newOtherPayroll">RATA</a></li>
                    <li><a class="dropdown-item fw-bold text-muted" style="font-size: 13px" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#newOtherPayroll">EME</a></li>
                    <li><a class="dropdown-item fw-bold text-muted" style="font-size: 13px" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#newOtherPayroll">CTO</a></li>
                </ul>
            </div>              
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.payroll.index', ['employment_type' => $employment_type, 'type' => $actions])
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