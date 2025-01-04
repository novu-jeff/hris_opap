<div>
    @php
        $monthNumeric = \Carbon\Carbon::parse($records['current']['month'].' 1')->format('m');
        $currentDate = \Carbon\Carbon::create($records['current']['year'], $monthNumeric, $records['current']['day'])->toDateString();
    @endphp
    <div class="text-center mb-3">
        <h2 class="mb-0 text-uppercase fw-bold">{{$records['current']['month'] . ' ' . $records['current']['day'] . ', ' . $records['current']['year']}}</h2>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-5">
        <a href="{{ route('timekeeping.index', ['year' => $records['previous']['year'], 'month' => $records['previous']['month'], 'day' => $records['previous']['day']]) }}" class="btn btn-info px-5 py-3 text-uppercase fw-bold">
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
            <a href="{{ route('timekeeping.index', ['year' => $records['next']['year'], 'month' => $records['next']['month'], 'day' => $records['next']['day']]) }}" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                Next Day
            </a>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-md-6 d-flex align-items-center gap-2">
            <label for="entries" class="form-label mb-0">Show entries:</label>
            <select id="entries" wire:model.live="entries" class="form-select w-auto">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
                <option value="60">60</option>
                <option value="70">70</option>
                <option value="80">80</option>
                <option value="90">90</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
            <label for="search" class="form-label mb-0">Search:</label>
            <input id="search" wire:model.live="search" type="text" class="form-control w-50" placeholder="Search something...">
        </div>
    </div>
    <table class="table table-striped w-100" id="logs-table">
        <thead>
            <tr>
                <th>Employee No</th>
                <th>BSD No.</th>
                <th>Employee Name</th>
                <th>Actions</th> 
            </tr>
        </thead>
        <tbody>
            @forelse ($timelogs as $key => $item)
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
            @empty
                <tr>
                    <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                </tr>            
            @endforelse
        </tbody>        
    </table>
    <div class="mt-4">
        {{ $timelogs->links(data: ['scrollTo' => false]) }}
    </div>
</div>
@section('script')
<script>
    $(function () {
        // Handle row click to toggle child content

        let currentLog;

        $('#logs-table tbody').on('click', '.view-log', function () {
            const tr = $(this).closest('tr'); // Get the closest table row
            const logId = tr.data('log-id'); // Retrieve log ID from the data attribute
            const logData = @json($timelogs->items()); // Use paginated items

            console.log(logData);
            console.log(logId);

            // Check if the clicked row is already shown
            const isRowShown = tr.hasClass('shown');

            // If the same row is clicked, just remove the child row and 'shown' class
            if (isRowShown) {
                tr.next('.child-row').remove(); // Remove child content
                tr.removeClass('shown'); // Remove 'shown' class from the row
            } else {
                // Hide other child rows and remove the 'shown' class from other rows
                $('#logs-table .child-row').remove();
                $('#logs-table tbody tr').removeClass('shown');

                try {
                    // Find the log entry with the matching ID
                    const log = logData[logId]; // Find the log by the logId

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
                        <div class="child-row mb-3">
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-5 mb-3">
                                    <strong>Employee Information:</strong>
                                    <ul class="my-3">
                                        <li>Employee No: <strong><u>${log.information?.employee_no ?? 'N/A'}</u></strong></li>
                                        <li>Employee Name: <strong><u>${log.information?.personal?.firstname ?? 'N/A'} ${log.information?.personal?.lastname ?? 'N/A'}</u></strong></li>
                                        <li>Biometrics ID: <strong><u>${log.bsd_no ?? 'N/A'}</u></strong></li>
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
                    tr.after(childContent); // Append content to the row
                    tr.addClass('shown'); // Add the 'shown' class to the row
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


        // Handle datePicker change
        $('#datePicker').on('change', function () {
            const date = $(this).val(); 
            
            // Split the date into year, month, and day
            const [year, month, day] = date.split('-');
            
            // Build the URL with the selected month, day, and year
            const url = '{{ route("timekeeping.index", ["year" => "__year__", "month" => "__month__", "day" => "__day__"]) }}'
                        .replace('__year__', year)
                        .replace('__month__', month)
                        .replace('__day__', day);

            // Redirect the user to the new URL
            location.href = url;
        });

        // Handle workSetup change
        $('#workSetup').on('change', function () {
            const selectedSetup = $(this).val();
            const url = new URL(window.location.href);

            if (selectedSetup) {
                url.searchParams.set('setup', selectedSetup);
            } else {
                url.searchParams.delete('setup');
            }

            window.location.href = url.toString();
        });
    });
</script>
@endsection
