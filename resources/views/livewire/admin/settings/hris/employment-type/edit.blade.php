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
                            <label class="mb-2" for="fields.code">Code <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.code" id="fields.code" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.code') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="fields.name">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.name" id="fields.name" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <p class="text-muted mb-3 text-uppercase fst-italic">Settings</p>

                    <hr class="mb-4">
                    <div class="row mx-2">
                        <div class="col-md-4 form-check mb-2">
                            <input class="form-check-input" disabled type="checkbox" wire:model="fields.is_salary" id="is_salary">
                            <label class="form-check-label" for="is_salary">Salary</label>
                            <div class="error-field">
                                @error('fields.is_salary') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4 form-check mb-2">
                            <input class="form-check-input" type="checkbox" wire:model="fields.is_ot_pay" id="is_ot_pay">
                            <label class="form-check-label" for="is_ot_pay">OT Pay</label>
                            <div class="error-field">
                                @error('fields.is_ot_pay') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4 form-check mb-2">
                            <input class="form-check-input" type="checkbox" wire:model="fields.is_clothing_allowance" id="is_clothing_allowance">
                            <label class="form-check-label" for="is_clothing_allowance">Clothing Allowance</label>
                            <div class="error-field">
                                @error('fields.is_clothing_allowance') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4 form-check mb-2">
                            <input class="form-check-input" type="checkbox" wire:model="fields.is_mid_year" id="is_mid_year">
                            <label class="form-check-label" for="is_mid_year">Mid-Year Bonus</label>
                            <div class="error-field">
                                @error('fields.is_mid_year') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4 form-check mb-2">
                            <input class="form-check-input" type="checkbox" wire:model="fields.is_year_end" id="is_year_end">
                            <label class="form-check-label" for="is_year_end">Year-End Bonus</label>
                            <div class="error-field">
                                @error('fields.is_year_end') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4 form-check mb-2">
                            <input class="form-check-input" type="checkbox" wire:model="fields.is_eme_rata" id="is_eme_rata">
                            <label class="form-check-label" for="is_eme_rata">RaTa</label>
                            <div class="error-field">
                                @error('fields.is_eme_rata') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4 form-check mb-2">
                            <input class="form-check-input" type="checkbox" wire:model="fields.is_eme" id="is_eme">
                            <label class="form-check-label" for="is_eme">EME</label>
                            <div class="error-field">
                                @error('fields.is_eme') <span class="text-danger">{{ $message }}</span> @enderror
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
