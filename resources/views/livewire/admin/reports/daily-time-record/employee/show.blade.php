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
        display: block;
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

    .dtr-copy img {
    position: static !important;
    display: block;
    margin: 0 auto 10px auto;
    height: 70px !important;
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
        font-size: 11px !important;
    }

    .dtr-table th, .dtr-table td {
        border: 1px solid black;
        text-align: center;
        padding: 4px;
        font-size: 11px; 
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

/* --- FIX Bootstrap container blocking two-column print --- */
#print-section .container {
    max-width: 100% !important;
    width: 100% !important;
    padding: 0 !important;
}

/* --- Fix two copies width --- */


   .print-wrapper {
    display: flex;
    width: 100%;
    justify-content: space-between;
    gap: 0;
    padding: 10px;
    flex-wrap: nowrap;
}

.dtr-copy {
    width: 48%;
    max-width: 100%;
    padding: 5px;
}
    .center {
        text-align: center;
        font-weight: bold;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    .p-dtr-table, .p-dtr-table th, .p-dtr-table td {
        border: 1px solid #000;
        font-size: 12px;
        padding: 0px;
        text-align: center;
    }
    .info-table td {
        border: none;
        padding: 3px;
        text-align: left;
    }
    .certify {  
        font-size: 13px;
        margin-top: 15px;
        text-align: justify;
    }

    @media print {
    .print-wrapper {
        padding: 0;
        gap: 0;
    }

    .dtr-copy {
        page-break-inside: avoid;
    }

    .td-small {
        width: 10px;
    }


}

.print-area-hidden {
    visibility: hidden;
    position: absolute;
    top: -9999px;
    left: -9999px;
}
@media print {
    body * {
        visibility: hidden !important;
    }

    #print-section, #print-section * {
        visibility: visible !important;
    }

    #print-section {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
    }
}

@media print {
    .p-dtr-table th,
    .p-dtr-table td {
        border-right: 1px solid #000 !important;
        border-left: 1px solid #000 !important;
        border-top: 1px solid #000 !important;
        border-bottom: 1px solid #000 !important;
    }

     /* force two equal columns */
    .dtr-copy {
        width: 48% !important;
        display: inline-block !important;
        vertical-align: top !important;
        page-break-inside: avoid !important;
    }

    /* keep remarks column visible & fixed width */
    th.remarks-col,
    td.remarks-col {
        min-width: 70px !important;
        max-width: 70px !important;
        width: 70px !important;
        display: table-cell !important;
        visibility: visible !important;
        white-space: normal !important;
    }

    /* prevent bootstrap and flex from squeezing tables */
    .p-dtr-table {
        table-layout: fixed !important;
    }

    /* force page to scale the content instead of cutting it */
    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        transform: scale(0.83);        /* ← adjust until everything fits */
        transform-origin: top left;
    }

    /* wrapper resets so scaling works full width */
    .print-wrapper {
        width: 100% !important;
        display: flex !important;
        justify-content: space-between !important;
        gap: 0 !important;
        padding: 0 !important;
    }

    /* each DTR copy */
    .dtr-copy {
        width: 49% !important;             /* slightly wider than before */
        min-width: 49% !important;
        max-width: 49% !important;
        page-break-inside: avoid !important;
    }

    /* force remarks column size */
    .remarks-col {
        width: 70px !important;
        min-width: 70px !important;
        max-width: 70px !important;
        white-space: normal !important;
    }

    /* prevent table from collapsing */
    .p-dtr-table {
        table-layout: fixed !important;
        border-collapse: collapse !important;
    }

    .p-dtr-table th,
    .p-dtr-table td {
        padding: 2px !important;
        font-size: 11px !important;
    }

   
}


</style>
@endsection
<div class="mahcon">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
            <div class="section-title">
                <h1>Daily Time Record <span  class="text-primary">{{ $employee_no }}</span></h1>
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
                   
                    <div class="d-flex justify-content-end mt-3">
                        <!--<button type="button" class="btn btn-secondary" onclick="printDTR()">
                            🖨️ Print DTR
                        </button>-->
                        <button class="btn btn-primary save-as-pdf ms-2">
       🖨️ Print DTR
    </button>
                    </div>
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
            <div class="print-container mt-4 ssss">
                 @php
                    $isAdmin = false;
                @endphp
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
      <div id="print-section" class="print-wrapper print-area-hidden">
         @if($logs)
        @include('livewire.admin.reports.daily-time-record.employee.print-dtr-table')
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

<script>
function printDTR() {
    
    window.print();

   
}
</script>
