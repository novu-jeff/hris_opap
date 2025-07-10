<form wire:submit.prevent="save" wire:target="save">
    <div class="accordion" id="accordionTabPersonal">
        <div class="accordion-item mb-4">
            <h2 class="accordion-header">
                <button class="accordion-button text-uppercase fw-bold " type="button"  data-bs-toggle="collapse" data-bs-target="#flush-parents" aria-expanded="false" aria-controls="flush-parents">
                    Relative Details
                </button>
            </h2>
            <div id="flush-parents" class="accordion-collapse collapse show" data-bs-parent="#accordionTabFamily">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3">
                            <label class="mb-2" for="records.spouse_surname">Spouse's Surname</label>
                            <input type="text" wire:model="records.spouse_surname" id="records.spouse_surname" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('records.spouse_surname') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="mb-2" for="records.spouse_firstname">First Name</label>
                            <input type="text" wire:model="records.spouse_firstname" id="records.spouse_firstname" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('records.spouse_firstname') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="mb-2" for="records.spouse_middlename">Middle Name</label>
                            <input type="text" wire:model="records.spouse_middlename" id="records.spouse_middlename" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('records.spouse_middlename') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="mb-2" for="suffix">Suffix</label>
                            <select wire:model="records.spouse_suffix" id="records.spouse_suffix" class="form-select text-uppercase">
                                <option value=""> - CHOOSE - </option>
                                <option value="jr">Jr</option>
                                <option value="sr">Sr</option>
                                <option value="I">I</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                                <option value="V">V</option>
                            </select>
                            <div class="error-field">
                                @error('records.spouse_suffix') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mb-3">
                            <label class="mb-2" for="records.spouse_occupation">Occupation</label>
                            <input type="text" wire:model="records.spouse_occupation" id="records.spouse_occupation" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('records.spouse_occupation') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="mb-2" for="records.spouse_business_name_employer">Employer / Business Name</label>
                            <input type="text" wire:model="records.spouse_business_name_employer" id="records.spouse_business_name_employer" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('records.spouse_business_name_employer') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="mb-2" for="records.spouse_business_address">Business Address</label>
                            <input type="text" wire:model="records.spouse_business_address" id="records.spouse_business_address" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('records.spouse_business_address') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="mb-2" for="records.spouse_contact_no">Contact Number</label>
                            <input type="text" wire:model="records.spouse_contact_no" id="records.spouse_contact_no" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('records.spouse_contact_no') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <hr>
                        </div>

                        <div class="col-12 col-md-3 mb-3">
                            <label class="mb-2" for="records.father_surname">Father's Surname</label>
                            <input type="text" wire:model="records.father_surname" id="records.father_surname" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('records.father_surname') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="mb-2" for="records.father_firstname">First Name</label>
                            <input type="text" wire:model="records.father_firstname" id="records.father_firstname" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('records.father_firstname') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="mb-2" for="records.father_middlename">Middle Name</label>
                            <input type="text" wire:model="records.father_middlename" id="records.father_middlename" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('records.father_middlename') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="mb-2" for="suffix">Suffix</label>
                            <select wire:model="records.father_suffix" id="records.father_suffix" class="form-select text-uppercase">
                                <option value=""> - CHOOSE - </option>
                                <option value="jr">Jr</option>
                                <option value="sr">Sr</option>
                                <option value="I">I</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                                <option value="V">V</option>
                            </select>
                            <div class="error-field">
                                @error('records.father_suffix') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <hr>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="mb-2" for="records.records.mother_surname">Mother's Surname</label>
                            <input type="text" wire:model="records.mother_surname" id="records.mother_surname" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('records.mother_surname') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="mb-2" for="records.mother_firstname">First Name</label>
                            <input type="text" wire:model="records.mother_firstname" id="records.mother_firstname" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('records.mother_firstname') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="mb-2" for="records.mother_middlename">Middle Name</label>
                            <input type="text" wire:model="records.mother_middlename" id="records.mother_middlename" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('records.mother_middlename') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end bg-transparent border-0 px-5">
            <div class="text-end">
                <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                    <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                    <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                </button>
                @if ($errors->any())
                    <div class="mt-4 pb-5">
                        <small class="text-danger">There's an error upon submitting, please review your form.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</form>

