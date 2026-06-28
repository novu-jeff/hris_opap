<div class="inner-content">

    @php
        [$start, $end] = explode(' to ', $payslip['payroll']['cut_off_period']);

        $startDate = \Carbon\Carbon::parse($start);

        $fullMonthCutoff =
            $startDate->copy()->startOfMonth()->format('F j')
            .' - '.
            $startDate->copy()->endOfMonth()->format('F j, Y');

        $earnings = [
            'Monthly Basic Salary' => $payslip['basic_salary'],
        ];

        if($payslip['employment_type_id'] == 1){
            $earnings['Personnel Economic Relief Allowance'] = $payslip['pera'];
            $earnings['Gross Amount Earned'] = $payslip['gross_amount_earned'];
        }

      

        $deductions = [
            'GSIS Contribution' => $payslip['rlip'],
            'PAG-IBIG Contribution' => $payslip['hdmf'],
            'PhilHealth Contribution' => $payslip['philhealth'],
            'GSIS Conso Loan' => $payslip['consoloan'],
            'GSIS Emergency Loan' => $payslip['emergency_loan'],
            'GSIS PLREG' => $payslip['plreg'],
        ];

        if($payslip['employment_type_id']==1){
            $deductions['GSIS MPL'] = $payslip['mpl'];
        }else{
            $deductions['MPL'] = $payslip['mpl'];
        }

        $deductions += [
            'GSIS MPL Lite' => $payslip['mpl_lite'],
            'GSIS CPL' => $payslip['cpl'],
            'GSIS GSEL' => $payslip['gsel'],
            'GSIS GBEL' => $payslip['gbel'],
            'MP2' => $payslip['mp2'],
        ];

        if($payslip['employment_type_id']==1){
            $deductions['MPL STLMS'] = $payslip['mplstlms'];
        }

        $deductions += [
            'Cir375-ECQ' => $payslip['cir375_cir449'],
            'BIR Withholding Tax' => $payslip['w_tax'],
            'UCA' => $payslip['uca'],
            'Disallowance' => $payslip['disallowance'],
            'Late / UT / Absences' => $payslip['aut'],
            'Overpayment' => $payslip['overpayment'],
            'Tax 3%' => $payslip['tax_3'],
            'Tax 5%' => $payslip['tax_5'],
            'Tax 8%' => $payslip['tax_8'],
            'Tax 10%' => $payslip['tax_10'],
        ];
    @endphp

    {{-- HEADER --}}
    <div class="ps-header">

        <div class="ps-logo">
            <img src="{{ asset('/img/'.$provider['client_logo']) }}">
        </div>

        <div class="ps-title">
            <h2>OFFICE OF THE PRESIDENTIAL ADVISER</h2>
            <h2>ON PEACE, RECONCILIATION AND UNITY</h2>

            <div class="subtitle">
                PAYROLL PAYMENT SLIP
            </div>
        </div>

    </div>

    {{-- EMPLOYEE INFORMATION --}}
    <table class="table table-bordered employee-table">

        <tr>

            <th width="15%">Employee No.</th>
            <td width="35%">
                {{ $payslip['employee_no'] }}
            </td>

            <th width="15%">Employee Name</th>
            <td width="35%">
                {{ $payslip['name'] }}
            </td>

        </tr>

        <tr>

            <th>Position</th>
            <td>{{ $payslip['position'] }}</td>

            <th>Unit</th>
            <td>{{ $payslip['information']['section']['name'] }}</td>

        </tr>

        <tr>

            <th>Payroll Date</th>

            <td>
                {{ \Carbon\Carbon::parse($payslip['payroll']['payroll_date'])->format('F d, Y') }}
            </td>

            <th>Cut-off Period</th>

            <td>

                {{ $fullMonthCutoff }}

            </td>

        </tr>

    </table>

    {{-- EARNINGS / DEDUCTIONS --}}
    <div class="row g-3 mt-3">

        {{-- EARNINGS --}}
        <div class="col-md-6">

            <table class="table table-bordered payroll-table">

                <thead>

                    <tr>

                        <th colspan="2">
                            EARNINGS
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($earnings as $title=>$amount)

                        <tr>

                            <td>

                                {{ $title }}

                            </td>

                            <td class="text-end">

                                {{ number_format($amount,2) }}

                            </td>

                        </tr>

                    @endforeach

                    @for($i=count($earnings);$i<18;$i++)

                        <tr>

                            <td>&nbsp;</td>

                            <td></td>

                        </tr>

                    @endfor

                    <tr class="table-total">

                        <td>

                            TOTAL EARNINGS

                        </td>

                        <td class="text-end">

                            {{ number_format($payslip['gross_amount_earned'],2) }}

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        {{-- DEDUCTIONS --}}
        <div class="col-md-6">

            <table class="table table-bordered payroll-table">

                <thead>

                    <tr>

                        <th colspan="2">

                            DEDUCTIONS

                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($deductions as $title=>$amount)

                        <tr>

                            <td>

                                {{ $title }}

                            </td>

                            <td class="text-end">

                                {{ number_format($amount,2) }}

                            </td>

                        </tr>

                    @endforeach

                    {{-- Dynamic Loan Deductions --}}

                    @foreach($payslip->deductions->where('reference_type','loan') as $loan)

                        <tr>

                            <td>

                                {{ $loan->loan->loanType->name ?? 'Loan Deduction' }}

                            </td>

                            <td class="text-end">

                                {{ number_format($loan->amount,2) }}

                            </td>

                        </tr>

                        @endforeach

                        @php
                            $loanTotal = $payslip->deductions
                                ->where('reference_type', 'loan')
                                ->sum('amount');
                        @endphp
    
                        @for(
                            $i = count($deductions) + $payslip->deductions->where('reference_type','loan')->count();
                            $i < 18;
                            $i++
                        )
    
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                            </tr>
    
                        @endfor
    
                        <tr class="table-total">
    
                            <td>
                                TOTAL DEDUCTIONS
                            </td>
    
                            <td class="text-end fw-bold">
                                {{ number_format($payslip['total_deductions'],2) }}
                            </td>
    
                        </tr>
    
                    </tbody>
    
                </table>
    
            </div>
    
        </div>
    
        {{-- NET PAY SUMMARY --}}
    
        <div class="netpay-card mt-4">
    
            <div class="netpay-title">
    
                NET PAY SUMMARY
    
            </div>
    
            <table class="table table-bordered mb-0">
    
                <thead>
    
                    <tr>
    
                        <th>{{ $payslip['employment_type_id'] == 1 ? 'Gross Earnings' : 'Total Earnings' }}</th>
    
                        <th>Total Deductions</th>
    
                        <th>Net Amount</th>
    
                        <th>Amount Due (15)</th>
    
                        <th>Amount Due (30)</th>
    
                    </tr>
    
                </thead>
    
                <tbody>
    
                    <tr>
    
                        <td class="text-end">
                            {{ number_format(
                                $payslip['employment_type_id'] == 1
                                    ? $payslip['gross_amount_earned']
                                    : $payslip['basic_salary'],
                                2
                            ) }}
    
                        </td>
    
                        <td class="text-end">
    
                            {{ number_format($payslip['total_deductions'],2) }}
    
                        </td>
    
                        <td class="text-end fw-bold text-success">
    
                            {{ number_format($payslip['net_amount'],2) }}
    
                        </td>
    
                        <td class="text-end">
    
                            {{ number_format($payslip['net_first_half'],2) }}
    
                        </td>
    
                        <td class="text-end">
    
                            {{ number_format($payslip['net_second_half'],2) }}
    
                        </td>
    
                    </tr>
    
                </tbody>
    
            </table>
    
        </div>
    
        {{-- FOOTER --}}
    
        <div class="row mt-5 align-items-end">
    
            <div class="col-6">
    
                <div class="signature-title">
    
                    Prepared / Approved By
    
                </div>
    
                <div class="signature-name">
    
                    MARIESER T. ALMELOR
    
                </div>
    
                <div class="signature-position">
    
                    Chief Administrative Officer
    
                </div>
    
            </div>
    
            <div class="col-6 text-end">
    
                <table class="table table-borderless table-sm footer-table">
    
                    <tr>
    
                        <td width="45%">
                            Generated On
                        </td>
    
                        <td>
                            {{ now()->format('F d, Y h:i A') }}
                        </td>
    
                    </tr>
    
                    <tr>
    
                        <td>
                            Payroll Period
                        </td>
    
                        <td>
    
                            {{ $fullMonthCutoff }}
    
                        </td>
    
                    </tr>
    
                </table>
    
            </div>
    
        </div>
    
    </div>
    <div class="payslip-watermark">

        CONFIDENTIAL
    
    </div>
 <style>   

    /* ===============================
   PAGE
================================ */

@page{
    size:A4 landscape;
    margin:10mm;
}

body{
    margin:0;
    padding:0;
    font-family:Arial,Helvetica,sans-serif;
    color:#333;
    font-size:11px;
    background:#f4f6f9;
}

/* ===============================
   WRAPPER
================================ */

.payslip-wrapper{
    width:100%;
    max-width:1400px;
    margin:30px auto;
    padding:20px;
}

.payslip-container{
    width:100%;
    /*min-width:1200px;*/
    background:#fff;
    border:1px solid #d6d6d6;
    border-radius:8px;
    padding:25px;
    position:relative;
}

/* ===============================
   HEADER
================================ */

.ps-header{

    display:flex;

    justify-content:center;

    align-items:center;

    gap:25px;

    width:100%;

}

.ps-logo{

    width:110px;

    text-align:center;

}

.ps-logo img{

    width:85px;

}

.ps-title{

    flex:1;

    text-align:center;

}

.ps-title h2{

    margin:0;

    color:#0F4C81;

    font-size:18px;

    font-weight:bold;

}

.subtitle{

    margin-top:6px;

    font-size:15px;

    font-weight:700;

    letter-spacing:1px;

}

/* ===============================
   EMPLOYEE TABLE
================================ */

.employee-table{

    width:100%;

    table-layout:fixed;

    border-collapse:collapse;

    margin-bottom:20px;

}

.employee-table th{

    background:#F2F6FA;

    color:#0F4C81;

    font-weight:700;

    width:15%;

    vertical-align:middle;

}

.employee-table td{

    background:#fff;

}

.employee-table th,
.employee-table td{

    border:1px solid #d8d8d8;

    padding:8px;

}

/* ===============================
   PAYROLL TABLES
================================ */

.payroll-table{

    width:100%;

    border-collapse:collapse;

    font-size:10px;

}

.payroll-table thead th{

    background:#0F4C81;

    color:#fff;

    text-align:center;

    padding:10px;

    font-size:12px;

}

.payroll-table td{

    padding:6px 8px;

    border:1px solid #ececec;

}

.payroll-table tr:nth-child(even){

    background:#fafafa;

}

.table-total{

    background:#F3F6F9;

    font-weight:bold;

}

.table-total td{

    border-top:2px solid #0F4C81;

}

/* ===============================
   NET PAY
================================ */

.netpay-card{

    margin-top:25px;

    border:2px solid #198754;

    border-radius:6px;

    overflow:hidden;

}

.netpay-title{

    background:#198754;

    color:#fff;

    text-align:center;

    font-weight:bold;

    padding:10px;

    font-size:14px;

}

.netpay-card table{

    width:100%;

    margin:0;

}

.netpay-card th{

    background:#EAF7EA;

    text-align:center;

    padding:8px;

}

.netpay-card td{

    text-align:right;

    padding:10px;

    font-size:12px;

}

.text-success{

    color:#198754 !important;

    font-size:14px;

}

/* ===============================
   FOOTER
================================ */

.signature-title{

    font-size:11px;

    margin-bottom:35px;

}

.signature-name{

    font-weight:bold;

    text-decoration:underline;

    font-size:12px;

}

.signature-position{

    font-size:11px;

}

.footer-table td{

    padding:2px 6px;

    font-size:10px;

}

.payslip-container.blur{

    filter:blur(18px);

    transition:.2s;

}

/* ===============================
   WATERMARK
================================ */

.payslip-watermark{

    position:absolute;

    top:50%;

    left:50%;

    transform:translate(-50%,-50%) rotate(-30deg);

    font-size:120px;

    font-weight:900;

    color:rgba(255,0,0,.05);

    white-space:nowrap;

    pointer-events:none;

    z-index:1;

}

/* ===============================
   SECURITY
================================ */

.payslip-overlay{

    position:absolute;

    inset:0;

    pointer-events:none;

    background:repeating-linear-gradient(

        45deg,

        rgba(255,255,255,.02),

        rgba(255,255,255,.02) 3px,

        transparent 3px,

        transparent 8px

    );

}

/* ===============================
   UTILITIES
================================ */

.text-end{

    text-align:right;

}

.text-center{

    text-align:center;

}

.fw-bold{

    font-weight:bold;

}

/* ===============================
   PRINT
================================ */

@media print{

    body{

        background:#fff;

    }

}

</style>

<script>

    document.addEventListener('DOMContentLoaded', () => {
    
        const payslip = document.getElementById('payslipProtected');
    
        if (!payslip) return;
    
        const blur = () => payslip.classList.add('blur');
        const clearBlur = () => payslip.classList.remove('blur');
    
        /* ==========================
           Disable Right Click
        ========================== */
    
        document.addEventListener('contextmenu', e => {
            e.preventDefault();
        });
    
        /* ==========================
           Disable Text Selection
        ========================== */
    
        document.addEventListener('selectstart', e => {
            e.preventDefault();
        });
    
        /* ==========================
           Disable Copy
        ========================== */
    
        document.addEventListener('copy', e => {
            e.preventDefault();
        });
    
        /* ==========================
           Disable Cut
        ========================== */
    
        document.addEventListener('cut', e => {
            e.preventDefault();
        });
    
        /* ==========================
           Disable Drag
        ========================== */
    
        document.addEventListener('dragstart', e => {
            e.preventDefault();
        });
    
        /* ==========================
           Disable Ctrl + P
        ========================== */
    
        document.addEventListener('keydown', e => {
    
            const key = e.key.toLowerCase();
    
            if ((e.ctrlKey || e.metaKey) && key === 'p') {
    
                e.preventDefault();
    
                alert('Printing has been disabled.');
    
            }
    
        });
    
        /* ==========================
           Blur on Print Screen
        ========================== */
    
        document.addEventListener('keyup', e => {
    
            if (e.key === 'PrintScreen') {
    
                blur();
    
                navigator.clipboard.writeText('');
    
                setTimeout(clearBlur,1500);
    
            }
    
        });
    
        /* ==========================
           Blur when tab inactive
        ========================== */
    
        document.addEventListener('visibilitychange', () => {
    
            if(document.hidden){
    
                blur();
    
            }else{
    
                clearBlur();
    
            }
    
        });
    
        /* ==========================
           Blur when browser loses focus
        ========================== */
    
        window.addEventListener('blur', blur);
    
        window.addEventListener('focus', clearBlur);
    
        /* ==========================
           DevTools Detection
        ========================== */
    
        let opened = false;
    
        setInterval(() => {
    
            const before = performance.now();
    
            debugger;
    
            const after = performance.now();
    
            if(after - before > 120){
    
                if(!opened){
    
                    opened = true;
    
                    blur();
    
                }
    
            }else{
    
                if(opened){
    
                    opened = false;
    
                    clearBlur();
    
                }
    
            }
    
        },500);
    
        /* ==========================
           Detect Window Resize
        ========================== */
    
        setInterval(() => {
    
            const widthGap = window.outerWidth - window.innerWidth;
    
            const heightGap = window.outerHeight - window.innerHeight;
    
            if(widthGap > 170 || heightGap > 170){
    
                blur();
    
            }
    
        },1000);
    
    });
    
    </script>