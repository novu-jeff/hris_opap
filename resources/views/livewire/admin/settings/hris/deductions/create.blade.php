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
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2" for="fields.code">Code </label>
                            <input type="text" wire:model="fields.code" id="fields.code" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.code') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-9 mb-4">
                            <label class="mb-2" for="fields.name">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.name" id="fields.name" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="fields.source">Source <span class="text-danger">*</span></label>
                            <select wire:model="fields.source" wire:change="onChangeSelect('source', event.target.value)" id="source" class="form-select">
                                <option value=""> - Choose - </option>
                                <option value="entry">Data Entry</option>
                            </select>
                            <div class="error-field">
                                @error('fields.source') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="fields.frequency">Frequency <span class="text-danger">*</span></label>
                            <select wire:model="fields.frequency" wire:change="onChangeSelect('frequency', event.target.value)" id="frequency" class="form-select">
                                <option value=""> - Choose - </option>
                                <option value="bi_monthly">Bi-Monthly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                            <div class="error-field">
                                @error('fields.frequency') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="fields.eligible">Eligible <span class="text-danger">*</span></label>
                            <select wire:model="fields.eligible" wire:change="onChangeSelect('eligible', event.target.value)" id="eligible" class="form-select select-2" multiple>
                                @foreach ($job_category as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                            <div class="error-field">
                                @error('fields.eligible') <span class="text-danger">{{ $message }}</span> @enderror
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
