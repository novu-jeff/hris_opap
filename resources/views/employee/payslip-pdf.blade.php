<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .inner-content {
            max-width: 510px;
            margin: 0 auto;
            position: relative;
            padding: 20px;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(200, 200, 200, 0.2);
            z-index: 0;
            pointer-events: none;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 100px;
            margin-bottom: 10px;
        }

        .header-text {
            font-weight: bold;
            font-size: 14px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 4px 8px;
        }

        .info-table td.label {
            width: 50%;
            font-weight: bold;
        }

        .info-table td.value {
            width: 50%;
            text-align: right;
        }

        .section-title {
            font-weight: bold;
            font-size: 13px;
            background-color: #f0f0f0;
            padding: 5px;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .text-center {
            text-align: center;
        }

        .issued {
            margin-top: 20px;
        }

    </style>
</head>
<body>
<div class="inner-content">
    <div class="watermark">CONFIDENTIAL</div>

    <div class="header">
        <img src="{{ public_path('/img/' . $provider['client_logo']) }}" alt="Logo">
        <div class="header-text">
            Office of the Presidential Adviser on Peace, Reconciliation and Unity<br>
            PAYROLL PAYMENT SLIP
        </div>
    </div>

    <!-- Employee Info -->
    <table class="info-table">
        <tr>
            <td class="label">Cut Off Period:</td>
            <td class="value">
                {{ collect(explode(' to ', $payslip['payroll']['cut_off_period']))
                    ->map(fn($date, $i) => \Carbon\Carbon::parse($date)->format($i === 0 ? 'F j' : 'F j, Y'))
                    ->implode(' to ') }}
            </td>
        </tr>
        <tr>
            <td class="label">Payroll Date:</td>
            <td class="value">{{ \Carbon\Carbon::parse($payslip['payroll']['payroll_date'])->format('F d, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Employee's Name:</td>
            <td class="value">{{ $payslip['name'] }}</td>
        </tr>
        <tr>
            <td class="label">Position:</td>
            <td class="value">{{ $payslip['position'] }}</td>
        </tr>
        <tr>
            <td class="label">Unit:</td>
            <td class="value">{{ $payslip['information']['section']['name'] }}</td>
        </tr>
    </table>

    <!-- Earnings -->
    <div class="section-title">*** Earnings ***</div>
    <table class="info-table">
        <tr>
            <td class="label">Monthly Basic Salary:</td>
            <td class="value">PHP {{ number_format($payslip['basic_salary'], 2) }}</td>
        </tr>
        <tr>
            <td class="label">Personnel Economic Relief Allowance:</td>
            <td class="value">PHP {{ number_format($payslip['pera'], 2) }}</td>
        </tr>
        <tr>
            <td class="label">Overtime:</td>
            <td class="value">PHP 0.00</td>
        </tr>
    </table>

    <!-- Deductions -->
    <div class="section-title">*** Deductions ***</div>
    <table class="info-table">
        <tr><td class="label">GSIS Contribution:</td><td class="value">PHP {{ number_format($payslip['rlip'], 2) }}</td></tr>
        <tr><td class="label">PAG-IBIG Contribution:</td><td class="value">PHP {{ number_format($payslip['hdmf'], 2) }}</td></tr>
        <tr><td class="label">Phil Health Contribution:</td><td class="value">PHP {{ number_format($payslip['philhealth'], 2) }}</td></tr>
        <tr><td class="label">GSIS Emergency Loan:</td><td class="value">PHP {{ number_format($payslip['emergency_loan'], 2) }}</td></tr>
        <tr><td class="label">GSIS Conso Loan:</td><td class="value">PHP {{ number_format($payslip['consoloan'], 2) }}</td></tr>
        <tr><td class="label">GSIS MPL:</td><td class="value">PHP {{ number_format($payslip['mpl'], 2) }}</td></tr>
        <tr><td class="label">GSIS MPL Lite:</td><td class="value">PHP {{ number_format($payslip['mplstlms'], 2) }}</td></tr>
        <tr><td class="label">GSIS CPL:</td><td class="value">PHP {{ number_format($payslip['cpl'], 2) }}</td></tr>
        <tr><td class="label">HDMF Calamity Loan:</td><td class="value">PHP {{ number_format($payslip['hdmf'], 2) }}</td></tr>
        <tr><td class="label">HDMF MP2:</td><td class="value">PHP {{ number_format($payslip['mp2'], 2) }}</td></tr>
        <tr><td class="label">Cir375-ECQ:</td><td class="value">PHP {{ number_format($payslip['cir375_cir449'], 2) }}</td></tr>
        <tr><td class="label">SSS:</td><td class="value">PHP {{ number_format($payslip['sss'], 2) }}</td></tr>
        <tr><td class="label">PAGIBIG:</td><td class="value">PHP {{ number_format($payslip['pagibig'], 2) }}</td></tr>
        <tr><td class="label">BIR Withholding TAX:</td><td class="value">PHP {{ number_format($payslip['w_tax'], 2) }}</td></tr>
        <tr><td class="label">Lates / Undertime / Absences:</td><td class="value">PHP {{ number_format($payslip['aut'], 2) }}</td></tr>
        <tr><td class="label">Total Deductions:</td><td class="value">PHP {{ number_format($payslip['total_deductions'], 2) }}</td></tr>
    </table>

    <!-- Net Pay -->
    <div class="section-title">*** Net Pay ***</div>
    <table class="info-table">
        <tr><td class="label">Net Amount:</td><td class="value">PHP {{ number_format($payslip['net_amount'], 2) }}</td></tr>
        <tr><td class="label">DBP:</td><td class="value">PHP {{ number_format($payslip['dbp'], 2) }}</td></tr>
        <tr><td class="label">Unlad Kawani:</td><td class="value">PHP {{ number_format($payslip['kawani'], 2) }}</td></tr>
        <tr><td class="label">Amount Due (15):</td><td class="value">PHP {{ number_format($payslip['salary'], 2) }}</td></tr>
        <tr><td class="label">Amount Due (28):</td><td class="value">PHP {{ number_format($payslip['salary'], 2) }}</td></tr>
    </table>

    <!-- Issued By -->
    <div class="issued text-center">
        <div>Issued by : <span style="text-decoration: underline;">____________________</span></div>
        <div>___________________________</div>
    </div>

</div>
</body>
</html>
