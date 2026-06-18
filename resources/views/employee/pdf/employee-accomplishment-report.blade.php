<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Employee Accomplishment Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info td {
            padding: 5px;
        }

        .logs th,
        .logs td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .logs th {
            background: #f2f2f2;
        }

        .section {
            margin-top: 20px;
        }

        .label {
            font-weight: bold;
        }

        .box {
            border: 1px solid #000;
            padding: 10px;
            min-height: 60px;
        }
    </style>

</head>

<body>

    <div class="header">
        <h2>EMPLOYEE ACCOMPLISHMENT REPORT</h2>
        <p>{{ $date->format('F d, Y') }}</p>
    </div>

    <table class="info">
        <tr>
            <td width="20%"><strong>Employee No.</strong></td>
            <td>: {{ $employee->employee_no }}</td>
        </tr>

        <tr>
            <td><strong>Employee Name</strong></td>
            <td>:
                {{ optional($employee->personal)->lastname ?? 'N/A' }},
                {{ optional($employee->personal)->firstname  ?? 'N/A' }}
                {{ optional($employee->personal)->middlename ?? 'N/A' }}
            </td>
        </tr>

        <tr>
            <td><strong>Date</strong></td>
            <td>: {{ $date->format('F d, Y') }}</td>
        </tr>
    </table>

    <div class="section">

        <table class="logs">

            <thead>

                <tr>
                    <th>Clock In</th>
                    <th>Lunch Out</th>
                    <th>Lunch In</th>
                    <th>Clock Out</th>
                </tr>

            </thead>

            <tbody>

                <tr>

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

                </tr>

            </tbody>

        </table>

    </div>

    <div class="section">

        <p class="label">
            Accomplishment Type
        </p>

        <div class="box">
            {{ $clockOut->accomplishment_type ?? 'N/A' }}
        </div>

    </div>

    <div class="section">

        <p class="label">
            Accomplishment Details
        </p>

        <div class="box">
            {{ $clockOut->accomplishment_details ?? 'N/A' }}
        </div>

    </div>

    <div class="section">

        <p class="label">
            Uploaded Accomplishment File
        </p>

        <div class="box">
            {{ $clockOut->accomplishment ?? 'No uploaded PDF' }}
        </div>

    </div>

    <br><br>

    <table width="100%">
        <tr>
            <td width="50%" align="center">
                ___________________________<br>
                Employee Signature
            </td>

            <td width="50%" align="center">
                ___________________________<br>
                Supervisor
            </td>
        </tr>
    </table>

</body>

</html>

