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
                    <div class="col-12 col-md-4 mb-3">
                        <div 
                            class="card border-3 
                                {{ in_array($status, ['Clock In', 'Clock Out']) ? 'border-primary bg-primary text-white' : '' }} 
                                {{ in_array($status, ['Break In', 'Break Out']) ? 'border-secondary bg-secondary text-white' : '' }} 
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
                                    <div class="text-center fw-bold text-uppercase mt-4">
                                        <span wire:loading.remove wire:target="triggerClock">{{$status}}</span>
                                        <span wire:loading wire:target="triggerClock">Saving...</span>
                                    </div>
                                </div>
                            </div>      
                        </div>  
                        @if (in_array($status, ['Break In', 'Break Out']))
                            <div class="text-center mt-2">
                                <button style="border-radius: 15px" class="btn btn-primary border-3 w-100 py-3 text-uppercase fw-bold" wire:click="triggerClockOut" wire:target="triggerClockOut">
                                    Clock Out
                                </button>
                            </div>     
                        @endif       
                    </div>
                    <div class="col-12 col-md-4 mb-3">
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
                                    <div class="text-center fw-bold text-uppercase mt-4">
                                        <span wire:loading.remove wire:target="showLogs">Clock Logs</span>
                                        <span wire:loading wire:target="showLogs">Please Wait...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3" style="overflow: hidden">
                        <div class="camera d-flex justify-content-center align-items-center w-100">
                            <video id="video" autoplay></video>
                            <canvas id="canvas" class=""></canvas>
                            <div class="overlay">
                                Camera Capture Display
                            </div>
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
                        Clock Logs ( {{ \Carbon\Carbon::now()->format('F') }} )
                    </h1>                    
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (!empty($logs))
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th>Clock In</th>
                                        <th>Break Out</th>
                                        <th>Break In</th>
                                        <th>Clock Out</th>

                                        <th>Consumed AM</th>
                                        <th>Consumed PM</th>
                                        <th>Total Consumed</th>
                                        <th>OT Mins</th>
                                        <th>Over All Mins</th>
                                        <th>Accomplishment</th>

                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $key => $item)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('l') }}</td>
                                            <td>{{ $item->clock_in_am ? \Carbon\Carbon::parse($item->clock_in_am)->format('h:i A') : '' }}</td>
                                            <td>{{ $item->clock_out_am ? \Carbon\Carbon::parse($item->clock_out_am)->format('h:i A') : '' }}</td>
                                            <td>{{ $item->clock_in_pm ? \Carbon\Carbon::parse($item->clock_in_pm)->format('h:i A') : '' }}</td>
                                            <td>{{ $item->clock_out_pm ? \Carbon\Carbon::parse($item->clock_out_pm)->format('h:i A') : '' }}</td>
                                            <td>{{ $item->mins_consumed_am ? \Carbon\CarbonInterval::minutes($item->mins_consumed_am)->cascade()->format('%h hours %i minutes') : '' }}</td>
                                            <td>{{ $item->mins_consumed_pm ? \Carbon\CarbonInterval::minutes($item->mins_consumed_pm)->cascade()->format('%h hours %i minutes') : '' }}</td>
                                            <td>{{ $item->total_mins_consumed ? \Carbon\CarbonInterval::minutes($item->total_mins_consumed)->cascade()->format('%h hours %i minutes') : '' }}</td>
                                            <td>{{ $item->mins_ot ? \Carbon\CarbonInterval::minutes($item->mins_ot)->cascade()->format('%h hours %i minutes') : '' }}</td>
                                            <td>{{ $item->overall_mins ? \Carbon\CarbonInterval::minutes($item->overall_mins)->cascade()->format('%h hours %i minutes') : '' }}</td>
                                            <td>{{ $item->accomplishment }} </td>
                                            <td></td>
                                        </tr>
                                    @empty
                                        
                                    @endforelse
                                </tbody>
                            </table>
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
                    console.error("Error accessing camera: ", err);
                    Swal.fire({
                        title: 'Oops',
                        text: 'Camera access was denied or not supported.',
                        icon: 'error',
                    });
                });
        } else {
            console.error("getUserMedia not supported.");
            Swal.fire({
                title: 'Oops',
                text: 'getUserMedia not supported',
                icon: 'error',
            });
        }

        Livewire.on('capture', (data) => {
           
            const isForcedClockOut = data[0].isForcedClockout ? true : false;

            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            
            const isNotBlank = hasContent(imageData);

            isImageCaptured = isNotBlank;

            @this.call('processClock', canvas.toDataURL('image/png'), isImageCaptured, isForcedClockOut);
        
        });

        function hasContent(imageData) {
            for (let i = 0; i < imageData.data.length; i += 4) {
                const r = imageData.data[i];     
                const g = imageData.data[i + 1];
                const b = imageData.data[i + 2]; 
                const a = imageData.data[i + 3];

                if (a > 0 && (r > 0 || g > 0 || b > 0)) {
                    return true;
                }
            }
            return false;
        }
        
});

</script>

@endsection