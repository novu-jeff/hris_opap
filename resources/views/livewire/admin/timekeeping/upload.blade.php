<div>
    <label class="mb-2" for="file">File Upload</label>
    <input type="file" wire:model="file" id="file" class="form-control" wire:loading.attr="disabled" wire:target="upload_file">
    <div class="mt-2 text-muted fw-bold text-uppercase d-flex justify-content-between align-items-center" style="font-size: 13px">
        <small>Note: only csv files are allowed.</small>
    </div>
    <div wire:loading wire:target="file" class="mt-2 text-center text-muted">
        <p>Please Wait... <i class="fa-solid fa-spinner fa-spin"></i></p>
    </div>
    @error('file') 
        <div class="error-field mt-3">
            @error('file') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    @enderror
    <div class="mt-3">
        @if($upload_preview)
            File Ready to import: <a href="{{$upload_preview}}">{{$upload_preview}}</a>
        @endif
    </div>
    @if($upload_preview)
        <div class="mt-4 d-flex justify-content-end">
            <button class="btn btn-primary px-5 py-3 text-uppercase fw-bold" 
                    wire:click="upload_file"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>Upload File</span>
                <span wire:loading wire:target="upload_file">Importing <i class="fa-solid fa-spinner fa-spin"></i></span>
            </button>
        </div>
    @endif
    <div class="w-100 mt-5" wire:loading wire:target="upload_file">
        <div class="alert alert-danger d-flex justify-content-center gap-3 align-items-center" role="alert">
            <i class="fa-solid fa-triangle-exclamation fs-5"></i>
            <div class="text-uppercase fw-bold">
                Please do not close or reload the page to prevent errors during the upload process.
            </div>
        </div>  
    </div> 

    <div class="modal" id="modal-loading" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="loading-spinner mb-2"></div>
                    <div id="progress-text">Uploading...</div> 
                    <button id="cancel-job" wire:click="cancelUpload" class="btn btn-danger btn-sm mt-2">Cancel Upload</button>
                </div>
            </div>
        </div>
    </div>
    <style>
        .loading-spinner{
            width:30px;
            height:30px;
            border:2px solid indigo;
            border-radius:50%;
            border-top-color:#0001;
            display:inline-block;
            animation:loadingspinner .7s linear infinite;
            }
            @keyframes loadingspinner{
            0%{
                transform:rotate(0deg)
            }
            100%{
                transform:rotate(360deg)
            }
        }           
    </style>
</div>


@section('script')
    <script>
        $(document).ready(function() {
            Livewire.on('isLoading', (event) => {
                console.log(event);
                let batchId = event[0] ?? "";

                if (!batchId) {
                    console.warn("No batch ID found. Aborting job progress check.");
                    return;
                }

                // Ensure modal is initialized and shown
                setTimeout(() => {
                    $('#modal-loading').modal('show');
                }, 1000);

                function checkJobProgress(batchId) {
                    $.ajax({
                        url: `/admin/timekeeping/upload/job/${batchId}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {

                            if (response.progress !== undefined) {
                                $('#progress-text').text(`Uploading... ${response.progress}%`);

                                if (response.progress < 100) {
                                    setTimeout(() => checkJobProgress(batchId), 30); // Poll every 2 sec
                                } else {
                                    location.reload();
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error("Error fetching job progress:", xhr);
                            modal.modal('hide'); // Hide modal on error
                        }
                    });
                }

                // Start job progress check
                setTimeout(() => checkJobProgress(batchId), 100); // Small delay to ensure modal visibility
            });
        });
    </script>
@endsection
