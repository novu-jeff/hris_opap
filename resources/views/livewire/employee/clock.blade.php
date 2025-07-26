<div>
    <div class="clockinout">
        <div class="row">
            <div class="col-12 col-md-4 mb-5">
                <label for="user" class="mb-3">Manipulate time for testing</label>
                <input type="time" class="form-control" wire:model="manipulate_timestamp">
                <button wire:click="delete" class="btn btn-danger mt-3">Delete Record</button>
            </div>
            <div class="col-12 col-md-12 mb-3 mb-3">
                <div class="row">
                    <div class="col-12 col-md-5">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div 
                                    class="clock-process card border-3 
                                        {{ in_array($status, ['Clock In', 'Clock Out']) ? 'border-primary bg-primary text-white' : '' }} 
                                        {{ in_array($status, ['Lunch In', 'Lunch Out']) ? 'border-secondary bg-secondary text-white' : '' }} 
                                        {{ $status === 'Done' ? 'border-danger bg-danger text-white' : '' }}" 
                                    wire:click="triggerClock" wire:target="triggerClock">
                                    <div class="card-body d-flex align-items-center">
                                        <div>
                                            <div class="d-flex justify-content-center">
                                                <span wire:loading.remove wire:target="triggerClock">
                                                    <i class="fa-regular fa-circle-check"></i>
                                                </span>
                                                <span wire:loading wire:target="triggerClock">
                                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                                </span>
                                            </div>
                                            <div class="text-center fw-bold text-uppercase mt-1">
                                                <span wire:loading.remove wire:target="triggerClock">{{$status}}</span>
                                                <span wire:loading wire:target="triggerClock">Saving...</span>
                                            </div>
                                        </div>
                                    </div>      
                                </div>  
                                @if (in_array($status, ['Lunch Out']))
                                    <div class="text-center mt-3">
                                        <button style="border-radius: 15px" class="btn btn-primary border-3 w-100 py-3 text-uppercase fw-bold" wire:click="triggerClockOut(true)" wire:target="triggerClockOut">
                                            Clock Out
                                        </button>
                                    </div>     
                                @endif       
                            </div>
                            <div class="col-12 mb-3">
                                <div class="card border-3 bg-dark text-white w-100" wire:click="showLogs" wire:target="showLogs">
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
                                                <span wire:loading.remove wire:target="showLogs">Clock Logs</span>
                                                <span wire:loading wire:target="showLogs">Please Wait...</span>
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
                            <div class="watermark">
                                <img src="{{asset('img/logo.png')}}" alt="watermark">
                            </div>
                            <div class="overlay py-3">
                                <div class="{{ $gps_location ? 'd-block' : 'd-none' }}">
                                    <div class="map-container" wire:ignore>
                                        <div id="map"></div>
                                    </div>
                                </div>
                                <div class="details p-3 d-flex align-items-center">
                                    <div id="location-info">
                                        @if($gps_location)
                                            @php
                                                $place = $gps_location['place'] ?? 'Unknown';
                                                $lat = $gps_location['coordinates']['lat'] ?? 0;
                                                $lng = $gps_location['coordinates']['lng'] ?? 0;
                                            @endphp
                                            <div class="mb-0">{{ $place }}</div>
                                            <div class="mb-0">Lat: {{ number_format($lat, 5) }}°, Long: {{ number_format($lng, 5) }}°</div>
                                            <div class="mb-0">{{ now()->toDayDateTimeString() }}</div>
                                        @else
                                            <div class="text-nowrap">Locating, Please Wait... <i class="ms-2 fa-solid fa-spinner fa-spin"></i></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted text-center text-muted text-uppercase mt-3 fst-italic">
                            <small>Make sure your location is visible in the frame before proceeding.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" wire:ignore.self id="logs_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">
                        {{ \Carbon\Carbon::now()->format('F Y') }}
                    </h1>                    
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (!empty($logs))
                        <div class="accordion" id="logsAccordion">
                            @forelse($logs as $date => $item)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $date }}">
                                        <button class="accordion-button text-uppercase fw-bold {{ $loop->first  ? '' : 'collapsed' }}" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $date }}" 
                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                                                aria-controls="collapse{{ $date }}">
                                                {{ \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format(format: 'd, l') }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $date }}" 
                                        class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                        aria-labelledby="heading{{ $date }}" 
                                        data-bs-parent="#logsAccordion">
                                        <div class="accordion-body">
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
                                                        <td>{{ isset($item['clock_in']) ? \Carbon\Carbon::parse($item['clock_in'])->format('h:i A') : '-' }}</td>
                                                        <td>{{ isset($item['lunch_in']) ? \Carbon\Carbon::parse($item['lunch_in'])->format('h:i A') : '-' }}</td>
                                                        <td>{{ isset($item['lunch_out']) ? \Carbon\Carbon::parse($item['lunch_out'])->format('h:i A') : '-' }}</td>
                                                        <td>{{ isset($item['clock_out']) ? \Carbon\Carbon::parse($item['clock_out'])->format('h:i A') : '-' }}</td>
                                                    </tr>
                                                          
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-uppercase fw-bold text-muted">No logs for this month</div>
                            @endforelse
                        </div>                    
                    @else
                        <div class="alert alert-info mb-0">Currently no clock logs.</div>
                    @endif
                </div>                
            </div>
        </div>
    </div>
</div>

@section('script')
 
<script>


   $(function() { 


        locateMe();

        async function locateMe() {
            try {
                const {lat, lng} = await getGPSCoordinates();
                Livewire.dispatch('getLocation', { lat, lng }); 
            } catch (error) {
                console.error(error);
            }
        }

        Livewire.on('loadMap', (event) => {
            const { token, lng, lat } = event[0];
            setupMap(token, [lng, lat]);
        });

        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');
        let isImageCaptured = false;

        video.addEventListener('loadedmetadata', () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
        });

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then((stream) => {
                    video.srcObject = stream;
                })
                .catch((err) => {
                    Swal.fire({
                        title: 'Please be informed',
                        text: 'Camera and location access are required to continue. Please ensure both are enabled in your device settings before proceeding.',
                        icon: 'info',
                    });
                });
        } else {
            Swal.fire({
                title: 'Please be informed',
                text: 'Camera and location access are required to continue. Please ensure both are enabled in your device settings before proceeding.',
                icon: 'info',
            });
        }
        
        
        
});

</script>

@endsection