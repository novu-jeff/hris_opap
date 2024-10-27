<div>
    <div class="modal fade" wire:ignore.self id="update-profile-resume-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-profile-resume-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="update-profile-resume-modalLabel">Update Profile Resume</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-12 mb-3">
                            <label for="resume" class="mb-2">Resume <span class="text-danger">*</span></label>
                            <div class="droparea">
                                <div>
                                    <div class="text-center d-flex">
                                        <input type="file" wire:model="resume" id="resume" class="w-100">
                                    </div>
                                </div>
                            </div>
                            @if ($resume_preview)
                                <div class="image-preview w-100">
                                    <iframe src="{{ $resume_preview }}" width="100%" height="500px" class="mt-3"></iframe>                                                    
                                    <button wire:click='remove_resume'>
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            @endif
                            <div class="error-field">
                                @error('resume') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" wire:click='save_resume'>Proceed</button>
                </div>
            </div>
        </div>
    </div> 
</div>