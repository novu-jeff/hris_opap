<div>
    <table class="table table-striped w-100">
        <thead>
            <tr>
                <th>ID</th>
                <th>Biometrics</th>
                <th>Name</th>
                <th>Clock In Time</th>
                <th>Catured Clock In</th>
                <th>Clock Out Time</th>
                <th>Captured Clouck Out</th>
                <th>Hours Consumed</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $log)
                <tr>
                    <td>{{ $log->information->employee_no }}</td>
                    <td>{{ $log->information->biometrics_id }}</td>
                    <td>{{ $log->information->personal->firstname . ' ' . $log->information->personal->lastname }}</td> 
                    <td>{{ \Carbon\Carbon::parse($log->clock_in)->format('h:i A') }}</td>
                    <td>
                        <img src="{{ $log->captured_image_clockin ? asset('storage/clockinout/' . $log->captured_image_clockin) : 'https://placehold.co/200x100.png?text=No+Image' }}" alt="Clock In Image">
                    </td>
                    <td>{{ $log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('h:i A') : 'In Progress...' }}</td>
                    <td>
                        <img src="{{ $log->captured_image_clockout ? asset('storage/clockinout/' . $log->captured_image_clockout) : 'https://placehold.co/200x100.png?text=No+Image' }}" alt="Clock Out Image">
                    </td>
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
                            In Progress...
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y') }}</td> 
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
