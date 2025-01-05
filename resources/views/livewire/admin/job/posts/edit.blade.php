<form wire:submit.prevent="save">
    <div class="card shadow p-3">
        <div class="card-header bg-transparent border-0">
            <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
        </div>
        <hr class="mx-3">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-4 mb-4">
                    <label class="mb-2" for="position">Position <span class="text-danger">*</span></label>
                    <input type="text" wire:model="position" id="position" class="form-control text-uppercase">
                    <div class="error-field">
                        @error('position') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-12 col-md-4 mb-4">
                    <label class="mb-2" for="company_name">Company Name <span class="text-danger">*</span></label>
                    <input type="text" wire:model="company_name" id="company_name" class="form-control text-uppercase">
                    <div class="error-field">
                        @error('company_name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-12 col-md-4 mb-4">
                    <label class="mb-2" for="location">Location <span class="text-danger">*</span></label>
                    <input type="text" wire:model="location" id="location" class="form-control text-uppercase">
                    <div class="error-field">
                        @error('location') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-12 col-md-4 mb-4">
                    <label class="mb-2" for="setup">Work Setup <span class="text-danger">*</span></label>
                    <select wire:model="setup" id="setup" class="form-select">
                        <option value=""> - CHOOSE - </option>
                        <option value="work from home">Work From Home</option>
                        <option value="onsite">On Site</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                    <div class="error-field">
                        @error('setup') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-12 col-md-4 mb-4">
                    <label class="mb-2" for="type">Employment Type <span class="text-danger">*</span></label>
                    <select wire:model="type" id="type" class="form-select">
                        <option value=""> - CHOOSE - </option>
                        @foreach ($employment_types as $item)
                            <option value="{{$item->id}}">{{$item->name . ' - (' . $item->code . ')'}}</option>
                        @endforeach
                    </select>  
                    <div class="error-field">
                        @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-12 col-md-4 mb-4">
                    <label class="mb-2" for="slots">Available Slots <span class="text-danger">*</span></label>
                    <input type="number" wire:model="slot" id="slot" class="form-control"> 
                    <div class="error-field">
                        @error('slot') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-12 col-md-6 mb-4">
                    <label class="mb-2" for="min_salary">Minimum Salary <span class="text-danger">*</span></label>
                    <input type="number" wire:model="min_salary" id="min_salary" class="form-control text-uppercase">
                    <div class="error-field">
                        @error('min_salary') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-12 col-md-6 mb-4">
                    <label class="mb-2" for="max_salary">Maximum Salary <span class="text-danger">*</span></label>
                    <input type="number" wire:model="max_salary" id="max_salary" class="form-control text-uppercase" value="123">
                    <div class="error-field">
                        @error('max_salary') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label class="mb-2" for="description">Job Description <span class="text-danger">*</span></label>
                    <div wire:ignore>
                        <textarea wire:model="description" id="ckeditor" class="form-control text-uppercase"></textarea>
                    </div>
                    <div class="error-field">
                        @error('description') <span class="text-danger">{{ $message }}</span> @enderror
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
</form>
