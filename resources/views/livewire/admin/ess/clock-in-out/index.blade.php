<style>
    .child-row {
        display: none;
    }
</style>
<div>
    <table class="table table-striped w-100" id="logs-table">
        <thead>
            <tr>
                <th>Employee No</th>
                <th>Employee Name</th>
                <th>Clock In Time</th>
                <th>Clock Out Time</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $log)
                <tr data-log-id="{{ $log->id }}">
                    <td>{{ $log->information->employee_no }}</td>
                    <td>{{ $log->information->personal->firstname . ' ' . $log->information->personal->lastname }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->clock_in)->format('h:i A') }}</td>
                    <td>{{ $log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('h:i A') : 'In Progress...' }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y') }}</td>
                    <td>
                        <button class="btn btn-primary view-log">View Log</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@section('script')
<script>
    $(function () {
        if ($.fn.DataTable.isDataTable('table')) {
            $('table').DataTable().destroy();
        }

        const table = $('#logs-table').DataTable({
            responsive: true,
            order: [],
        });

        $('#logs-table tbody').on('click', '.view-log', function () {
            const tr = $(this).closest('tr');
            const row = table.row(tr);

            if (row.child.isShown()) {
                // Close child row
                row.child.hide();
                tr.removeClass('shown');
            } else {
                const logId = tr.data('log-id');
                const logData = @json($records->keyBy('id'));

                const log = logData[logId];
                const clockInImage = log.captured_image_clockin
                    ? `{{ asset('storage/clockinout/') }}/${log.captured_image_clockin}`
                    : 'https://placehold.co/200x100.png?text=No+Image';
                const clockOutImage = log.captured_image_clockout
                    ? `{{ asset('storage/clockinout/') }}/${log.captured_image_clockout}`
                    : 'https://placehold.co/200x100.png?text=No+Image';
                
                const clockIn = new Date(log.clock_in);
                const clockOut = log.clock_out ? new Date(log.clock_out) : null;
                const consumedHours = clockOut
                    ? `${Math.floor((clockOut - clockIn) / 3600000)}h ${Math.floor(((clockOut - clockIn) % 3600000) / 60000)}m`
                    : 'In Progress...';

                const childContent = `
                    <div class="mb-3">
                        <hr>
                        <div class="d-flex gap-5">
                            <div>
                                <strong>Other Details:</strong>
                                <ul class="my-3">
                                    <li>Biometrics ID: <u>${log.information.biometrics_id ?? ''}</u></li>
                                    <li>Hours Consumed: <u>${consumedHours}</u></li>
                                    <li>Clock In Location: <u>${log.captured_location_clockin}</u></li>
                                    <li>Clock Out Location: <u>${log.captured_location_clockout ?? 'In progress...'}</u></li>    
                                </ul>
                            </div>
                            <div>
                                <div style="display: flex; gap: 30px; align-items: center;">
                                    <div>
                                        <p class="fw-bold">Captured Clock In:</p>
                                        <img src="${clockInImage}" alt="Clock In Image" style="width: 300px; height: 150px; object-fit: cover">
                                    </div>
                                    <div>
                                        <p class="fw-bold">Captured Clock Out:</p>
                                        <img src="${clockOutImage}" alt="Clock Out Image" style="width: 300px; height: 150px; object-fit: cover">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                row.child(childContent).show();
                tr.addClass('shown');
            }
        });
    });
</script>
@endsection
