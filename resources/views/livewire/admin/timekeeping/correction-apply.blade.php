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
                            <input type="time" wire:model="clock_in_am" id="clock_in_am" class="form-control">
                            <div class="error-field">
                                @error('clock_in_am') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2">Break Out <span class="text-danger">*</span></label>
                            <input type="time" wire:model="clock_out_am" id="clock_out_am" class="form-control">
                            <div class="error-field">
                                @error('clock_out_am') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2">Break In <span class="text-danger">*</span></label>
                            <input type="time" wire:model="clock_in_pm" id="clock_in_pm" class="form-control">
                            <div class="error-field">
                                @error('clock_in_pm') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2">Clock Out <span class="text-danger">*</span></label>
                            <input type="time" wire:model="clock_out_pm" id="clock_out_pm" class="form-control">
                            <div class="error-field">
                                @error('clock_out_pm') <span class="text-danger">{{ $message }}</span> @enderror
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
