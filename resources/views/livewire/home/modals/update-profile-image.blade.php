<div wire:ignore>
    <div class="modal fade" id="update-profile-image-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-profile-image-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="update-profile-image-modalLabel">Update Profile Image</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-12 mb-3">
                            <label for="image" class="mb-2">Image <span class="text-danger">*</span></label>
                            <div class="droparea">
                                <div>
                                    <div class="m-auto text-center icon">
                                        <i class="fa-solid fa-upload fa-bounce"></i>
                                    </div>
                                    <div class="text-center">
                                        <h6>Upload image here</h6>
                                        <small>(only accepts jpg, jpeg, and png file)</small>
                                    </div>
                                </div>
                            </div>
                            <input type="file" wire:model="record.image" id="image" class="form-control d-none">
                            <div class="droparea-preview"></div>
                            <div class="error-field"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" wire:click='save'>Proceed</button>
                </div>
            </div>
        </div>
    </div>      
</div>