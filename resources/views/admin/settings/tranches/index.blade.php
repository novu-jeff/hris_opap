@extends('layouts.admin', [
    'title' => 'HRIS | All Tranches'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Manage All Tranches</h1>
        </div>
        <div class="action">
            <div class="d-md-flex gap-3">
                <a href="{{route('tranches.create')}}" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Add New</a>
            </div>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.settings.tranches.index')
    </div>
</div>
</div>
@endsection

<style>
    .table-light td {
    font-weight: 600;
    }
    .salary-table thead th {
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 2;
}
    .salary-table th,
.salary-table td {
    padding: 10px 12px;
    border: none;
}

.salary-table thead th {
    font-size: 13px;
    letter-spacing: 0.5px;
}

.salary-row {
    border-bottom: 1px solid #f1f1f1;
    transition: background 0.2s ease;
}

.salary-row:hover {
    background: #f9fbfd;
}

.wtax-row td {
    font-size: 12px;
    color: #6c757d;
    padding-top: 0;
    padding-bottom: 10px;
}

.modal-content {
    background: #ffffff;
}

.modal-header {
    background: #fafafa;
}

/* Enable horizontal scroll container */
.table-responsive {
    overflow-x: auto;
    position: relative;
}

/* Freeze first column */
.sticky-first th:first-child,
.sticky-first td:first-child {
    position: sticky;
    left: 0;
    background: #fff;
    z-index: 3;
    border-right: 1px solid #eee;
}

/* Header should stay above body */
.sticky-first thead th {
    z-index: 4;
}

.table-responsive {
    border-radius: 8px;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
}

.table-responsive::-webkit-scrollbar {
    height: 6px;
}
.table-responsive::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

.salary-card {
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 12px;
    background: #fff;
}

.card-step {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 8px;
    text-align: center;
}

.card-step .label {
    font-size: 11px;
    color: #6c757d;
}

.card-step .value {
    font-weight: 600;
    font-size: 13px;
}

.card-step .wtax {
    font-size: 11px;
    color: #999;
}

/* Desktop (large screens) */
.custom-modal {
    width: 96%;
    max-width: 815px !important;
    margin: 1rem auto; /* reduces side gaps */
}

.modal-dialog.custom-modal {
    margin-left: auto;
    margin-right: auto;
}
/* Medium screens (tablet) */
@media (max-width: 992px) {
    .custom-modal {
        max-width: 95%;
    }
}

/* Mobile */
@media (min-width: 1400px) {
    .custom-modal {
        max-width: 1450px;
    }
}
@media (min-width: 1200px) {
    .custom-modal {
        width: 92%;
        max-width: 1400px;
    }
}

@media (max-width: 576px) {
    .custom-modal {
        margin: 0;
        max-width: 100%;
        height: 100%;
    }

    .custom-modal .modal-content {
        height: 100vh;
        border-radius: 0;
    }

    .custom-modal .modal-body {
        overflow-y: auto;
        padding: 15px;
    }

    .modal-header {
        padding: 12px 15px;
    }

    .modal-title {
        font-size: 14px;
    }

    .salary-table th,
    .salary-table td {
        font-size: 12px;
        padding: 6px;
    }

    .salary-table td {
        white-space: nowrap;
    }
}
 
</style>