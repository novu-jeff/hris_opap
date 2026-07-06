<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<style>

@page{
    size:A4;
    margin:15mm;
}

body{
    background:#fff;
    margin:0;
    padding:0;
    font-family:DejaVu Sans, Arial, sans-serif;
    font-size:11px;
    color:#000;
}

table{
    width:100%;
    border-collapse:collapse;
}

td{
    border:1px solid #000;
    padding:6px;
    vertical-align:top;
}

.logo{
    width:75px;
}

.header-title{
    font-size:15px;
    font-weight:bold;
    color:#0F4C81;
    text-align:center;
}

.header-subtitle{
    font-size:13px;
    font-weight:bold;
    text-align:center;
}

.document-title{
    margin-top:12px;
    font-size:17px;
    font-weight:bold;
    text-align:center;
    letter-spacing:.5px;
}

.section-title{
    font-size:14px;
    font-weight:bold;
    text-align:center;
}

.label{
    font-weight:bold;
    background:#F2F6FA;
    color:#0F4C81;
    padding:3px 5px;
    font-size:11px;
}

.value{
    padding:6px 5px;
    font-size:11px;
    line-height:16px;
}

.row-fixed{
    height:70px;
}

.top{
    vertical-align:top;
}

.bottom{
    vertical-align:bottom;
}

.middle{
    vertical-align:middle;
}

.signature{
    margin-top:45px;
    border-top:1px solid #000;
    width:220px;
    text-align:center;
    padding-top:4px;
    margin-left:auto;
    margin-right:auto;
}

</style>

</head>

<body>

    @php

    $logo = public_path('img/'.$provider['client_logo']);
    
    @endphp
    
    <table style="border:none; margin-bottom:10px;">
    
    <tr>
    
    <td
    width="5"
    style="border:none;">
    
    @if(file_exists($logo))
    
    <img src="{{ $logo }}" class="logo">
    
    @endif
    
    </td>
    
    <td style="border:none;">
    
    <div class="header-title">
    
    OFFICE OF THE PRESIDENT
    
    </div>
    
    <div class="header-title">
    
    OFFICE OF THE PRESIDENTIAL ADVISER
    
    </div>
    
    <div class="header-title">
    
    ON PEACE, RECONCILIATION, AND UNITY
    
    </div>
    
    <div class="header-subtitle">
    
    Human Resource Management Office
    
    </div>
    
    <div class="document-title">
    
    AUTHORITY TO RENDER OFFSETTING
    
    </div>
    
    </td>
    
    </tr>
    
    </table>

<table>

<tr>

<td width="50%">

<div class="label">

Office Order No.

</div>

<div class="value">

{{ $record->office_order_no }}

</div>

</td>

<td>

<div class="label">

Date of Filing

</div>

<div class="value">

{{ \Carbon\Carbon::parse($record->filing_date)->format('F d, Y') }}

</div>

</td>

</tr>

<tr>

<td>

<div class="label">

NAME (Last)&nbsp;&nbsp;&nbsp;&nbsp;
(First)&nbsp;&nbsp;&nbsp;&nbsp;
(M.I.)

</div>

<div class="value">

{{ strtoupper($employee->personal->lastname) }},
{{ strtoupper($employee->personal->firstname) }}
{{ strtoupper(substr($employee->personal->middlename ?? '',0,1)) }}

</div>

</td>

<td>

<div class="label">

DURATION (Date/s Covered)

</div>

<div class="value">

{{ \Carbon\Carbon::parse($record->offset_date)->format('F d, Y') }}

<br><br>

{{ str_replace('_',' ',$record->request_type) }}

({{ number_format($record->hours_requested,2) }} Hours)

</div>

</td>

</tr>

<tr class="row-fixed">

    <td class="top">
    
    <div class="label">
    Position
    </div>
    
    <div class="value" style="margin-top:8px;">
    {{ optional($employee->positions)->name ?? '____________________' }}
    </div>
    
    </td>
    
    <td class="top">
    
    <div class="label">
    Salary / Mo.
    </div>
    
    <div class="value" style="margin-top:8px;">
    ₱ {{ number_format($employee->salary ?? 0,2) }}
    </div>
    
    </td>
    
    </tr>

</table>

<br>

<div class="section-title">

CERTIFICATION OF OVERTIME CREDITS

</div>

<div class="center">

As of

{{ now()->format('F d, Y') }}

</div>

<br>

<table>

<tr>

<td width="50%">

<div class="label">

Credits Earned

(No. of days / hours)

</div>

<div class="value">

{{ number_format($earnedHours,2) }}

Hours

</div>

</td>

<td>

<div class="label">

Remaining Balance

</div>

<div class="value">

{{ number_format($remainingHours,2) }}

Hours

</div>

</td>

</tr>

<tr>

<td colspan="2" style="height:90px;">

<div class="label">

Certified Correct

</div>

<div class="signature">

HR Officer

</div>

</td>

</tr>

<tr>

<td style="height:90px;">

<div class="label">

Requested By

</div>

<div class="signature">

{{ strtoupper($employee->personal->lastname) }},
{{ strtoupper($employee->personal->firstname) }}

</div>

</td>

<td>

<div class="label">

Remarks

</div>

<div style="margin-top:10px">

{{ $record->remarks }}

</div>

</td>

</tr>

<tr>

<td style="height:90px;">

<div class="label">

Recommending Approval

</div>

<div class="signature">

{{-- {{ optional($recommendedBy)->name }} --}}

</div>

</td>

<td>

<div class="label">

Approved By

</div>

<div class="signature">

{{-- {{ optional($approvedBy)->name }} --}}

</div>

</td>

</tr>

</table>
<table
style="
margin-top:15px;
border:none;
">

<tr>

<td
style="
border:none;
font-size:10px;
">

This document was generated through the HRIS Employee Self-Service Portal.

</td>

<td
style="
border:none;
text-align:right;
font-size:10px;
">

Generated On

<br>

{{ now()->format('F d, Y h:i A') }}

</td>

</tr>

</table>

</body>

</html>