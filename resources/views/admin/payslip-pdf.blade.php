<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
        }

        .inner-content {
            max-width: 750px;
            margin: auto;
            padding: 15px;
            position: relative;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            font-size: 80px;
            color: rgba(0,0,0,0.05);
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            width: 70px;
        }

        .header-text {
            font-weight: bold;
            font-size: 14px;
        }

        .grid {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .card {
            flex: 1;
        }

        .section-title {
            font-weight: bold;
            font-size: 12px;
            border-bottom: 1px solid #000;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
        }

        .row-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .label {
            white-space: nowrap;
        }

        .value {
            text-align: right;
            min-width: 120px;
            font-family: monospace;
        }

        .highlight {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .net {
            font-weight: bold;
            font-size: 13px;
        }

        .issued {
            margin-top: 15px;
            text-align: center;
        }

        .line {
            display: inline-block;
            width: 300px;
            border-bottom: 1px solid #000;
        }
    </style>
</head>

<body>
<div class="inner-content">

    <div class="watermark">CONFIDENTIAL</div>

    <div class="header">
        <img src="{{ public_path('/img/' . $provider['client_logo']) }}">
        <div class="header-text">
            Office of the Presidential Adviser on Peace, Reconciliation and Unity<br>
            PAYROLL PAYMENT SLIP
        </div>
    </div>

    @php
        [$start, $end] = explode(' to ', $payslip['payroll']['cut_off_period']);
        $startDate = \Carbon\Carbon::parse($start);

        $fullMonthStart = $startDate->copy()->startOfMonth();
        $fullMonthEnd   = $startDate->copy()->endOfMonth();

        $fullMonthCutoff = $fullMonthStart->format('F j')
            . ' – ' .
            $fullMonthEnd->format('F j, Y');
    @endphp

    <!-- TOP GRID -->
    <div class="grid">

        <!-- Employee Info -->
        <div class="card">
            <div class="section-title">Employee Details</div>
            <table>
                <tr>
                    <td class="label">Cut Off</td>
                    <td class="value">{{ $payslipView['fullMonthCutoff'] }}</td>
                </tr>
                <tr>
                    <td class="label">Name</td>
                    <td class="value">{{ $payslip['name'] }}</td>
                </tr>
                <tr>
                    <td class="label">Position</td>
                    <td class="value">{{ $payslip['position'] }}</td>
                </tr>
                <tr>
                    <td class="label">Unit</td>
                    <td class="value">{{ $payslip['information']['section']['name'] }}</td>
                </tr>
            </table>
        </div>

        <!-- Summary -->
        <div class="card">
            <div class="section-title">Summary</div>
            <table>
                
                @if ($payslip['employment_type_id'] == 1)

                <tr class="highlight">
                    <td class="label">Gross</td>
                    <td class="value">PHP {{ number_format($payslip['gross_amount_earned'], 2) }}</td>
                </tr>

                @else

                <tr class="highlight">
                    <td class="label">Basic Salary</td>
                    <td class="value">PHP {{ number_format($payslip['basic_salary'], 2) }}</td>
                </tr>

                @endif
                <tr class="highlight">
                    <td class="label">Total Deductions</td>
                    <td class="value">PHP {{ number_format($payslip['total_deductions'], 2) }}</td>
                </tr>
                <tr class="net">
                    <td class="label">Net Pay</td>
                    <td class="value">PHP {{ number_format($payslip['net_amount'], 2) }}</td>
                </tr>
            </table>
        </div>

    </div>

    <!-- Earnings -->
    <div class="section-title">Earnings</div>
    <table>
        <tr>
            <td class="label">Monthly Basic Salary</td>
            <td class="value">PHP {{ number_format($payslip['basic_salary'], 2) }}</td>
        </tr>

        @if ($payslip['employment_type_id'] == 1)
        <tr>
            <td class="label">Personnel Economic Relief Allowance</td>
            <td class="value">PHP {{ number_format($payslip['pera'], 2) }}</td>
        </tr>
        <tr>
            <td class="label">Gross Amount Earned</td>
            <td class="value">PHP {{ number_format($payslip['gross_amount_earned'], 2) }}</td>
        </tr>
        @endif
    </table>

    <!-- Deductions -->
    <div class="section-title">Deductions</div>

   

    <table>
        <tr><td class="label">GSIS Contribution</td><td class="value">PHP {{ number_format($payslip['rlip'], 2) }}</td></tr>
        <tr><td class="label">PAG-IBIG</td><td class="value">PHP {{ number_format($payslip['hdmf'], 2) }}</td></tr>
        <tr><td class="label">PhilHealth</td><td class="value">PHP {{ number_format($payslip['philhealth'], 2) }}</td></tr>
        <tr><td class="label">GSIS Conso Loan</td><td class="value">PHP {{ number_format($payslip['consoloan'], 2) }}</td></tr>
        <tr><td class="label">GSIS Emergency Loan</td><td class="value">PHP {{ number_format($payslip['emergency_loan'], 2) }}</td></tr>
        <tr><td class="label">GSIS PLREG</td><td class="value">PHP {{ number_format($payslip['plreg'], 2) }}</td></tr>

        @if ($payslip['employment_type_id'] == 1)
        <tr><td class="label">GSIS MPL</td><td class="value">PHP {{ number_format($payslip['mpl'], 2) }}</td></tr>
        @else
        <tr><td class="label">GSIS MPL</td><td class="value">PHP 0.00</td></tr>
        @endif

        <tr><td class="label">GSIS MPL Lite</td><td class="value">PHP {{ number_format($payslip['mpl_lite'], 2) }}</td></tr>
        <tr><td class="label">GSIS CPL</td><td class="value">PHP {{ number_format($payslip['cpl'], 2) }}</td></tr>
        <tr><td class="label">GSIS GSEL</td><td class="value">PHP {{ number_format($payslip['gsel'], 2) }}</td></tr>
        <tr><td class="label">MP2</td><td class="value">PHP {{ number_format($payslip['mp2'], 2) }}</td></tr>

        @if ($payslip['employment_type_id'] != 1)
        <tr><td class="label">MPL</td><td class="value">PHP {{ number_format($payslip['mpl'], 2) }}</td></tr>
        @endif

        @if ($payslip['employment_type_id'] == 1)
        <tr><td class="label">MPL STLMS</td><td class="value">PHP {{ number_format($payslip['mplstlms'], 2) }}</td></tr>
        @endif

        <tr><td class="label">Cir375-ECQ</td><td class="value">PHP {{ number_format($payslip['cir375_cir449'], 2) }}</td></tr>
        <tr><td class="label">BIR Withholding TAX</td><td class="value">PHP {{ number_format($payslip['w_tax'], 2) }}</td></tr>
        <tr><td class="label">UCA</td><td class="value">PHP {{ number_format($payslip['uca'], 2) }}</td></tr>
        <tr><td class="label">Disallowance</td><td class="value">PHP {{ number_format($payslip['disallowance'], 2) }}</td></tr>
        <tr><td class="label">Lates/Absences</td><td class="value">PHP {{ number_format($payslip['aut'], 2) }}</td></tr>
        <tr><td class="label">Overpayment</td><td class="value">PHP {{ number_format($payslip['overpayment'], 2) }}</td></tr>
        <tr>
            <td class="label">TAX 3%</td>
            <td class="value">PHP {{ number_format($payslip['tax_3'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td class="label">TAX 5%</td>
            <td class="value">PHP {{ number_format($payslip['tax_5'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td class="label">TAX 8%</td>
            <td class="value">PHP {{ number_format($payslip['tax_8'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td class="label">TAX 10%</td>
            <td class="value">PHP {{ number_format($payslip['tax_10'] ?? 0, 2) }}</td>
        </tr>
        
        <tr class="highlight">
            <td class="label">Total Deductions</td>
            <td class="value">PHP {{ number_format($payslip['total_deductions'], 2) }}</td>
        </tr> 
    </table>

    <!-- Disbursement -->
    <div class="section-title">Disbursement</div>
    <table>
        <tr><td class="label">Net Amount</td><td class="value net">PHP {{ number_format($payslip['net_amount'], 2) }}</td></tr>
        <tr><td class="label">DBP</td><td class="value">PHP {{ number_format($payslip['dbp'], 2) }}</td></tr>
        <tr><td class="label">Unlad Kawani</td><td class="value">PHP {{ number_format($payslip['kawani'], 2) }}</td></tr>
        <tr><td class="label">LBP Payroll</td><td class="value">PHP {{ number_format($payslip['lbp_payroll_account'], 2) }}</td></tr>
        <tr><td class="label">15th</td><td class="value">PHP {{ number_format($payslip['net_first_half'], 2) }}</td></tr>
        <tr><td class="label">30th</td><td class="value">PHP {{ number_format($payslip['net_second_half'], 2) }}</td></tr>
    </table>

    <!-- Signature -->
    <div class="issued">
        <div>Issued by:</div>
        <div class="line">{{ $supervisingOfficer['full_name'] }}</div><br>
        <small>{{ $supervisingOfficer['position_name'] }}</small>
    </div>

</div>
</body>
</html>