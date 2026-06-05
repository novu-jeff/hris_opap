<form wire:submit.prevent="save">
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow p-4">
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                </div>
                <hr class="mx-3">
                <div class="card-body">
                    <div class="row">
                       
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="fields.accomplishment_name">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.accomplishment_name" id="fields.accomplishment_name" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.accomplishment_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                </div>

                <hr class="mx-3">
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                        <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
