<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip PDF</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        .payslip-wrapper {
            width: 100%;
            max-width: 450px;
            margin: 20px auto;
        }

        .payslip-container {
            position: relative;
            border: 2px solid #000;
            padding: 18px;
            background: #fff;
        }

        .watermark {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 55px;
            font-weight: bold;
            color: rgba(255, 0, 0, 0.04);
            z-index: 1;
            white-space: nowrap;
        }

        .content {
            position: relative;
            z-index: 2;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .logo {
            width: 55px;
            margin-bottom: 6px;
        }

        .office-title {
            font-size: 12px;
            font-weight: bold;
            line-height: 1.4;
        }

        .payroll-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 6px;
        }

        .pay-period {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .emp-info{border-bottom: 1px solid #000;}
        .emp-info td:first-child {
            width: 38%;
        }
        .emp-info td:last-child {
            width: 62%;
            padding-left: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 11px;
        }

        td:first-child {
            width: 58%;
            font-weight: bold;
        }

        .amount {
            width: 42%;
            text-align: right;
            font-weight: bold;
            white-space: nowrap;
        }

        .section-title {
            font-weight: bold;
            margin: 8px 0 4px;
        }

        .footer {
            margin-top: 18px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 8px;
        }
    </style>
</head>

<body>

<div class="payslip-wrapper">
    <div class="payslip-container">

        <div class="watermark">CONFIDENTIAL</div>

        <div class="content">

            {{-- HEADER --}}
            <div class="header">
                @php
                        $logo = !empty($provider['client_logo'])
                            ? public_path('img/' . $provider['client_logo'])
                            : null;
                    @endphp

                    @if($logo && file_exists($logo))
                        <img
                            src="{{ $logo }}"
                            class="logo"
                        >
                    @endif
                

                <div class="office-title">
                    Office of the Presidential Adviser <br>
                    on Peace, Reconciliation and Unity
                </div>

                <div class="payroll-title">
                    PAYROLL <br>
                    PAYMENT SLIP
                </div>
            </div>

            {{-- PAY PERIOD --}}
            <div class="pay-period">
                Pay Period:
                {{ $payslipView['fullMonthCutoff'] }}
            </div>

            {{-- EMPLOYEE INFO --}}
            <table class="emp-info" style="margin-bottom: 12px;">
                <tr>
                    <td>EMPLOYEE'S NAME :</td>
                    <td>{{ strtoupper($payslip['name']) }}</td>
                </tr>
                <tr>
                    <td>EMPLOYEE'S NO :</td>
                    <td>{{ strtoupper($payslip['employee_no']) }}</td>
                </tr>
                <tr>
                    <td>POSITION :</td>
                    <td>{{ strtoupper($payslip['position']) }}</td>
                </tr>
                <tr>
                    <td>UNIT :</td>
                    <td>{{ strtoupper($payslip['information']['section']['name']) }}</td>
                </tr>
            </table>

            {{-- EARNINGS --}}
            <div class="section-title">***Earnings***</div>

            <table>
                <tr>
                    <td>Monthly Basic Salary :</td>
                    <td class="amount">
                        {{ number_format($payslip['basic_salary'], 2) }}
                    </td>
                </tr>

                @if($payslip['employment_type_id'] == 1)
                <tr>
                    <td>Personnel Economic Relief Allowance :</td>
                    <td class="amount">
                        {{ number_format($payslip['pera'], 2) }}
                    </td>
                </tr>

                <tr>
                    <td>Gross Amount Earned :</td>
                    <td class="amount">
                        {{ number_format($payslip['gross_amount_earned'], 2) }}
                    </td>
                </tr>
                @endif
            </table>

            {{-- DEDUCTIONS --}}
            <div class="section-title">***Deductions***</div>
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
                'GSIS GBEL' => $payslip['gbel'],
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
                'TAX 3%' => $payslip['tax_3'],
                'TAX 5%' => $payslip['tax_5'],
                'TAX 8%' => $payslip['tax_8'],
                'TAX 10%' => $payslip['tax_10'],
            ];
        @endphp
            <table>
                @foreach($deductions as $label => $value)
                <tr>
                    <td>{{ $label }} :</td>
                    <td class="amount">
                        @if((float)$value > 0)
                            {{ number_format($value, 2) }}
                        @endif
                    </td>
                </tr>
                @endforeach

                <tr>
                    <td>Total Deductions :</td>
                    <td class="amount">
                        {{ number_format($payslip['total_deductions'], 2) }}
                    </td>
                </tr>
            </table>

            {{-- NET PAY --}}
            <div class="section-title">***Net Pay***</div>

            <table>
                <tr>
                    <td>Net Amount :</td>
                    <td class="amount">
                        {{ number_format($payslip['net_amount'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td>Amount Due (15) :</td>
                    <td class="amount">
                        {{ number_format($payslip['net_first_half'], 2) }}
                    </td>
                </tr>

                <tr>
                    <td>Amount Due (30) :</td>
                    <td class="amount">
                        {{ number_format($payslip['net_second_half'], 2) }}
                    </td>
                </tr>
            </table>
            @if(!$use_employee)
            {{-- FOOTER --}}
            <div class="footer">
                <div>
                    Issued by:
                    MARIESER T. ALMELOR
                </div>
                <div>
                    Chief Administrative Officer
                </div>
                <div>
                    Date:
                    {{ now()->format('d F Y') }}
                </div>
            </div>
            @else
            <div class="footer">
                <div>
                    Disclaimer: This payslip is self-generated by the employee and is for reference purposes only. It is not an official payslip issued or certified by Management.
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

</body>
</html>