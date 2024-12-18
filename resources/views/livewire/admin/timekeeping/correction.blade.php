<div>
    @php
        $monthNumeric = \Carbon\Carbon::parse($records['current']['month'].' 1')->format('m');
        $currentDate = \Carbon\Carbon::create($records['current']['year'], $monthNumeric, $records['current']['day'])->toDateString();
    @endphp
    <div class="text-center mb-3">
        <h2 class="mb-0 text-uppercase fw-bold">{{$records['current']['month'] . ' ' . $records['current']['day'] . ', ' . $records['current']['year']}}</h2>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-5">
        <a href="{{ route('timekeeping.correction', ['year' => $records['previous']['year'], 'month' => $records['previous']['month'], 'day' => $records['previous']['day']]) }}" class="btn btn-info px-5 py-3 text-uppercase fw-bold">
            Previous Day
        </a>
        <div class="d-flex gap-2">
            <input type="date" id="datePicker" class="form-control" style="width: 300px" value="{{$currentDate}}">
            <select id="workSetup" class="form-select" style="width: 300px">
                <option value=""> - CHOOSE WORK SETUP -</option>
                <option value="all" {{empty($setup) ?? '' }}>All</option>
                <option value="onsite" {{$setup == 'onsite' ? 'selected' : '' }}>On-Site</option>
                <option value="wfh" {{$setup == 'wfh' ? 'selected' : '' }}>Work From Home</option>
            </select>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('timekeeping.correction', ['year' => $records['next']['year'], 'month' => $records['next']['month'], 'day' => $records['next']['day']]) }}" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                Next Day
            </a>
        </div>
    </div>
    <table class="table table-striped w-100" id="logs-table">
        <thead>
            <tr>
                <th>Employee No</th>
                <th>BSD No.</th>
                <th>Employee Name</th>
                <th>Actions</th> <!-- Added actions column for "View" button -->
            </tr>
        </thead>
        <tbody>
            @foreach ($records['data'] as $key => $item)
                @php
                    // Check if the condition is met (avoid repeating the logic)
                    $highlightBG = ($item->origin == 'web' && $setup == 'wfh') || ($item->origin == 'biometrics' && $setup == 'onsite') ? '#014959' : null;
                    $highlightColor = ($item->origin == 'web' && $setup == 'wfh') || ($item->origin == 'biometrics' && $setup == 'onsite') ? '#fff' : null;
                @endphp
                <tr data-log-id="{{ $key }}" class="fw-bold" style="background-color: {{ $highlightBG }}; color: {{ $highlightColor }}">
                    <!-- Display Employee No, BSD No., and Employee Name -->
                    <td style="color:{{$highlightColor}};background-color: {{ $highlightBG }}">{{ $item->information->employee_no ?? '' }}</td>
                    <td style="color:{{$highlightColor}};background-color: {{ $highlightBG }}">{{ $item->bsd_no ?? '' }}</td>
                    <td style="color:{{$highlightColor}};background-color: {{ $highlightBG }}">
                        {{ optional(optional($item->information)->personal)->firstname . ' ' . optional(optional($item->information)->personal)->lastname ?? '' }}
                    </td>                    
                    <td style="color:{{$highlightColor}};background-color: {{ $highlightBG }}">
                        <button class="view-log btn btn-primary px-3 text-uppercase fw-medium">View</button>
                    </td>
                </tr>               
            @endforeach
        </tbody>        
    </table>
    
</div>
@section('script')
    <script>
        $(function() {

            // Check if DataTable is already initialized, if so, destroy it to reinitialize
            if ($.fn.DataTable.isDataTable('#logs-table')) {
                $('#logs-table').DataTable().destroy();
            }

            // Initialize DataTable
            const table = $('#logs-table').DataTable({
                responsive: true,
                pageLength: 20 ,
                order: [],
            });

            // Handle row click to toggle child content
            $('#logs-table tbody').on('click', '.view-log', function () {
                const tr = $(this).closest('tr'); // Get the closest table row
                const row = table.row(tr); // Access the DataTable row object

                // Hide all other child rows
                table.rows().every(function () {
                    if (this.child.isShown() && !$(this.node()).is(tr)) {
                        this.child.hide();
                        $(this.node()).removeClass('shown');
                    }
                });

                if (row.child.isShown()) {
                    // If child row is already shown, hide it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    const logId = tr.data('log-id'); // Retrieve log ID from the data attribute
                    const logData = @json($records['data']); // Parse the JSON data from PHP

                    try {
                        // Find the log entry with the matching ID
                        const log = logData[logId];
                        if (!log) {
                            throw new Error('Log data not found or invalid.');
                        }

                        // Get the log's clock-in, break-out, break-in, and clock-out times
                        const clockIn = log.clock_in_am;
                        const breakOut = log.clock_out_am;
                        const breakIn = log.clock_in_pm;
                        const clockOut = log.clock_out_pm;

                        // Format the captured images if available
                        const clockInImage = log.captured_image_clockin ? `{{ asset('storage/clockinout/') }}/${log.captured_image_clockin}` : 'https://placehold.co/300x150.png?text=No+Image';
                        const clockOutImage = log.captured_image_clockout ? `{{ asset('storage/clockinout/') }}/${log.captured_image_clockout}` : 'https://placehold.co/300x150.png?text=No+Image';

                        // Check if images are available for display
                        const showCapturedImages = log.captured_image_clockin || log.captured_image_clockout;

                        // Generate the content for the child row in the requested format
                        const childContent = `
                            <div class="mb-3">
                                <hr>
                                <div class="row">
                                    <div class="col-12 col-md-5 mb-3">
                                        <strong>Employee Information:</strong>
                                        <ul class="my-3">
                                            <li>Employee No: <strong><u>${log.information.employee_no ?? 'N/A'}</u></strong></li>
                                            <li>Employee Name: <strong><u>${log.information?.personal?.firstname ?? 'N/A'} ${log.information?.personal?.lastname ?? 'N/A'}</u></strong></li>
                                            <li>Biometrics ID: <strong><u>${log.information?.bsd_no ?? 'N/A'}</u></strong></li>
                                        </ul>
                                    </div>
                                    <div class="col-12 col-md-7 mb-3">
                                        <div class="d-flex gap-3">
                                            ${showCapturedImages ? `
                                                <div class="mb-4">
                                                    <p class="fw-bold">Captured Clock In:</p>
                                                    <img src="${clockInImage}" alt="Clock In Image" style="width: 300px; height: 150px; object-fit: cover">
                                                </div>
                                                <div class="mb-4">
                                                    <p class="fw-bold">Captured Clock Out:</p>
                                                    <img src="${clockOutImage}" alt="Clock Out Image" style="width: 300px; height: 150px; object-fit: cover">
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12">
                                         <hr>
                                        <strong>Employee Clock In & Out</strong>
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-danger px-3 text-uppercase fw-medium" id="applyCorrectionLink" data-id="${log.id}">Apply Correction</a>
                                        </div>
                                        <table class="table-auto my-3 border-collapse border border-gray-300 w-full">
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="border px-4 py-2">Clock In</th>
                                                    <th class="border px-4 py-2">Break Out</th>
                                                    <th class="border px-4 py-2">Break In</th>
                                                    <th class="border px-4 py-2">Clock Out</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="border px-4 py-2"><strong><u>${clockIn ?? ''}</u></strong></td>
                                                    <td class="border px-4 py-2"><strong><u>${breakOut ?? ''}</u></strong></td>
                                                    <td class="border px-4 py-2"><strong><u>${breakIn ?? ''}</u></strong></td>
                                                    <td class="border px-4 py-2"><strong><u>${clockOut ?? ''}</u></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Show the child row with the generated content
                        row.child(childContent).show();
                        tr.addClass('shown');

                        $(document).on('click', '#applyCorrectionLink', function(e) {
                            e.preventDefault(); 
                            const log_id = $(this).data('id');
                            const url = '{{ route("timekeeping.correction-apply", ["id" => "__id__"]) }}'.replace('__id__', log_id);
                            window.open(url, '_blank'); 
                        });

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


            $('#datePicker').on('change', function () {
                const date = $(this).val(); 
                
                // Split the date into year, month, and day
                const [year, month, day] = date.split('-');
                
                // Build the URL with the selected month, day, and year
                const url = '{{ route("timekeeping.correction", ["year" => "__year__", "month" => "__month__", "day" => "__day__"]) }}'
                            .replace('__year__', year)
                            .replace('__month__', month)
                            .replace('__day__', day);

                // Redirect the user to the new URL
                location.href = url;
            });

            $('#workSetup').on('change', function () {
                const selectedSetup = $(this).val();
                const url = new URL(window.location.href);

                // Add or update the 'setup' query parameter in the URL
                if (selectedSetup) {
                    url.searchParams.set('setup', selectedSetup);
                } else {
                    url.searchParams.delete('setup');
                }

                // Redirect to the updated URL
                window.location.href = url.toString();
            });

        });
    </script>
@endsection