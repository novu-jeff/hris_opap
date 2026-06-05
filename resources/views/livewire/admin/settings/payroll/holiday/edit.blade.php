<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-12">
            <div class="card shadow p-4">
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                </div>
                <hr class="mx-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" id="name" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="date">Date <span class="text-danger">*</span></label>
                            <input type="date" wire:model="date" id="date" class="form-control text-uppercase">
                            @if (!$isYearly)
                                <small class="text-info">This event will occur only on the specific date you provide.</small>
                            @endif
                            <div class="error-field">
                                @error('date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                       
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="type">Type <span class="text-danger">*</span></label>
                            <select wire:model="type" id="type" class="form-control" wire:change="checkIfYearly">
                                <option value="">Select Type</option>
                                <option value="regular">Regular Holiday</option>
                                <option value="special-non-working">Special Non-Working Holiday</option>
                                <option value="special-working">Special Working Holiday</option>
                                <option value="wfh">Work From Home</option>
                                <option value="half-day-wfh">Half Day WFH</option>
                                <option value="half-day">Half Day</option>
                                <option value="company">Company Holiday</option>
                            </select>
                            <div class="error-field">
                                @error('type') <span class="text-danger">{{ $message }}</span> @enderror
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
