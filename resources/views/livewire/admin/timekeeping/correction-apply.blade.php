<form wire:submit.prevent="save" wire:target="save">
    <div class="row">
        <div class="col-12">
            <div class="card shadow p-4">
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                </div>
                <hr class="mx-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2">Clock In <span class="text-danger">*</span></label>
                            <input type="time" wire:model="clockin" id="clockin" class="form-control">
                            <div class="error-field">
                                @error('clockin') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2">Lunch Out <span class="text-danger">*</span></label>
                            <input type="time" wire:model="breakout" id="breakout" class="form-control">
                            <div class="error-field">
                                @error('breakout') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2">Lunch In <span class="text-danger">*</span></label>
                            <input type="time" wire:model="breakin" id="breakin" class="form-control">
                            <div class="error-field">
                                @error('breakin') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2">Clock Out <span class="text-danger">*</span></label>
                            <input type="time" wire:model="clockout" id="clockout" class="form-control">
                            <div class="error-field">
                                @error('clockout') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mx-3">
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="save">Proceed <i class="fa-solid fa-arrow-right ms-2"></i></span>
                        <span wire:loading wire:target="save">Proceeding <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
