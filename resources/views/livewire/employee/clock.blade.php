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
                        @if (in_array($status, ['Break Out']))
                            <div class="text-center mt-3">
                                <button style="border-radius: 15px" class="btn btn-primary border-3 w-100 py-3 text-uppercase fw-bold" wire:click="triggerClockOut(true)" wire:target="triggerClockOut">
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
                        {{ \Carbon\Carbon::now()->format('F, Y') }}
                    </h1>                    
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (!empty($logs))
                        <div class="accordion" id="logsAccordion">
                            @forelse($logs as $key => $item)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $key }}">
                                        <button class="accordion-button text-uppercase fw-bold {{ $key == 0 ? '' : 'collapsed' }}" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $key }}" 
                                                aria-expanded="{{ $key == 0 ? 'true' : 'false' }}" 
                                                aria-controls="collapse{{ $key }}">
                                                {{ \Carbon\Carbon::createFromFormat('d/m/Y', $item['date'])->format('j, l') }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $key }}" 
                                        class="accordion-collapse collapse {{ $key == 0 ? 'show' : '' }}" 
                                        aria-labelledby="heading{{ $key }}" 
                                        data-bs-parent="#logsAccordion">
                                        <div class="accordion-body">
                                            <table class="table table-bordered text-center">
                                                <thead>
                                                    <tr>
                                                        <th>Clock In</th>
                                                        <th>Break Out</th>
                                                        <th>Break In</th>
                                                        <th>Clock Out</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ isset($item['logs'][0]['time']) ? \Carbon\Carbon::parse($item['logs'][0]['time'])->format('h:i A') : '-' }}</td>
                                                        <td>{{ isset($item['logs'][1]['time']) ? \Carbon\Carbon::parse($item['logs'][1]['time'])->format('h:i A') : '-' }}</td>
                                                        <td>{{ isset($item['logs'][2]['time']) ? \Carbon\Carbon::parse($item['logs'][2]['time'])->format('h:i A') : '-' }}</td>
                                                        <td>{{ isset($item['logs'][3]['time']) ? \Carbon\Carbon::parse($item['logs'][3]['time'])->format('h:i A') : '-' }}</td>
                                                    </tr>
                                                    @php
                                                        $hasLocation = false;
                                                        $hasImage = false;
                                                    
                                                        // Check if any of the logs have a captured location or image
                                                        for ($i = 0; $i < 4; $i++) {
                                                            if (!empty($item['logs'][$i]['captured_location'])) {
                                                                $hasLocation = true;
                                                            }
                                                            if (!empty($item['logs'][$i]['captured_image'])) {
                                                                $hasImage = true;
                                                            }
                                                        }

                                                        $accomplishment = collect($item['logs'])->firstWhere('accomplishment');

                                                    @endphp
                                                    
                                                    @if($hasLocation)
                                                        <tr>
                                                            @for ($i = 0; $i < 4; $i++)
                                                                <td>
                                                                    {{ isset($item['logs'][$i]['captured_location']) ? $item['logs'][$i]['captured_location'] : 'N/A' }}
                                                                </td>
                                                            @endfor
                                                        </tr>
                                                    @endif
                                                    
                                                    @if($hasImage)
                                                        <tr>
                                                            @for ($i = 0; $i < 4; $i++)
                                                                <td>
                                                                    @if (!empty($item['logs'][$i]['captured_image']))
                                                                        <img src="{{ Storage::url('timelogs/' . $item['logs'][$i]['captured_image']) }}" 
                                                                            alt="logs" style="width: 100%; height: 100px; object-fit: cover">
                                                                    @else
                                                                        No Image
                                                                    @endif
                                                                </td>
                                                            @endfor
                                                        </tr>
                                                    @endif
                                                    @if(!empty($accomplishment))
                                                        <tr>
                                                            <td colspan="12">
                                                                <div class="text-start mt-2 pb-3 px-3">
                                                                    <p class="mb-2 fw-bold">Accomplishment Report:</p>
                                                                    <small>{{ $accomplishment['accomplishment'] }}</small>
                                                                </div>
                                                            </td>    
                                                        </tr>  
                                                    @endif                     
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

        Livewire.on('triggerClock', (data) => {
            if(data[0] == 'true') {
                Livewire.dispatch('triggerClock', [false]);
            }
        });

        Livewire.on('captureImage', (data) => {
           
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            
            const isNotBlank = hasContent(imageData);

            let isImageCaptured = isNotBlank;

            Livewire.dispatch('grabImage', [canvas.toDataURL('image/png'), data[0]['time'], isImageCaptured]);
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