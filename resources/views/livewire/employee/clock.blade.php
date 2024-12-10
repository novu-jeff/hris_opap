<div>
    <div class="clockinout">
        <div class="row">
            <div class="col-12 col-md-12 mb-3 mb-3">
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <div 
                            class="card border-3 
                                {{ in_array($status, ['Clock In', 'Clock Out']) ? 'border-primary bg-primary text-white' : '' }} 
                                {{ in_array($status, ['Break In', 'Break Out']) ? 'border-secondary bg-secondary text-white' : '' }} 
                                {{ $status === 'Done' ? 'border-danger bg-danger text-white' : '' }}" 
                            wire:click="triggerClock">
                            <div class="card-body d-flex align-items-center">
                                <div>
                                    <div class="d-flex justify-content-center">
                                        <i class="fa-regular fa-circle-check"></i>
                                    </div>
                                    <div class="text-center mt-3">
                                        {{$status}}
                                    </div>
                                </div>
                            </div>      
                        </div>              
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="card border-3 bg-dark text-white w-100" wire:click="showLogs">
                            <div class="card-body d-flex align-items-center">
                                <div>
                                    <div class="d-flex justify-content-center">
                                        <i class="fa-regular fa-calendar-check"></i>
                                    </div>
                                    <div class="text-center mt-3">
                                        Clock Logs
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
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Daily Time Record</h1>
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
                                        <th>Regular Hours</th>
                                        <th>OT Hours</th>
                                        <th>Total Hours</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
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
                                            <td>{{ $item->total_mins_consumed ? sprintf('%02d:%02d', floor($item->total_mins_consumed / 60), $item->total_mins_consumed % 60) . ' HRS' : '' }}</td>
                                            <td>{{ $item->mins_ot ? sprintf('%02d:%02d', floor($item->mins_ot / 60), $item->mins_ot % 60) . ' HRS' : '' }}</td>
                                            <td>{{ $item->overall_mins ? sprintf('%02d:%02d', floor($item->overall_mins / 60), $item->overall_mins % 60) . ' HRS' : '' }}</td>
                                            <td></td>
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

        Livewire.on('capture', () => {
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            
            const isNotBlank = hasContent(imageData);

            isImageCaptured = isNotBlank;

            @this.call('processClock', canvas.toDataURL('image/png'), isImageCaptured);
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