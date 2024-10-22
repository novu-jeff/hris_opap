<div>
    <div class="modal fade" wire:ignore.self id="update-profile-image-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-profile-image-modalLabel" aria-hidden="true">
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
                                    <div class="text-center d-flex">
                                        <input type="file" wire:model="profile" id="profile" class="w-100">
                                    </div>
                                </div>
                            </div>
                            @if (!empty($record))
                                <div class="image-preview">
                                    <img src="{{Storage::url('applicant/users/'.$user_id.'/'.$record)}}" alt="" srcset="">
                                    <button wire:click='remove_profile'>
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            @endif
                            <div class="error-field">
                                @error('profile') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
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