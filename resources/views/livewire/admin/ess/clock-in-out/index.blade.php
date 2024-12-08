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

                        const clockIn = record.clock_in_am ? new Date(record.clock_in_am) : null;
                        const breakOut = record.clock_out_am ? new Date(record.clock_out_am) : null;
                        const breakIn = record.clock_in_pm ? new Date(record.clock_in_pm) : null;
                        const clockOut = record.clock_out_pm ? new Date(record.clock_out_pm) : null;
                        const consumedHours = clockIn && clockOut
                            ? `${Math.floor((clockOut - clockIn) / 3600000)}h ${Math.floor(((clockOut - clockIn) % 3600000) / 60000)}m`
                            : 'In Progress...';

                        return `
                            <div class="mb-3">
                                <hr>
                                <div class="row">
                                    <div class="col-12 col-md-8">
                                        <strong>Employee Information:</strong>
                                        <ul class="my-3">
                                            <li>Employee No: <strong><u>${record.employee_no}</u></strong></li>
                                            <li>Employee Name: <strong><u>${record.information?.personal.firstname + ' ' + record.information?.personal.lastname}</u></strong></li>
                                            <li>Biometrics ID: <strong><u>${record.information?.biometrics_id ?? 'N/A'}</u></strong></li>
                                        </ul>
                                        <hr>
                                        <strong>Employee Clock In & Out</strong>
                                        <table class="table-auto my-3 border-collapse border border-gray-300 w-full">
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="border px-4 py-2">Clock In</th>
                                                    <th class="border px-4 py-2">Break Out</th>
                                                    <th class="border px-4 py-2">Break In</th>
                                                    <th class="border px-4 py-2">Clock Out</th>
                                                    <th class="border px-4 py-2">Is Late</th>
                                                    <th class="border px-4 py-2">Is Under Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="border px-4 py-2"><strong><u>${formatTime(clockIn)}</u></strong></td>
                                                    <td class="border px-4 py-2"><strong><u>${formatTime(breakOut)}</u></strong></td>
                                                    <td class="border px-4 py-2"><strong><u>${formatTime(breakIn)}</u></strong></td>
                                                    <td class="border px-4 py-2"><strong><u>${formatTime(clockOut)}</u></strong></td>
                                                    <td class="border px-4 py-2"><strong><u>${record.isLate ? 'Yes' : 'No'}</u></strong></td>
                                                    <td class="border px-4 py-2"><strong><u>${record.isUnderTime ? 'Yes' : 'No'}</u></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div>
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


