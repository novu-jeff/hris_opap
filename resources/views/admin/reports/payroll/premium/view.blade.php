@extends('layouts.admin', [
    'title' => 'HRIS | Payroll Record'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Premium Payroll Records</h1>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.reports.payroll.premium.view', [
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
.payroll-table-wrapper {
    max-height: 600px; /* Adjust the table height */
    overflow-y: auto;
    position: relative;
}

.table-header th {
    position: sticky;
    top: 50;
    z-index: 10;
}

.table-footer {
    position: sticky;
    bottom: 0;
    z-index: 10;
}

.table-footer td {
    background-color: #f8f9fa; /* Same as table-footer bg-light */
}


    /* Wrapper */
.payroll-table-wrapper {
    max-width: 100%;
    overflow: auto;
}

/* Table Base */
.payroll-table {
    font-size: 12px;
    white-space: nowrap;
    border-collapse: separate;
    border-spacing: 0;
}

/* Header Styling */
.main-header th {
    background: #2f3542;
    color: #fff;
    font-weight: 600;
    font-size: 12px;
}

.sub-header th {
    background: #57606f;
    color: #fff;
    font-size: 11px;
}

/* Sticky Columns */
.sticky-col {
    position: sticky;
    left: 0;
    background: #fff;
    z-index: 3;
    min-width: 180px;
}

.sticky-col-2 {
    position: sticky;
    left: 180px;
    background: #fff;
    z-index: 3;
    min-width: 150px;
}

/* Section Row */
.section-row td {
    background: #dfe4ea;
    font-weight: bold;
    letter-spacing: 1px;
}

/* Section Total */
.section-total td {
    background: #fff3cd;
    font-weight: bold;
}

/* Grand Total */
.grand-total td {
    background: #2f3542;
    color: #fff;
    font-weight: bold;
}

/* Hover Effect */
.payroll-table tbody tr:hover {
    background: #f1f2f6;
}

/* Numbers Alignment */
.payroll-table td.text-end {
    font-variant-numeric: tabular-nums;
}

/* Emphasis */
.text-success {
    color: #2ed573 !important;
}

.text-danger {
    color: #ff4757 !important;
}

.text-primary {
    color: #1e90ff !important;
}


</style>

