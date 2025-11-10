<div>
    @if(!empty($records))
    <div class="row">
        <div class="col-12 col-md-3 mb-3">
            <label class="mb-2" for="records.spouse_surname.new">Spouse's Surname</label>
            <input type="text" readonly wire:model="records.spouse_surname.new" id="records.spouse_surname.new" class="form-control restricted {{$records['spouse_surname']['new'] !== $records['spouse_surname']['old'] ? 'border-danger border-3' : ''}}">
            <div class="error-field">
                @error('records.spouse_surname.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 col-md-3 mb-3">
            <label class="mb-2" for="records.spouse_firstname.new">First Name</label>
            <input type="text" readonly wire:model="records.spouse_firstname.new" id="records.spouse_firstname.new" class="form-control restricted {{$records['spouse_firstname']['new'] !== $records['spouse_firstname']['old'] ? 'border-danger border-3' : ''}}">
            <div class="error-field">
                @error('records.spouse_firstname.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 col-md-3 mb-3">
            <label class="mb-2" for="records.spouse_middlename.new">Middle Name</label>
            <input type="text" readonly wire:model="records.spouse_middlename.new" id="records.spouse_middlename.new" class="form-control restricted {{$records['spouse_middlename']['new'] !== $records['spouse_middlename']['old'] ? 'border-danger border-3' : ''}}">
            <div class="error-field">
                @error('records.spouse_middlename.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 col-md-3 mb-3">
            <label class="mb-2" for="suffix">Suffix</label>
            <select disabled wire:model="records.spouse_suffix.new" id="records.spouse_suffix.new" class="form-select restricted {{$records['spouse_suffix']['new'] !== $records['spouse_suffix']['old'] ? 'border-danger border-3' : ''}}">
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
                @error('records.spouse_suffix.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 col-md-12 mb-3">
            <label class="mb-2" for="records.spouse_occupation.new">Occupation</label>
            <input type="text" readonly wire:model="records.spouse_occupation.new" id="records.spouse_occupation.new" class="form-control restricted {{$records['spouse_occupation']['new'] !== $records['spouse_occupation']['old'] ? 'border-danger border-3' : ''}}">
            <div class="error-field">
                @error('records.spouse_occupation.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
            <label class="mb-2" for="records.spouse_business_name_employer.new">Employer / Business Name</label>
            <input type="text" readonly wire:model="records.spouse_business_name_employer.new" id="records.spouse_business_name_employer.new" class="form-control restricted {{$records['spouse_business_name_employer']['new'] !== $records['spouse_business_name_employer']['old'] ? 'border-danger border-3' : ''}}">
            <div class="error-field">
                @error('records.spouse_business_name_employer.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
            <label class="mb-2" for="records.spouse_business_address.new">Business Address</label>
            <input type="text" readonly wire:model="records.spouse_business_address.new" id="records.spouse_business_address.new" class="form-control restricted {{$records['spouse_business_address']['new'] !== $records['spouse_business_address']['old'] ? 'border-danger border-3' : ''}}">
            <div class="error-field">
                @error('records.spouse_business_address.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
            <label class="mb-2" for="records.spouse_contact_no.new">Contact Number</label>
            <input type="text" readonly wire:model="records.spouse_contact_no.new" id="records.spouse_contact_no.new" class="form-control restricted {{$records['spouse_contact_no']['new'] !== $records['spouse_contact_no']['old'] ? 'border-danger border-3' : ''}}">
            <div class="error-field">
                @error('records.spouse_contact_no.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 mb-4">
            <hr>
        </div>

        <div class="col-12 col-md-3 mb-3">
            <label class="mb-2" for="records.father_surname.new">Father's Surname</label>
            <input type="text" readonly wire:model="records.father_surname.new" id="records.father_surname.new" class="form-control restricted {{$records['father_surname']['new'] !== $records['father_surname']['old'] ? 'border-danger border-3' : ''}}">
            <div class="error-field">
                @error('records.father_surname.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 col-md-3 mb-3">
            <label class="mb-2" for="records.father_firstname.new">First Name</label>
            <input type="text" readonly wire:model="records.father_firstname.new" id="records.father_firstname.new" class="form-control restricted {{$records['father_firstname']['new'] !== $records['father_firstname']['old'] ? 'border-danger border-3' : ''}}">
            <div class="error-field">
                @error('records.father_firstname.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 col-md-3 mb-3">
            <label class="mb-2" for="records.father_middlename.new">Middle Name</label>
            <input type="text" readonly wire:model="records.father_middlename.new" id="records.father_middlename.new" class="form-control restricted {{$records['father_middlename']['new'] !== $records['father_middlename']['old'] ? 'border-danger border-3' : ''}}">
            <div class="error-field">
                @error('records.father_middlename.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 col-md-3 mb-3">
            <label class="mb-2" for="suffix">Suffix</label>
            <select disabled wire:model="records.father_suffix.new" id="records.father_suffix.new" class="form-select restricted {{$records['father_suffix']['new'] !== $records['father_suffix']['old'] ? 'border-danger border-3' : ''}}">
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
                @error('records.father_suffix.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 mb-4">
            <hr>
        </div>
        <div class="col-12 col-md-4 mb-3">
            <label class="mb-2" for="records.records.new.mother_surname.new">Mother's Surname</label>
            <input type="text" readonly wire:model="records.mother_surname.new" id="records.mother_surname.new" class="form-control restricted {{$records['mother_surname']['new'] !== $records['mother_surname']['old'] ? 'border-danger border-3' : ''}}">
            <div class="error-field">
                @error('records.mother_surname.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
            <label class="mb-2" for="records.mother_firstname.new">First Name</label>
            <input type="text" readonly wire:model="records.mother_firstname.new" id="records.mother_firstname.new" class="form-control restricted {{$records['mother_firstname']['new'] !== $records['mother_firstname']['old'] ? 'border-danger border-3' : ''}}">
            <div class="error-field">
                @error('records.mother_firstname.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
            <label class="mb-2" for="records.mother_middlename.new">Middle Name</label>
            <input type="text" readonly wire:model="records.mother_middlename.new" id="records.mother_middlename.new" class="form-control restricted {{$records['mother_middlename']['new'] !== $records['mother_middlename']['old'] ? 'border-danger border-3' : ''}}">
            <div class="error-field">
                @error('records.mother_middlename.new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    @endif
</div>