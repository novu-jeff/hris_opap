```blade
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <title>Monthly Accomplishment Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        h2,
        h3,
        h4 {
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .employee-info {
            margin-bottom: 20px;
        }

        .employee-info td {
            padding: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .report-table th {
            background: #efefef;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }
    </style>

</head>

<body>

    <div class="header">

        <h2>MONTHLY EMPLOYEE ACCOMPLISHMENT REPORT</h2>

        <h4>{{ $month->format('F Y') }}</h4>

    </div>

    <table class="employee-info">

        <tr>

            <td width="20%">
                <strong>Employee No.</strong>
            </td>

            <td width="80%">
                : {{ $employee->employee_no }}
            </td>

        </tr>

        <tr>

            <td>
                <strong>Employee Name</strong>
            </td>

            <td>

                :

                {{ optional($employee->personal)->lastname ?? 'N/A' }},
                {{ optional($employee->personal)->firstname ?? 'N/A' }}
                {{ optional($employee->personal)->middlename ?? 'N/A' }}

            </td>

        </tr>

    </table>

    <table class="report-table">

        <thead>

            <tr>

                <th width="10%">
                    Date
                </th>

                <th width="10%">
                    Clock In
                </th>

                <th width="10%">
                    Lunch Out
                </th>

                <th width="10%">
                    Lunch In
                </th>

                <th width="10%">
                    Clock Out
                </th>

                <th width="20%">
                    Type
                </th>

                <th width="30%">
                    Details
                </th>

            </tr>

        </thead>

        <tbody>

            @foreach($logs as $date => $dayLogs)

                @php

                    $records = $dayLogs->values();

                    $clockIn = $records->get(0);

                    $lunchOut = $records->get(1);

                    $lunchIn = $records->get(2);

                    $clockOut = $records->last();

                @endphp

                <tr>

                    <td>

                        {{ \Carbon\Carbon::parse($date)->format('M d') }}

                    </td>

                    <td>

                        {{ $clockIn ? \Carbon\Carbon::parse($clockIn->timestamp)->format('h:i A') : '-' }}

                    </td>

                    <td>

                        {{ $lunchOut ? \Carbon\Carbon::parse($lunchOut->timestamp)->format('h:i A') : '-' }}

                    </td>

                    <td>

                        {{ $lunchIn ? \Carbon\Carbon::parse($lunchIn->timestamp)->format('h:i A') : '-' }}

                    </td>

                    <td>

                        {{ $clockOut ? \Carbon\Carbon::parse($clockOut->timestamp)->format('h:i A') : '-' }}

                    </td>

                    <td>

                        {{ $clockOut->accomplishment_type ?? '-' }}

                    </td>

                    <td>

                        @if(!empty($clockOut->accomplishment_details))

                            {{ $clockOut->accomplishment_details }}

                        @elseif(!empty($clockOut->accomplishment))

                            Uploaded PDF:
                            {{ $clockOut->accomplishment }}

                        @else

                            -

                        @endif

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

    <br><br>

    <table width="100%">

        <tr>

            <td width="50%" align="center">

                _______________________________<br>

                Employee Signature

            </td>

            <td width="50%" align="center">

                _______________________________<br>

                Supervisor

            </td>

        </tr>

    </table>

</body>

</html>
```
