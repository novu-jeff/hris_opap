<div class="payslip-wrapper">
    {{-- Protected Payslip --}}
    <div class="payslip-container" id="payslipProtected">

        <div class="inner-content">
            {{-- HEADER --}}
            <div class="header d-flex justify-content-center gap-3 align-items-center text-center px-5">
                <div class="logo">
                    <img style="width: 100px !important;" src="{{ asset('/img/' . $provider['client_logo']) }}">            
                </div>    
                <div class="header-text fw-bold text-center">
                    Office of the Presidential Adviser on Peace, Reconciliation and Unity <br>
                    PAYROLL PAYMENT SLIP
                </div>
            </div>

            {{-- EMPLOYEE INFO --}}
            <!--'Payroll Date' => \Carbon\Carbon::parse($payslip['payroll']['payroll_date'])->format('F d, Y'),-->
            <!--'Cutt Off Period' => collect(explode(' to ', $payslip['payroll']['cut_off_period']))
                        ->map(fn($date, $i) => \Carbon\Carbon::parse($date)->format($i === 0 ? 'F j' : 'F j, Y'))
                        ->implode(' to '),-->
            @php
               
                // Parse cut-off period
                [$start, $end] = explode(' to ', $payslip['payroll']['cut_off_period']);

                $startDate = \Carbon\Carbon::parse($start);

                // Full month range based on the payroll month
                $fullMonthStart = $startDate->copy()->startOfMonth();
                $fullMonthEnd   = $startDate->copy()->endOfMonth();

                $fullMonthCutoff = $fullMonthStart->format('F j')
                    . ' – ' .
                    $fullMonthEnd->format('F j, Y');
            @endphp



            <div class="info border-section p-3 mt-3">
                @foreach([
                    'Cutt Off Period' =>  $payslipView['fullMonthCutoff'],
                    'Employee\'s Name' => $payslip['name'],
                    'Position' => $payslip['position'],
                    'Unit' => $payslip['information']['section']['name'],
                ] as $label => $value)
                    <div class="d-flex align-items-start border-bottom py-1">
                        <div class="label fw-bold">{{ $label }}:</div>
                        <div class="value ms-2">{{ $value }}</div>
                    </div>
                @endforeach
            </div>

            {{-- EARNINGS --}}
            <div class="info border-section p-3 mt-3">
                <div class="tle px-2 fw-bold">*** Earnings ***</div>
                <div class="d-flex align-items-start border-bottom py-1">
                    <div class="label">Monthly Basic Salary:</div>
                    <div class="value ms-2">PHP {{ number_format($payslip['basic_salary'], 2) }}</div>
                </div>
                @php
                if ($payslip['employment_type_id'] == 1) {
                @endphp    
                <div class="d-flex align-items-start border-bottom py-1">
                    <div class="label">Personnel Economic Relief Allowance:</div>
                    <div class="value ms-2">PHP {{ number_format($payslip['pera'], 2)}}</div>
                </div>
                <div class="d-flex align-items-start border-bottom py-1">
                    <div class="label">Gross Amount Earned:</div>
                    <div class="value ms-2">PHP {{ number_format($payslip['gross_amount_earned'], 2)}}</div>
                </div>
                @php
                  }
                @endphp 
               
            </div>

            {{-- DEDUCTIONS --}}
            <div class="info border-section p-3 mt-3">
                <div class="tle px-2 fw-bold">*** Deductions ***</div>

                @php
                $deductions = [
                    'GSIS Contribution' => $payslip['rlip'],
                    'PAG-IBIG Contribution' => $payslip['hdmf'],
                    'Phil Health Contribution' => $payslip['philhealth'],
                    'GSIS Conso Loan' => $payslip['consoloan'],
                    'GSIS Emergency Loan' => $payslip['emergency_loan'],
                    'GSIS PLREG' => $payslip['plreg'],
                ];

                // Insert AFTER GSIS PLREG
                if ($payslip['employment_type_id'] == 1) {
                    $deductions['GSIS MPL'] = $payslip['mpl'];
                }else{
                    $deductions['GSIS MPL'] = '0.00';
                }
                $deductions += [
                    'GSIS MPL Lite' => $payslip['mpl_lite'],
                    'GSIS CPL' => $payslip['cpl'],
                    'GSIS GSEL' => $payslip['gsel'],
                    'MP2' => $payslip['mp2'],
                ];

                // Insert AFTER HDMF MP2
                if ($payslip['employment_type_id'] != 1) {
                    $deductions['MPL'] = $payslip['mpl'];
                }

                if ($payslip['employment_type_id'] == 1) {
                    $deductions['MPL STLMS'] = $payslip['mplstlms'];
                }

                $deductions += [
                    'Cir375-ECQ' => $payslip['cir375_cir449'],
                    'BIR Withholding TAX' => $payslip['w_tax'],
                    'UCA' => $payslip['uca'],
                    'DISALLOWANCE' => $payslip['disallowance'],
                    'Lates / Undertime / Absences' => $payslip['aut'],
                    'OVERPAYMENT' => $payslip['overpayment'],
                    'TAX 3%' => $payslip['tax_3 '],
                    'TAX 5%' => $payslip['tax_5'],
                    'TAX 8%' => $payslip['tax_8'],
                    'TAX 10%' => $payslip['tax_10'],
                ];
            @endphp

            @foreach($deductions as $label => $value)
                <div class="d-flex align-items-start border-bottom py-1">
                    <div class="label">{{ $label }}:</div>
                    <div class="value ms-2">PHP {{ number_format($value, 2) }}</div>
                </div>
            @endforeach
            </div>

    @if($payslip->deductions->where('reference_type', 'loan')->count())

        @foreach($payslip->deductions->where('reference_type', 'loan') as $deduction)
            <div class="d-flex align-items-start border-bottom py-1">
                <div class="label">
                    {{ $deduction->loan->loanType->name ?? 'Loan Deduction' }}
                </div>
                <div class="value ms-2">
                    PHP {{ number_format($deduction->amount, 2) }}
                </div>
            </div>
        @endforeach
    @endif


            <div class="d-flex align-items-start border-bottom py-1 fw-bold">
                <div class="label">Total Deductions:</div>
                <div class="value ms-2">
                    PHP {{ number_format($payslip['total_deductions'], 2) }}
                </div>
            </div>

            {{-- NET PAY --}}
            <div class="info border-section p-3 mt-3">
                <div class="tle px-2 fw-bold">*** Net Pay ***</div>
                @foreach([
                    'Net Amount' => $payslip['net_amount'],
                    'DBP' => $payslip['dbp'],
                    'Unlad Kawani' => $payslip['kawani'],
                    'LBP Payroll Account' => $payslip['lbp_payroll_account'],
                    'Amount Due (15)' => $payslip['net_first_half'],
                    'Amount Due (30)' => $payslip['net_second_half'],
                ] as $label => $value)
                    <div class="d-flex align-items-start border-bottom py-1">
                        <div class="label">{{ $label }}:</div>
                        <div class="value ms-2">PHP {{ number_format($value, 2) }}</div>
                    </div>
                @endforeach
            </div>

            {{-- ISSUED BY --}}
            <div class="info border-section p-3 mt-3 text-center">
                <div>Issued by: <span class="text-decoration-underline">{{ $supervisingOfficer['full_name'] }}</span></div>
                <div>{{ $supervisingOfficer['position_name'] }}</div>
            </div>

        </div>
    </div>

    {{-- SECURITY OVERLAYS --}}
    <div class="payslip-overlay"></div>

    <div class="payslip-watermark-diagonal-1">CONFIDENTIAL1 • {{$payslip['name']}} • DO NOT COPY</div>
    <div class="payslip-watermark-diagonal-2">CONFIDENTIAL 2• {{$payslip['name']}} • DO NOT COPY</div>
<div class="payslip-watermark-diagonal-3">CONFIDENTIAL 3• {{$payslip['name']}} • DO NOT COPY</div>
<div class="payslip-watermark-diagonal-4">CONFIDENTIAL4 • {{$payslip['name']}} • DO NOT COPY</div>
<div class="payslip-watermark-diagonal-5">CONFIDENTIAL5 • {{$payslip['name']}} • DO NOT COPY</div>
<div class="payslip-watermark-center-large">CONFIDENTIAL</div>
</div>


<style>
/* Wrapper */
.payslip-wrapper {
    position: relative;
    max-width: 900px;
    margin: 0 auto;
    font-family: Arial, sans-serif;
}

/* Container */
.payslip-container {
    position: relative;
    z-index: 5;
    background: #fff;
    padding: 20px;
    border: 2px solid #333;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
}

/* Section borders */
.border-section {
    border: 1px solid #333;
    border-radius: 5px;
}

/* Row bottom lines */
.border-bottom {
    border-bottom: 1px dashed #999;
}

/* Overlay */
.payslip-overlay {
    position: absolute;
    top:0; left:0;
    width: 100%; height: 100%;
    background: repeating-linear-gradient(
        45deg,
        rgba(255,255,255,0.03) 0,
        rgba(255,255,255,0.03) 2px,
        transparent 2px,
        transparent 5px
    );
    pointer-events: none;
    z-index: 10;
}

/* Watermark 1 (center diagonal) */
.payslip-watermark {
    position: absolute;
    top:50%;
    left:50%;
    transform: translate(-50%, -50%) rotate(-30deg);
    font-size: 60px;
    font-weight: 900;
    color: rgba(255,0,0,0.15);
    white-space: nowrap;
    pointer-events: none;
    z-index: 20;
}

/* Watermark 2 (bottom-right) */
.payslip-watermark-bottom {
    position: absolute;
    bottom: 365px;
    right: 15px;
    font-size: 25px;
    font-weight: 700;
    color: rgba(255,0,0,0.1);
    pointer-events: none;
    z-index: 20;
}

/* Blur effect */
.payslip-container.blur {
    filter: blur(25px);
    transition: filter 0.3s;
}

/* Overlay only covers payslip */
.payslip-overlay,
.payslip-watermark,
.payslip-watermark-bottom {
    position: absolute;
    pointer-events: none; /* IMPORTANT: allows clicks to pass through */
    z-index: 10; /* above payslip but below page elements like chatbox */
}

/* Payslip container */
.payslip-container {
    position: relative;
    z-index: 5;
}

/* Diagonal watermark #1 */
.payslip-watermark-diagonal-1,
.payslip-watermark-diagonal-2,
.payslip-watermark-diagonal-3,
.payslip-watermark-diagonal-4,
.payslip-watermark-diagonal-5     {
    position: absolute;
    font-size: 40px;
    font-weight: 100;
    color: rgba(255, 0, 0, 0.08);
    white-space: nowrap;
    pointer-events: none;
    z-index: 20;
}

/* Diagonal #1: top-left to bottom-right */
.payslip-watermark-diagonal-1 {
    top: 25%;
    left: -40%;
    transform: rotate(25deg);
}

/* Diagonal #2: bottom-left to top-right */
.payslip-watermark-diagonal-2 {
    bottom: 30%;
    left: -40%;
    transform: rotate(25deg);
}

/* Diagonal #2: bottom-left to top-right */
.payslip-watermark-diagonal-3 {
    bottom: 10%;
    left: -40%;
    transform: rotate(25deg);
}

/* Diagonal #2: bottom-left to top-right */
.payslip-watermark-diagonal-4 {
    top: 10%;
    left: -40%;
    transform: rotate(25deg);
}

/* Diagonal #2: bottom-left to top-right */
.payslip-watermark-diagonal-5 {
    top: 40%;
    left: -40%;
    transform: rotate(25deg);
}

/* Large center watermark */
.payslip-watermark-center-large {
    position: absolute;
    top: 55%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 100px;
    font-weight: 900;
    color: rgba(255, 0, 0, 0.05);
    pointer-events: none;
    z-index: 20;
    white-space: nowrap;
}

/* Keep previous bottom-right watermark */
.payslip-watermark-bottom {
    bottom: 15px;
    right: 15px;
    font-size: 25px;
    font-weight: 700;
    color: rgba(255, 0, 0, 0.1);
    pointer-events: none;
    z-index: 20;
}

/* Prevent printing */
@media print {
    body * { display: none !important; }
}
</style>


<script>
const payslip = document.getElementById('payslipProtected');

// Disable Ctrl/Cmd + P
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'p') {
        e.preventDefault();
        alert("Printing is disabled on this page.");
    }
});

// Disable right-click and selection
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('selectstart', e => e.preventDefault());



// Blur on PrintScreen
document.addEventListener('keyup', e => {
    if (e.key === "PrintScreen") {
        payslip.classList.add('blur');
        setTimeout(() => payslip.classList.remove('blur'), 1200);
    }
});

// Only blur when the entire window loses focus (user switches tab or minimizes)
/*window.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        payslip.classList.add('blur');
    } else {
        payslip.classList.remove('blur');
    }
});*/

// Blur when tab loses focus
window.addEventListener('blur', () => payslip.classList.add('blur'));
window.addEventListener('focus', () => payslip.classList.remove('blur'));

// Detect DevTools
let devtoolsOpen = false;
setInterval(() => {
    const start = performance.now();
    debugger;
    if (performance.now() - start > 100) {
        if (!devtoolsOpen) {
            devtoolsOpen = true;
            payslip.classList.add('blur');
        }
    } else {
        if (devtoolsOpen) {
            devtoolsOpen = false;
            payslip.classList.remove('blur');
        }
    }
}, 300);
</script>