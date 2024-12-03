<style>
    .child-row {
        display: none;
    }
</style>
<div>
    <table class="table table-striped w-100" id="logs-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Total Employees</th>
                <th>Total Clock In</th>
                <th>Total Clock Out</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $key => $log)
                <tr data-log-id="{{ $key }}">
                    <td>{{ $log['info']['date'] }}</td>
                    <td>{{ count($log['data']) . ' employee' . (count($log['data']) > 0 ? 's' : '') }}</td>
                    <td>{{ $log['info']['total']['clockin'] . ' employee' . ($log['info']['total']['clockin'] > 0 ? 's' : '') . ' already clocked in.' }}</td>
                    <td>{{ $log['info']['total']['clockout'] . ' employee' . ($log['info']['total']['clockout'] > 0 ? 's' : '') . ' already clocked out.' }}</td>
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
        // Destroy DataTable if already initialized
        if ($.fn.DataTable.isDataTable('#logs-table')) {
            $('#logs-table').DataTable().destroy();
        }

        // Initialize DataTable
        const table = $('#logs-table').DataTable({
            responsive: true,
            order: [],
        });

        // Handle "View Log" button click
        $('#logs-table tbody').on('click', '.view-log', function () {
            const tr = $(this).closest('tr'); // Get the closest table row
            const row = table.row(tr); // Access the DataTable row object

            if (row.child.isShown()) {
                // Close child row if already shown
                row.child.hide();
                tr.removeClass('shown');
            } else {
                const logId = tr.data('log-id'); // Retrieve log ID
                const logData = @json($records); // Parse the JSON data from PHP

                try {
                    // Find the log entry with the matching ID
                    const log = logData[logId];

                    // Validate log data
                    if (!log || !log.data || !Array.isArray(log.data)) {
                        throw new Error('Log data not found or invalid.');
                    }

                    // Generate content for the child row by looping through `data` array
                    const childContent = log.data.map(record => {
                        const clockInImage = record.captured_image_clockin
                            ? `{{ asset('storage/clockinout/') }}/${record.captured_image_clockin}`
                            : 'https://placehold.co/200x100.png?text=No+Image';
                        const clockOutImage = record.captured_image_clockout
                            ? `{{ asset('storage/clockinout/') }}/${record.captured_image_clockout}`
                            : 'https://placehold.co/200x100.png?text=No+Image';

                        const clockIn = record.clock_in ? new Date(record.clock_in) : null;
                        const clockOut = record.clock_out ? new Date(record.clock_out) : null;
                        const consumedHours = clockIn && clockOut
                            ? `${Math.floor((clockOut - clockIn) / 3600000)}h ${Math.floor(((clockOut - clockIn) % 3600000) / 60000)}m`
                            : 'In Progress...';

                        return `
                            <div class="mb-3">
                                <hr>
                                <div class="d-flex gap-5">
                                    <div>
                                        <strong>Employee Information:</strong>
                                        <ul class="my-3">
                                            <li>Employee No: <u>${record.employee_no}</u></li>
                                            <li>Employee Name: <u>${record.information?.personal.firstname + ' ' + record.information?.personal.lastname}</u></li>
                                            <li>Biometrics ID: <u>${record.information?.biometrics_id ?? 'N/A'}</u></li>
                                            <li>Hours Consumed: <u>${consumedHours}</u></li>
                                            <li>Clock In Location: <u>${record.captured_location_clockin ?? 'N/A'}</u></li>
                                            <li>Clock Out Location: <u>${record.captured_location_clockout ?? 'In progress...'}</u></li>    
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
                    }).join('');

                    // Display the generated content in the child row
                    row.child(childContent).show();
                    tr.addClass('shown');

                } catch (error) {
                    console.error(error.message);

                    // Trigger SweetAlert error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message,
                        confirmButtonText: 'Okay'
                    });
                }
            }
        });
    });
</script>
@endsection


