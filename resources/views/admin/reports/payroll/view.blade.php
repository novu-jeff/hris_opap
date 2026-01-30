@extends('layouts.admin', [
    'title' => 'HRIS | Payroll Record'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Payroll Records</h1>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.reports.payroll.view', [
            'payrollId' => $payrollId
        ])
    </div>
</div>
</div>
@endsection

<style>
.table-responsive {
    overflow: visible !important; /* allow sticky to escape the scroll container */
}

tfoot tr.table-total-row {
    position: sticky;
    bottom: 0;
    background-color: #ffd966 !important; /* bright yellow */
    color: #000 !important;
    font-weight: bold;
    z-index: 5;
}

 .info-row {
        display: flex;
        /*justify-content: space-between;
        gap: 12px;*/
    }
    .info-label {
        min-width: 120px; /* adjust if needed */
        color: #555;
    }

    .info-label-3 {
        min-width: 180px; /* adjust if needed */
        color: #555;
    }
    .info-value {
        text-align: right;
        white-space: nowrap;
    }


</style>