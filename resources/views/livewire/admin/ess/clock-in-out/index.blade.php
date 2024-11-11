<div>
    <table class="table table-striped w-100">
        <thead>
            <tr>
                <th>ID</th>
                <th>Biometrics</th>
                <th>Name</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Hours Consumed</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $log)
                <tr>
                    <td>#{{ format_id($log->information->id, 6) }}</td>
                    <td>{{ $log->information->biometrics_id }}</td>
                    <td>{{ $log->information->personal->firstname . ' ' . $log->information->personal->lastname }}</td> 
                    <td>{{ \Carbon\Carbon::parse($log->clock_in)->format('h:i A') }}</td>
                    <td>{{ $log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('h:i A') : 'In Progress...' }}</td>
                    <td>
                        @if ($log->clock_in && $log->clock_out)
                            @php
                                $clockIn = \Carbon\Carbon::parse($log->clock_in);
                                $clockOut = \Carbon\Carbon::parse($log->clock_out);
                                $hoursConsumed = $clockIn->diffInHours($clockOut);
                                $minutesConsumed = $clockIn->diffInMinutes($clockOut) % 60;
                            @endphp
                            {{ $hoursConsumed }}h {{ $minutesConsumed }}m
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y') }}</td> 
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
