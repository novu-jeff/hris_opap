<div class="dtr-copy">

    <div class="header">
       
    <img src="{{ asset('/img/' . $provider['client_logo']) }}" class="logo"> 

        <div class="office-title">
            Office of the Presidential Adviser<br>
            on Peace, Reconciliation and Unity
        </div>

        <table class="info-table">
            <tr>
                <td><strong>Employee Name:</strong></td>
                <td>
                    {{ strtoupper(
                        $logs['employee_account']['firstname'].' '.
                        $logs['employee_account']['middlename'].' '.
                        $logs['employee_account']['lastname']
                    ) }}
                </td>
            </tr>

            <tr>
                <td><strong>Official Time:</strong></td>
                <td>{{ $officialTime['shift_duration'] ?? 'flexible' }}</td>
            </tr>

            <tr>
                <td><strong>Month:</strong></td>
                <td>{{ \Carbon\Carbon::parse($dtrDate)->format('M. 1-31, Y') }}</td>
            </tr>

            <tr>
                <td><strong>Office:</strong></td>
                <td>{{ $logs['employee_account']['section'] }}</td>
            </tr>
        </table>
    </div>


    <table class="dtr-table">
        <thead>
            <tr>
                <th rowspan="2">Days</th>
                <th colspan="2">AM</th>
                <th colspan="2">PM</th>
                <th colspan="2">AU/T</th>
            </tr>
            <tr>
                <th>In</th>
                <th>Out</th>
                <th>In</th>
                <th>Out</th>
                <th>Hour</th>
                <th>Mins</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($logs['dtr']['logs'] as $key => $day)

                @php
                    $remarks = $day['remarks'] ?? [];
                    $remarksText = '';

                    if (is_array($remarks) && count($remarks)) {
                        $remarksText = strtoupper(implode(', ', $remarks));
                    }

                    $tardinessMinutes = $day['aut']['tardiness']['minutes'] ?? 0;
                    $undertimeMinutes = $day['aut']['undertime']['minutes'] ?? 0;

                    $totalMinutes = $tardinessMinutes + $undertimeMinutes;
                    $autHours = floor($totalMinutes / 60);
                    $autMins = $totalMinutes % 60;

                    $hasNoLogs =
                        empty($day['clock_in']) &&
                        empty($day['lunch_in']) &&
                        empty($day['lunch_out']) &&
                        empty($day['clock_out']);
                @endphp


                {{-- SHOW REMARKS ROW like SATURDAY / SUNDAY / SICK LEAVE --}}
                @if($hasNoLogs && $remarksText)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($key)->format('j') }}</td>
                        <td colspan="6" style="font-weight: bold; text-transform: uppercase;">
                            {{ $remarksText }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($key)->format('j') }}</td>

                        <td>
                            {{ isset($day['clock_in'])
                                ? \Carbon\Carbon::parse($day['clock_in'])->format('g:i')
                                : '' }}
                        </td>

                        <td>
                            {{ isset($day['lunch_in'])
                                ? \Carbon\Carbon::parse($day['lunch_in'])->format('g:i')
                                : '' }}
                        </td>

                        <td>
                            {{ isset($day['lunch_out'])
                                ? \Carbon\Carbon::parse($day['lunch_out'])->format('g:i')
                                : '' }}
                        </td>

                        <td>
                            {{ isset($day['clock_out'])
                                ? \Carbon\Carbon::parse($day['clock_out'])->format('g:i')
                                : '' }}
                        </td>

                        <td>{{ $autHours ?: '' }}</td>
                        <td>{{ $autMins ?: '' }}</td>
                    </tr>
                @endif

            @endforeach
        </tbody>
    </table>


    <div class="signature">
        <div class="line">
            {{ strtoupper(
                $logs['employee_account']['firstname'].' '.
                $logs['employee_account']['middlename'].' '.
                $logs['employee_account']['lastname']
            ) }}
        </div>
        {{ strtoupper(
            $logs['employee_account']['position']   
        ) }}

        <br><br>

        <div class="line">
           
        </div>
        Department Head
    </div>

</div>