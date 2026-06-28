<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">

<style>

@page{
    size:A4 landscape;
    margin:12mm;
}

body{
    background:#ffffff;
    margin:0;
    padding:0;
    font-family:DejaVu Sans, Arial, sans-serif;
    font-size:8px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,
td{
    border:1px solid #CFCFCF;
    padding:2px 4px;
    vertical-align:top;
}

.header-title{
    font-size:15px;
    font-weight:bold;
    color:#0F4C81;
    text-align:center;
}

.header-subtitle{
    font-size:12px;
    font-weight:bold;
    text-align:center;
    margin-top:5px;
}

.section-header{
    background:#0F4C81;
    color:#fff;
    font-weight:bold;
    text-align:center;
}

.label{
    background:#F2F6FA;
    color:#0F4C81;
    font-weight:bold;
}

.value{
    background:#FFF;
}

.logo{
    width:75px;
}

</style>

</head>

<body>

@php

[$start,$end] = explode(' to ', $payslip['payroll']['cut_off_period']);

$startDate = \Carbon\Carbon::parse($start);

$fullMonthCutoff =
$startDate->copy()->startOfMonth()->format('F j')
.' - '.
$startDate->copy()->endOfMonth()->format('F j, Y');


$earnings = [];

$earnings[]=[
'label'=>'Monthly Basic Salary',
'amount'=>$payslip['basic_salary']
];

if($payslip['employment_type_id']==1){

    $earnings[]=[
        'label'=>'Personnel Economic Relief Allowance',
        'amount'=>$payslip['pera']
    ];

    $earnings[]=[
        'label'=>'Gross Amount Earned',
        'amount'=>$payslip['gross_amount_earned']
    ];

}

$deductions=[];

$deductions[]=['label'=>'GSIS Contribution','amount'=>$payslip['rlip']];
$deductions[]=['label'=>'PAG-IBIG Contribution','amount'=>$payslip['hdmf']];
$deductions[]=['label'=>'PhilHealth Contribution','amount'=>$payslip['philhealth']];
$deductions[]=['label'=>'GSIS Conso Loan','amount'=>$payslip['consoloan']];
$deductions[]=['label'=>'GSIS Emergency Loan','amount'=>$payslip['emergency_loan']];
$deductions[]=['label'=>'GSIS PLREG','amount'=>$payslip['plreg']];

if($payslip['employment_type_id']==1){

    $deductions[]=[
        'label'=>'GSIS MPL',
        'amount'=>$payslip['mpl']
    ];

}else{

    $deductions[]=[
        'label'=>'MPL',
        'amount'=>$payslip['mpl']
    ];

}

$deductions[]=['label'=>'GSIS MPL Lite','amount'=>$payslip['mpl_lite']];
$deductions[]=['label'=>'GSIS CPL','amount'=>$payslip['cpl']];
$deductions[]=['label'=>'GSIS GSEL','amount'=>$payslip['gsel']];
$deductions[]=['label'=>'GSIS GBEL','amount'=>$payslip['gbel']];
$deductions[]=['label'=>'MP2','amount'=>$payslip['mp2']];

if($payslip['employment_type_id']==1){

    $deductions[]=[
        'label'=>'MPL STLMS',
        'amount'=>$payslip['mplstlms']
    ];

}

$deductions[]=['label'=>'Cir375-ECQ','amount'=>$payslip['cir375_cir449']];
$deductions[]=['label'=>'BIR Withholding Tax','amount'=>$payslip['w_tax']];
$deductions[]=['label'=>'UCA','amount'=>$payslip['uca']];
$deductions[]=['label'=>'Disallowance','amount'=>$payslip['disallowance']];
$deductions[]=['label'=>'Late / UT / Absences','amount'=>$payslip['aut']];
$deductions[]=['label'=>'Overpayment','amount'=>$payslip['overpayment']];
$deductions[]=['label'=>'Tax 3%','amount'=>$payslip['tax_3']];
$deductions[]=['label'=>'Tax 5%','amount'=>$payslip['tax_5']];
$deductions[]=['label'=>'Tax 8%','amount'=>$payslip['tax_8']];
$deductions[]=['label'=>'Tax 10%','amount'=>$payslip['tax_10']];

foreach($payslip->deductions->where('reference_type','loan') as $loan){

    $deductions[]=[
        'label'=>$loan->loan->loanType->name ?? 'Loan Deduction',
        'amount'=>$loan->amount
    ];

}

@endphp


<table>

    <tr>

        <td width="90" style="border:none;">

        @php
        $logo = public_path('img/'.$provider['client_logo']);
        @endphp

        @if(file_exists($logo))
        <img src="{{ $logo }}" class="logo">
        @endif

        </td>

        <td style="border:none;">

            <div class="header-title">

            OFFICE OF THE PRESIDENTIAL ADVISER

            </div>

            <div class="header-title">

            ON PEACE, RECONCILIATION AND UNITY

            </div>

            <div class="header-subtitle">

            PAYROLL PAYMENT SLIP

            </div>

        </td>

    </tr>

</table>


<table style="margin-top:12px;">

    <tr>

        <td class="label" width="15%">Employee No.</td>
        <td class="value" width="35%">{{ $payslip['employee_no'] }}</td>

        <td class="label" width="15%">Employee Name</td>
        <td class="value" width="35%">{{ $payslip['name'] }}</td>

    </tr>

    <tr>

        <td class="label">Position</td>
        <td>{{ $payslip['position'] }}</td>

        <td class="label">Unit</td>
        <td>{{ $payslip['information']['section']['name'] }}</td>

    </tr>

    <tr>

        <td class="label">Payroll Date</td>

        <td>{{ \Carbon\Carbon::parse($payslip['payroll']['payroll_date'])->format('F d, Y') }}</td>

        <td class="label">Cut-off Period</td>

        <td>{{ $fullMonthCutoff }}</td>

    </tr>

</table>


@php

$deductions = collect($deductions)->values();

$middle = (int) ceil($deductions->count() / 2);

$leftDeductions = $deductions->slice(0, $middle);

$rightDeductions = $deductions->slice($middle);

@endphp

<table width="100%" style="border:none; margin-top:10px;">

<tr>

    <!-- ========================= -->
    <!-- EARNINGS                  -->
    <!-- ========================= -->

    <td width="34%" valign="top" style="border:none;">

        <table>

            <tr>
                <th colspan="2" class="section-header">
                    EARNINGS
                </th>
            </tr>

            @foreach($earnings as $item)

                <tr>

                    <td width="70%">
                        {{ $item['label'] }}
                    </td>

                    <td width="30%" align="right">
                        {{ number_format($item['amount'],2) }}
                    </td>

                </tr>

            @endforeach

            <tr style="font-weight:bold;background:#F2F6FA;">

                <td>

                    TOTAL EARNINGS

                </td>

                <td align="right">

                    {{ number_format(
                        $payslip['employment_type_id']==1
                            ? $payslip['gross_amount_earned']
                            : $payslip['basic_salary'],
                    2) }}

                </td>

            </tr>

        </table>

    </td>


    <!-- ========================= -->
    <!-- DEDUCTIONS LEFT           -->
    <!-- ========================= -->

    <td width="33%" valign="top" style="border:none;">

        <table>

            <tr>
                <th colspan="2" class="section-header">
                    DEDUCTIONS
                </th>
            </tr>

            @foreach($leftDeductions as $item)

                <tr>

                    <td width="72%">
                        {{ $item['label'] }}
                    </td>

                    <td width="28%" align="right">
                        {{ number_format($item['amount'],2) }}
                    </td>

                </tr>

            @endforeach

        </table>

    </td>


    <!-- ========================= -->
    <!-- DEDUCTIONS RIGHT          -->
    <!-- ========================= -->

    <td width="33%" valign="top" style="border:none;">

        <table>

            <tr>
                <th colspan="2" class="section-header">
                    DEDUCTIONS
                </th>
            </tr>

            @foreach($rightDeductions as $item)

                <tr>

                    <td width="72%">
                        {{ $item['label'] }}
                    </td>

                    <td width="28%" align="right">
                        {{ number_format($item['amount'],2) }}
                    </td>

                </tr>

            @endforeach

            <tr style="font-weight:bold;background:#F2F6FA;">

                <td>

                    TOTAL DEDUCTIONS

                </td>

                <td align="right">

                    {{ number_format($payslip['total_deductions'],2) }}

                </td>

            </tr>

        </table>

    </td>

</tr>

</table>



<!-- ============================= -->
<!-- Prevent DomPDF Page Break     -->
<!-- ============================= -->

<!-- ============================= -->
<!-- NET PAY SUMMARY               -->
<!-- ============================= -->

<table style="margin-top:6px; page-break-inside:auto;">

    <thead>

        <tr>

            <th colspan="5"
                style="
                    background:#198754;
                    color:#FFF;
                    font-size:11px;
                    text-align:center;
                    padding:4px;
                ">
                NET PAY SUMMARY
            </th>

        </tr>

        <tr style="background:#EAF7EA;">

            <th width="20%">
                {{ $payslip['employment_type_id']==1 ? 'Gross Earnings' : 'Total Earnings' }}
            </th>

            <th width="20%">
                Total Deductions
            </th>

            <th width="20%">
                Net Amount
            </th>

            <th width="20%">
                Amount Due (15)
            </th>

            <th width="20%">
                Amount Due (30)
            </th>

        </tr>

    </thead>

    <tbody>

        <tr>

            <td align="right">

                {{ number_format(
                    $payslip['employment_type_id']==1
                        ? $payslip['gross_amount_earned']
                        : $payslip['basic_salary'],
                2) }}

            </td>

            <td align="right">

                {{ number_format($payslip['total_deductions'],2) }}

            </td>

            <td align="right"
                style="
                    font-weight:bold;
                    color:#198754;
                ">

                {{ number_format($payslip['net_amount'],2) }}

            </td>

            <td align="right">

                {{ number_format($payslip['net_first_half'],2) }}

            </td>

            <td align="right">

                {{ number_format($payslip['net_second_half'],2) }}

            </td>

        </tr>

    </tbody>

</table>


<!-- ============================= -->
<!-- FOOTER                        -->
<!-- ============================= -->

    <table
        style="
            margin-top:8px;
            border:none;
            page-break-inside:avoid;
        ">

        <tr>

            <td
                width="60%"
                style="border:none;">
                @if(!$use_employee)
            <div
            style="
            font-weight:bold;
            margin-bottom:40px;
            ">

            Prepared / Approved By

            </div>

            <div
            style="
            font-weight:bold;
            font-size:13px;
            text-decoration:underline;
            ">

            MARIESER T. ALMELOR

            </div>

            <div>

            Chief Administrative Officer

            </div>
            @else
            <div
            style="
            border:1px solid #d6d6d6;
            background:#f8f9fa;
            padding:8px 10px;
            border-left:4px solid #0F4C81;
            font-size:9px;
            line-height:1.5;
            ">

            <div
            style="
            font-weight:bold;
            color:#0F4C81;
            margin-bottom:4px;
            ">
            NOTICE
            </div>

            <div style="color:#444;">

            This payslip was generated through the Employee Self-Service (ESS) portal
            and is provided for reference and verification purposes only.

            Official payroll records are maintained by the Human Resource Management Office (HRMO).
            If you find any discrepancy in your payroll information, please contact HR immediately for verification.

            </div>

            </div>


        
        @endif

            </td>


            <td
            width="40%"
            style="
            border:none;
            vertical-align:bottom;
            ">

                <table>

                <tr>

                    <td
                    style="
                    background:#F2F6FA;
                    font-weight:bold;
                    width:40%;
                    ">

                    Generated On

                    </td>

                    <td>

                    {{ now()->format('F d, Y h:i A') }}

                    </td>

                </tr>

                <tr>

                    <td
                    style="
                    background:#F2F6FA;
                    font-weight:bold;
                    ">

                    Payroll Period

                    </td>

                    <td>

                    {{ $fullMonthCutoff }}

                    </td>

                </tr>

                </table>

        </td>

        </tr>

    </table>


<!-- ============================= -->
<!-- WATERMARK                     -->
<!-- ============================= -->

<div
style="
position:fixed;
top:45%;
left:18%;
font-size:95px;
color:rgba(200,0,0,.05);
font-weight:bold;
transform:rotate(-30deg);
z-index:-1;
">

CONFIDENTIAL

</div>


</body>

</html>




