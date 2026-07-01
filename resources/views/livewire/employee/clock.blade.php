<div>
    <div class="clockinout">
        <div class="row">
            <!-- <div class="col-12 col-md-4 mb-5">
                <label for="user" class="mb-3">Manipulate time for testing</label>
                <input type="time" class="form-control" wire:model="manipulate_timestamp">
                <button wire:click="delete" class="btn btn-danger mt-3">Delete Record</button>
            </div> -->
            <div class="col-12 col-md-12 mb-3 mb-3">
                <div class="row capture-content">
                    <div class="col-12 col-md-5">
                        <div class="row">
                    
                            {{-- Clock In / Clock Out --}}
                            <div class="col-12 mb-3">
                                @php
                                    $hideClockCard = $hideClockInDueToExternalLog;
                                @endphp
                    
                                @if(!$hideClockCard)
                                    <div
                                        data-status="{{ $status }}"
                                        id="clockActionCard"
                                        class="clock-process card border-3
                                            {{ in_array($status, ['Clock In', 'Clock Out']) ? 'border-primary bg-primary text-white' : '' }}
                                            {{ in_array($status, ['Lunch In', 'Lunch Out']) ? 'border-secondary bg-secondary text-white' : '' }}
                                            {{ $status === 'Done' ? 'border-danger bg-danger text-white' : '' }}"
                                    >
                                        <div class="card-body d-flex align-items-center">
                                            <div>
                    
                                                <div class="d-flex justify-content-center">
                                                    <span class="clock-label"></span>
                    
                                                    <span class="clock-loading d-none">
                                                        <i class="fa-solid fa-spinner fa-spin"></i>
                                                    </span>
                                                </div>
                    
                                                <div class="text-center fw-bold text-uppercase mt-1">
                                                    <span class="clock-label">
                                                        {{ $status }}
                                                    </span>
                    
                                                    <span class="clock-loading d-none">
                                                        Processing...
                                                    </span>
                                                </div>
                    
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-center small text-muted mt-2 mb-0">
                                        Attendance already recorded today (device/biometric). Web clocking is disabled.
                                    </p>
                                @endif
                            </div>
                    
                            {{-- ALWAYS SHOW CLOCK LOGS --}}
                            <div class="col-12 mb-3">
                                <div
                                    class="card border-3 bg-dark text-white w-100"
                                    wire:click="showLogs"
                                    wire:target="showLogs">
                    
                                    <div class="card-body d-flex align-items-center">
                                        <div>
                    
                                            <div class="d-flex justify-content-center">
                                                <span wire:loading.remove wire:target="showLogs">
                                                    <i class="fa-regular fa-calendar-check"></i>
                                                </span>
                    
                                                <span wire:loading wire:target="showLogs">
                                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                                </span>
                                            </div>
                    
                                            <div class="text-center fw-bold text-uppercase mt-1">
                                                <span wire:loading.remove wire:target="showLogs">
                                                    Clock Logs
                                                </span>
                    
                                                <span wire:loading wire:target="showLogs">
                                                    Please Wait...
                                                </span>
                                            </div>
                    
                                        </div>
                                    </div>
                    
                                </div>
                            </div>
                    
                        </div>
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="camera d-flex justify-content-center align-items-center w-100">
                            <video id="video" autoplay></video>
                            <canvas id="canvas"></canvas>
                            <div class="alert-container">
                                
                            </div>
                            <div class="watermark">
                                <img src="{{ asset('/img/' . $provider['client_logo']) }}">            
                            </div>
                            
                        </div>
                        <div class="text-muted text-center text-muted text-uppercase mt-3 fst-italic">
                            <small>Please ensure your face is clearly visible before proceeding.</small>
                            @if($geofenceActive)
                                <br><small class="text-warning">Clock In requires GPS: you must be within {{ (int) round($geofenceRadiusM) }} m of the office.</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row capture-preview">
                    <div class="col-12">
                        <img src="" alt="" srcset="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" wire:ignore.self id="logs_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">
                        @foreach($availableMonths as $month)

<a
    href="{{ route('employee.accomplishment.download.monthly', [
        'year' => $month->year,
        'month' => $month->month,
    ]) }}"
    class="btn btn-success mb-2">

    {{ \Carbon\Carbon::create($month->year, $month->month, 1)->format('F Y') }}

</a>

@endforeach
                    </h1> 
                                      
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-end mb-3">
                      
                    </div>
                
                    @if (!empty($logs))
                        <div class="accordion" id="logsAccordion">
                            @foreach($logs as $item)
                                @php
                                    $dateKey = str_replace('/', '-', $item['date']);
                                    $logList = $item['logs'] ?? [];
                                   // $accomplishment = collect($logList)->firstWhere('accomplishment');
                                    $accomplishment = collect($logList)->last();
                                    $hasImage = collect($logList)->contains(fn($log) => !empty($log['captured_image']));
                                @endphp

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $dateKey }}">
                                        <button class="accordion-button text-uppercase fw-bold {{ $loop->first ? '' : 'collapsed' }}"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $dateKey }}"
                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                aria-controls="collapse{{ $dateKey }}">
                                                {{ \Carbon\Carbon::createFromFormat('j/n/Y', $item['date'])->format('d, l') }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $dateKey }}"
                                        class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                        aria-labelledby="heading{{ $dateKey }}"
                                        data-bs-parent="#logsAccordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                            <table class="table table-bordered text-center">
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
                                                            {{ isset($logList[0]['time']) ? \Carbon\Carbon::parse($logList[0]['time'])->format('h:i A') : '-' }}
                                                        </td>
                                                        <td>
                                                            {{ isset($logList[1]['time']) ? \Carbon\Carbon::parse($logList[1]['time'])->format('h:i A') : '-' }}
                                                        </td>
                                                        <td>
                                                            {{ isset($logList[2]['time']) ? \Carbon\Carbon::parse($logList[2]['time'])->format('h:i A') : '-' }}
                                                        </td>
                                                        <td>
                                                            {{ isset($logList[3]['time']) ? \Carbon\Carbon::parse($logList[3]['time'])->format('h:i A') : '-' }}
                                                        </td>
                                                    </tr>

                                                    @if($hasImage)
                                <tr>
                                    @for ($i = 0; $i < 4; $i++)
                                        <td>
                                            @if (!empty($logList[$i]['captured_image']))
                                                @php
                                                    $logImage = 'timelogs/' . $logList[$i]['captured_image'];
                                                @endphp
                                                <a style="cursor: pointer" data-fancybox data-src="{{ Storage::disk('public')->url($logImage) }}">
                                                    <img src="{{ Storage::disk('public')->url($logImage) }}"
                                                        alt="log image"
                                                        style="width: 100%; height: 100px; object-fit: cover;">
                                                </a>
                                            @else
                                                No Image
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                                                    @endif

                                                    @if(!empty($accomplishment))
                                                    <tr>
                                                        <td colspan="4">
                                                            <div class="text-start p-3">
                                                    
                                                                <h6 class="fw-bold mb-3">
                                                                    <i class="fa-solid fa-clipboard-check me-2"></i>
                                                                    Daily Accomplishment
                                                                </h6>
                                                    
                                                                {{-- Accomplishment Type --}}
                                                                @if(!empty($accomplishment['accomplishment_type']))
                                                                    <div class="mb-2">
                                                                        <strong>Type:</strong>
                                                                        <span class="badge bg-primary">
                                                                            {{ $accomplishment['accomplishment_type'] }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                    
                                                                {{-- Accomplishment Details --}}
                                                                @if(!empty($accomplishment['accomplishment_details']))
                                                                    <div class="mb-3">
                                                                        <strong>Details:</strong>
                                                                        <div class="border rounded p-2 bg-light text-dark mt-1">
                                                                            {{ $accomplishment['accomplishment_details'] }}
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                        @if(
                                                            !empty($accomplishment['accomplishment']) ||
                                                            !empty($accomplishment['accomplishment_type']) ||
                                                            !empty($accomplishment['accomplishment_details'])
                                                        )

                                                        <a
                                                            href="{{ route('employee.accomplishment.download', [
                                                                'employee' => $item['bsd_no'],
                                                                'date' => \Carbon\Carbon::createFromFormat('j/n/Y', $item['date'])->format('Y-m-d')
                                                            ]) }}"
                                                            class="btn btn-primary btn-sm">

                                                            <i class="fa-solid fa-download"></i>
                                                            Download Accomplishment Report

                                                        </a>

                                                        @endif


                                                    
                                                                {{-- Uploaded PDF --}}
                                                                @php
                                                                    $accomplishmentFile = $accomplishment['accomplishment'] ?? null;
                                                    
                                                                    $accomplishmentPath = $accomplishmentFile
                                                                        ? 'accomplishments/' . $accomplishmentFile
                                                                        : null;
                                                    
                                                                    $accomplishmentExists = $accomplishmentPath
                                                                        ? Storage::disk('public')->exists($accomplishmentPath)
                                                                        : false;
                                                                @endphp
                                                    
                                                                @if($accomplishmentFile)
                                                    
                                                                    @if($accomplishmentExists)
                                                    
                                                                        <div class="alert alert-success d-flex justify-content-between align-items-center mt-3 mb-0">
                                                    
                                                                            <div>
                                                                                <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                    
                                                                                <strong>
                                                                                    {{ $accomplishmentFile }}
                                                                                </strong>
                                                                            </div>
                                                    
                                                                            <a
                                                                                href="{{ Storage::disk('public')->url($accomplishmentPath) }}"
                                                                                class="btn btn-sm btn-primary"
                                                                                download>
                                                    
                                                                                <i class="fa-solid fa-download me-1"></i>
                                                                                Download
                                                    
                                                                            </a>
                                                    
                                                                        </div>
                                                    
                                                                    @else
                                                    
                                                                        <div class="alert alert-warning mt-3 mb-0">
                                                                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                                    
                                                                            Accomplishment file not found.
                                                                        </div>
                                                    
                                                                    @endif
                                                    
                                                                @endif
                                                    
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                
                                                    
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">Currently no clock logs.</div>
                    @endif
                </div>                
            </div>
        </div>
    </div>



    <div class="modal fade" wire:ignore.self id="clockInModal" tabindex="-1" aria-labelledby="clockInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
            <form wire:submit.prevent="triggerClock" enctype="multipart/form-data">

                <div class="modal-header border-0 pt-2 pb-0">
                    <h5 class="modal-title text-uppercase fw-bold" id="clockInModalLabel">Captured Image Preview</h5>
                </div>

                <div class="modal-body">
                    {{-- Captured image preview --}}
                    <div class="mb-3" wire:ignore>
                        <img id="clockInPreviewImage" src="" alt="Captured Image" class="img-fluid rounded shadow">
                    </div>

                    @if($status === 'Clock Out' || $isForcedOut)

                    <div class="mb-3"><div class="mb-3">
                        <label class="form-label fw-bold">
                            Daily Accomplishment
                        </label>
                    
                        <select
                            wire:model.defer="accomplishment_type"
                            wire:change="$refresh"
                            class="form-control">
                            <option value="">
                                 Select Accomplishment
                            </option>
                            <option value="Upload Accomplishment Report">
                                Upload Accomplishment Report
                            </option>
                            @foreach($accomplishmentOptions as $option)Upload Accomplishment Report
                                <option value="{{ $option }}">
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>

                        @if($accomplishment_type === 'Others')
                            <div class="mb-3">
                                <label class="form-label">
                                    Specify Accomplishment
                                </label>

                                <textarea
                                    wire:model="accomplishment_details"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Enter accomplishment details">
                                </textarea>
                            </div>
                            @endif
                    
                        @error('accomplishment_type')
                            <span class="text-danger">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>


                        {{-- Accomplishment file input --}}
                        @if($accomplishment_type === 'Upload Accomplishment Report')   
                        <div class="mb-3">
                            <label for="accomplishmentFile" class="text-start">Accomplishment Report</label>
                            <input
                                type="file"
                                wire:model="upload_accomplishment"
                                id="accomplishmentFile"
                                class="form-control"
                                accept="application/pdf"
                            />
                            <small class="text-muted d-block mt-1">PDF only, max 5 MB. Click Proceed right after selecting to avoid upload timeout.</small>

                            @error('upload_accomplishment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror


                            {{-- Temporary preview link --}}
                            @if($upload_accomplishment)
                                <p class="mt-2">
                                    Selected File: 
                                    
                                      <strong>{{ $upload_accomplishment->getClientOriginalName() }}</strong>
                                  
                                </p>
                            @endif
                        </div>
                    @endif
                @endif  
                </div>

                <div wire:ignore class="modal-footer border-0 d-flex gap-2 justify-content-between align-items-center">
                    <button type="button" class="retakeButton btn btn-danger py-3 px-5 text-uppercase fw-bold" data-bs-dismiss="modal">
                        Retake
                    </button>

                    <button type="submit"
                        class="btn btn-primary py-3 px-5 text-uppercase fw-bold d-flex align-items-center gap-2"
                        wire:target="{{ $status === 'Clock Out' || $isForcedOut ? 'saveAccomplishment' : 'triggerClock' }}"
                        wire:loading.attr="disabled">
                        <span>Proceed</span>
                        <span wire:loading wire:target="{{ 'triggerClock' }}">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

</div>

<script type="module">
    $(function() {
        initializeClockFace();
    })

    let clockProcessing = false;

$(document).on('click', '.clock-process', function (e) {

    if (clockProcessing) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }

    clockProcessing = true;

    const card = $('#clockActionCard');

    card.addClass('clock-process-disabled');

    $('.clock-label').addClass('d-none');
    $('.clock-loading').removeClass('d-none');
});

window.addEventListener('reset-clock-button', function () {

    clockProcessing = false;

    const card = $('#clockActionCard');

    card.removeClass('clock-process-disabled');

    $('.clock-label').removeClass('d-none');
    $('.clock-loading').addClass('d-none');
});
</script>


