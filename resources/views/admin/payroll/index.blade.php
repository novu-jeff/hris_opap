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
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item d-flex text-uppercase fw-bold" role="presentation">
                <a href="{{route('payroll.index', ['type' => 'salary'])}}" class="nav-link {{$type == 'salary' ? 'active' : ''}}" role="tab" aria-selected="true">Salary</a>
                <a href="{{route('payroll.index', ['type' => 'mid_year'])}}" class="nav-link {{$type == 'mid_year' ? 'active' : ''}}" role="tab" aria-selected="true">Mid Year </a>
                <a href="{{route('payroll.index', ['type' => '13th_month'])}}" class="nav-link {{$type == '13th_month' ? 'active' : ''}}" role="tab" aria-selected="true">13th Month</a>
                <a href="{{route('payroll.index', ['type' => 'cto'])}}" class="nav-link {{$type == 'cto' ? 'active' : ''}}" role="tab" aria-selected="true">CTO</a>
            </li>
          </ul>
        <div class="d-md-flex justify-content-end gap-3">
            <div class="dropdown">
                <button class="btn btn-primary text-uppercase px-5 py-3 fw-medium dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Generate Payroll
                </button>
                <ul class="dropdown-menu dropdown-menu-lg-start mt-2 text-uppercase">
                  <li><a class="dropdown-item fw-bold text-muted" style="font-size: 13px" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#newSalaryPayroll">Salary Payroll</a></li>
                  <li><a class="dropdown-item fw-bold text-muted" style="font-size: 13px" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#newOtherPayroll">Mid Year Payroll</a></li>
                  <li><a class="dropdown-item fw-bold text-muted" style="font-size: 13px" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#newOtherPayroll">13th Month Payroll</a></li>
                  <li><a class="dropdown-item fw-bold text-muted" style="font-size: 13px" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#newOtherPayroll">CTO Payroll</a></li>
                </ul>
            </div>              
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.payroll.index', ['type' => $type])
    </div>
</div>
@endsection