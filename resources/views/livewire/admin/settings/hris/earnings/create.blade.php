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
                            <label class="mb-2" for="fields.amount_type">Amount Type <span class="text-danger">*</span></label>
                            <select wire:model="fields.amount_type" wire:change="onChange('amount_type', $event.target.value)" id="amount_type" class="form-select">
                                <option value=""> - Choose - </option>
                                <option value="fixed_amount">Fixed Amount</option>
                                <option value="basic_salary">Basic Salary</option>
                                <option value="percentage">Percentage Based On Salary</option>
                            </select>
                            <div class="error-field">
                                @error('fields.amount_type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @if(in_array($amountType, ['fixed_amount', 'percentage']))
                            <div class="col-12 col-md-6 mb-3">
                                <label for="first_term" class="form-label">First Term</label>
                                <input type="number" id="first_term" wire:model="fields.first_term" class="form-control">
                                @error('fields.first_term') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="second_term" class="form-label">Second Term</label>
                                <input type="number" id="second_term" wire:model="fields.second_term" class="form-control">
                                @error('fields.second_term') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        @endif
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="fields.is_taxable">Is Taxable? <span class="text-danger">*</span></label>
                            <select wire:model="fields.is_taxable" id="is_taxable" class="form-select">
                                <option value=""> - Choose - </option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <div class="error-field">
                                @error('fields.is_taxable') <span class="text-danger">{{ $message }}</span> @enderror
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
