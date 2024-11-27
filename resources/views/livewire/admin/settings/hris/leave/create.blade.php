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
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="fields.code">Code <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.code" id="fields.code" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.code') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="fields.name">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.name" id="fields.name" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="fields.credits">Credits (in days) <span class="text-danger">*</span></label>
                            <input type="number" wire:model="fields.credits" id="fields.credits" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.credits') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="fields.isCummulative">Is Cummulative to next year? <span class="text-danger">*</span></label>
                            <select wire:model="fields.isCummulative" id="isCummulative" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <div class="error-field">
                                @error('fields.isCummulative') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mx-3">
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Proceed</button>
                </div>
            </div>
        </div>
    </div>
</form>
