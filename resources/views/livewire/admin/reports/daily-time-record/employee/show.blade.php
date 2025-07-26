@section('style')
<style>

    td {
        position: relative;
    }

    .underline {
        min-width: 300px;
        width: fit-content;
        border-bottom: 1px solid black;
        padding: 0 10px 0 20px;
        display: inline-flex;
        align-items: end;
    }

    .print-container {
        display: flex;
        justify-content: center;
        width: 100%;
        gap: 20px;
        padding: 0 80px 0 80px;
    }

    .print-container .dtr:nth-of-type(2) {
        display: none;
    }

    .dtr {
        width: 800px;
        margin: 10px 0 50px 0;
        padding: 10mm 5mm;
        box-sizing: border-box;
        border: 1px solid rgb(178, 178, 178);
        background-color: #fff;
        border-radius: 12px;
        position: relative;
    }

    .loading-screen {
        position: absolute;
        height: 100%;
        width: 100%;
        z-index: 2;
        left: 8px;
        top: 8px;
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
        font-size: 12px !important;
    }

    .dtr-table th, .dtr-table td {
        border: 1px solid black;
        text-align: center;
        padding: 5px;
        font-size: 12px; 
    }

    .dtr-summary {
        margin-top: 20px;
        font-size: 12px;
    }
    .dtr-summary td {
        text-align: left
    }

    .dtr-summary .signature {
        margin-top: 40px;
        text-align: center;
        font-size: 12px;
    }

    .signature h5 {
        text-transform: uppercase;
        font-weight: bold;
    }

    .shaded-box {
        position: absolute;
        top: 0;
        right: 0;
        padding: 5px;
    }
    .remarks {
        margin-top: 20px;
        font-size: 12px;
    }


    .dtr-summary {
        text-align: center;
        margin-top: 20px;
    }

    .dtr-summary h5 {
        text-transform: uppercase;
        font-weight: bold;
        margin: 30px 0 30px 0;
    }

    .dtr-summary-container {
        width: 90%;
        margin: auto;
        display: grid;
        grid-template-columns: repeat(3, minmax(200px, 1fr));
        text-align: left;
    }
    .dtr-summary-item {
        font-size: 13px;
        font-weight: 600 ;
        margin-bottom: 0px !important;
        text-transform: uppercase;
        color: #000000c5;
    }

    .signature {
        margin-top: 50px;
        text-align: center;
    }

    .sepe {
        width: 90%;
        height: 1px;
        background: #000;
        margin: 10px auto;
    }
    .certify {
        width: 90%;
        margin: auto;
        text-align: center
    }

    .remarks {
        margin-left: 40px;
    }

    .btn-correction {
        position: absolute;
        right: 0px;
        top: 50%;
        transform: translate(160px, -50%);
        display: flex;
        align-items: center;
    }


</style>
@endsection
<div class="mahcon">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
            <div class="section-title">
                <h1>Daily Time Record <span  class="text-primary">{{ $employee_no }}</span></h1>
            </div>
            <div class="action">
                <div class="d-md-flex gap-3">
                    <a href="{{route('reports.dtr')}}" class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
                </div>
            </div>
        </div>
        <div class="mt-3">
            @if($logs)
                <div class="py-3 d-flex justify-content-between gap-3 align-items-center">
                    <div class="d-flex gap-3">
                        <div class="d-flex align-items-center">
                            <button 
                                class="btn btn-sm btn-outline-primary" 
                                wire:click="changeMonth('control', '-1')"
                                wire:loading.attr="disabled" 
                                wire:loading.class="btn-secondary">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            
                            <div class="mx-3" id="monthYear">
                                {{ \Carbon\Carbon::parse($dtrDate)->format('F, Y') }}
                            </div>
                            
                            <button 
                                class="btn btn-sm btn-outline-primary" 
                                wire:click="changeMonth('control', '1')"
                                wire:loading.attr="disabled" 
                                wire:loading.class="btn-secondary"
                                @disabled($dtrDate == now()->format('F, Y'))>
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                        <div>
                            <input type="month" wire:change="changeMonth('date')" wire:model="monthDate" class="form-control">
                        </div>
                    </div>
                    <button class="btn btn-success save-as-pdf"><i class="fa-solid fa-print"></i></button>
                </div>
            @endif
        </div>
    </div>
    <div class="container">
        @if($logs)
            @if(!$hasLeaveCard && config('app.product') === 'government')
                <div class="warning mt-5 mb-3">
                    <div class="alert alert-info fw-bold text-center" role="alert">
                        <p class="m-0 text-uppercase">No Leave Card Detected</p>
                        <small class="text-uppercase" style="font-size: 12px;">
                            <a href="{{route('leave.show', ['leave' => 1, 'employee' => $employee_no])}}">Click here to add</a>
                        </small>
                    </div>
                </div>
            @endif
            <div class="print-container mt-4">
                 @php
                    $isAdmin = false;
                @endphp
                @include('livewire.admin.reports.daily-time-record.employee.dtr-table')
                @include('livewire.admin.reports.daily-time-record.employee.dtr-table')
            </div>
        @else
            <div class="alert alert-danger" role="alert">
                @if (!empty($errors))
                    <ul class="m-0">
                        @foreach ($errors as $error)
                            <li class="text-uppercase">{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
    </div>    
</div>