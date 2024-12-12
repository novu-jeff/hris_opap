@extends('layouts.admin', [
    'title' => 'HRIS | All Shift Schedules'
])
@section('style')
<style>
    /* Add any custom styling here */
    .underline {
        text-decoration: underline;
    }
    .dtr {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        padding: 10mm 5mm;
        box-sizing: border-box;
        border: 1px solid rgb(178, 178, 178);
        background-color: #fdffe4;
        border-radius: 12px
    }

    .dtr-header {
        position: relative;
        text-align: center;
        margin-bottom: 20px;
    }

    .dtr-header img {
        position: absolute;
        top: -10px;
        left: 30px;
        height: 70px;
    }

    @media(max-width: 993px ) {
        .dtr-header img {
            left: 0;
        }
    }

    .dtr-header h1 {
        font-size: 14px;
        margin: 5px 0;
    }

    .dtr-info {
        margin-bottom: 20px;
        font-size: 14px;
    }

    .dtr-info div {
        margin-bottom: 5px;
    }

    .dtr-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .dtr-table th, .dtr-table td {
        border: 1px solid black;
        text-align: center;
        padding: 5px;
    }

    .dtr-summary {
        margin-top: 20px;
        font-size: 12px;
    }
    .dtr-summary td {
        text-align: left
    }

    .dtr-summary .signature {
        margin-top: 20px;
        text-align: center;
    }

    .remarks {
        margin-top: 20px;
        font-size: 12px;
    }
</style>
@endsection
@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Daily Time Record > <span  class="text-primary">{{ $date }}</span></h1>
        </div>
        <div class="actions">
            <a href="{{ route('reports.dtr') }}" class="btn btn-outline-danger text-uppercase px-5 py-3 fw-medium">Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.reports.daily-time-record.employee.index', ['date' => $date])
    </div>
</div>
@endsection