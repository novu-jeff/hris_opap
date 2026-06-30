<div>
    <label class="mb-2" for="file">File Upload</label>
    <input type="file" wire:model="file" id="file" class="form-control" wire:loading.attr="disabled" wire:target="upload_file">
    <div class="mt-2 text-muted fw-bold text-uppercase d-flex justify-content-between align-items-center" style="font-size: 13px">
        <small class="text-muted">
            Supported formats:
            {{ implode(', ', $supportedFormats) }}
        </small> 
        <small>download <a href="{{asset('templates/timelogs_upload_template.csv')}}" class="text-primary">sample file</a></small>
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
        <div class="mt-4 d-flex justify-content-end" wire:ignore>
            <button
                class="btn btn-primary px-5 py-3 text-uppercase fw-bold"
                wire:click="upload_file"
                wire:loading.attr="disabled">
        
                <span>Upload File</span>
        
            </button>
        </div>
    @endif

    <div class="modal" id="modal-loading" wire:ignore.self data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
    
                <div class="modal-body">
    
                    <div wire:poll.1s="refreshProgress">
    
                        <h5 class="text-center mb-4">
                            Uploading Timelogs...
                        </h5>
    
                        <div class="progress" style="height:28px;">
    
                            <div
                                class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                role="progressbar"
                                style="width: {{ $progress }}%;"
                            >
                                {{ $progress }}%
                            </div>
    
                        </div>
    
                        <div class="mt-3 text-center">
    
                            <strong>
                                {{ number_format($processedRecords) }}
                                /
                                {{ number_format($totalRecords) }}
                            </strong>
    
                            <br>
    
                            <small class="text-muted">
                                records imported
                            </small>
    
                        </div>
    
                    </div>
    
                </div>
    
            </div>
        </div>
    </div>
    <style>
        .loading-spinner {
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

<script>
    document.addEventListener('livewire:init', () => {

        Livewire.on('show-progress', () => {

            const modal = new bootstrap.Modal(
                document.getElementById('modal-loading')
            );

            modal.show();

            });


    Livewire.on('upload-finished', () => {

        const modal = bootstrap.Modal.getInstance(
            document.getElementById('modal-loading')
        );

        if (modal) {
            modal.hide();
        }

    });

});
</script>
