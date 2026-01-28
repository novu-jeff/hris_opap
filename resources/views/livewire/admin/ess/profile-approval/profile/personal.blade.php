<div>
    @if(!empty($records))
        <div class="accordion" id="accordionTabPersonal">
            <div class="accordion-item mb-4">
                <h2 class="accordion-header">
                    <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-personal" aria-expanded="false" aria-controls="flush-personal">
                        Personal Information
                    </button>
                </h2>
                <div id="flush-personal" class="accordion-collapse collapse show">
                    {{-- PROFILE PHOTO PREVIEW --}}
                    @if(isset($records['profile']))
                    <div class="mb-4">
                        <h5 class="fw-bold text-uppercase mb-3">Profile Photo</h5>

                        <div class="d-flex align-items-center gap-4">

                            {{-- OLD PHOTO --}}
                            <div class="text-center">
                                <p class="mb-1 fw-semibold"></p>
                                @if(!empty($records['profile']['new']))
                                    <img src="{{ asset('storage/' . $records['profile']['new']) }}"
                                         class="rounded border border-3 {{$records['profile']['changed'] ? 'border-danger border-3' : ''}}"
                                        style="width:120px; height:120px; object-fit:cover;">
                                @else
                                    <div class="border rounded d-flex justify-content-center align-items-center"
                                        style="width:120px; height:120px; background:#f8f9fa;">
                                        <span class="text-muted small">No Image</span>
                                    </div>
                                @endif
                            </div>

                          
                        <hr class="mt-4">
                    </div>
                    @endif
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-12 col-md-3 mb-3">
                                <label class="mb-2" for="lastname">Surname</label>
                                <input type="text" readonly wire:model="records.lastname.new" id="lastname" class="form-control restricted {{$records['lastname']['changed'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.lastname.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                            <div class="col-12 col-md-4 mb-3">
                                <label class="mb-2" for="firstname">First Name</label>
                                <input type="text" readonly wire:model="records.firstname.new" id="firstname" class="form-control restricted {{$records['firstname']['changed'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.firstname.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                            <div class="col-12 col-md-3 mb-3">
                                <label class="mb-2" for="middlename">Middle Name</label>
                                <input type="text" readonly wire:model="records.middlename.new" id="middlename" class="form-control restricted {{$records['middlename']['changed'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.middlename.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                            <div class="col-12 col-md-2 mb-3">
                                <label class="mb-2" for="suffix">Suffix</label>
                                <select disabled wire:model="records.suffix.new" id="suffix" class="form-select restricted {{$records['suffix']['changed'] ? 'border-danger border-3' : ''}}">
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
                                    @error('records.suffix.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                            <div class="col-12 col-md-4 mb-3">
                                <label class="mb-2" for="birthday">Date of Birth</label>
                                <input type="text" readonly wire:model="records.birthday.new" id="birthday" class="form-control restricted {{$records['birthday']['changed'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.birthday.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                            <div class="col-12 col-md-4 mb-3">
                                <label class="mb-2" for="civil_status">Civil Status</label>
                                <select disabled wire:model="records.civil_status.new" id="civil_status" class="form-select restricted {{$records['civil_status']['changed'] ? 'border-danger border-3' : ''}}">
                                    <option value=""> - CHOOSE - </option>
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="separated">Separated</option>
                                    <option value="widowed">Widowed</option>
                                    <option value="annulled">Annulled</option>
                                </select>
                                <div class="error-field">
                                    @error('records.civil_status.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                            <div class="col-12 col-md-4 mb-3">
                                <label class="mb-2" for="sex">Sex</label>
                                <select disabled wire:model="records.sex.new" id="sex" class="form-select restricted {{$records['sex']['changed'] ? 'border-danger border-3' : ''}}">
                                    <option value=""> - CHOOSE - </option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                <div class="error-field">
                                    @error('records.sex.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                            <div class="col-12 mb-3">
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-4 mb-3">
                                <label class="mb-2" for="citizenship">Citizenship</label>
                                <select disabled wire:model="records.citizenship.new" wire:change="select_change('citizenship')"  id="citizenship" class="form-select restricted {{$records['citizenship']['changed'] ? 'border-danger border-3' : ''}}">
                                    <option value=""> - CHOOSE - </option>
                                    <option value="filipino">Filipino</option>
                                    <option value="dual_citizenship">Dual Citizenship</option>
                                </select>
                                <div class="error-field">
                                    @error('records.citizenship.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @if ($isDualCitizenship)
                                <div class="col-12 col-md-4 mb-3">    
                                    <label class="mb-2" for="country">Country (Dual Citizenship)</label>
                                        <select disabled wire:model="records.country.new" id="citizenship_type" class="form-select restricted {{$records['country']['changed'] ? 'border-danger border-3' : ''}}">
                                            <option value=""> - CHOOSE - </option>
                                            @foreach ($countries as $country)
                                                <option value="{{$country['name']['common']}}">{{$country['name']['common']}}</option>
                                            @endforeach
                                        </select>
                                        <div class="error-field">
                                            @error('records.country.new') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                @endif
                            <div class="col-12 col-md-4 mb-3">
                                <label class="mb-2" for="citizenship_type">Citizenship Type</label>
                                <select disabled wire:model="records.citizenship_type.new" id="citizenship_type" class="form-select restricted {{$records['citizenship_type']['changed'] ? 'border-danger border-3' : ''}}">
                                    <option value=""> - CHOOSE - </option>
                                    <option value="by_birth">By Birth</option>
                                    <option value="by_naturalization">By Naturalization</option>
                                </select>
                                <div class="error-field">
                                    @error('records.citizenship_type.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item mb-4">
                <h2 class="accordion-header">
                    <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-address" aria-expanded="false" aria-controls="flush-address">
                        Address
                    </button>
                </h2>
                <div id="flush-address" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-12 col-md-12 mb-3">
                                <label class="mb-2" for="present_address">Residential Address</label>
                                <input type="text" readonly wire:model="records.present_address.new" id="present_address" class="restricted {{$records['present_address']['changed'] ? 'border-danger border-3' : ''}} form-control text-uppercase" placeholder="House / Block / Lot / Street / Subdivision / Village / Barangay">
                                <div class="error-field">
                                    @error('records.present_address.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                            <div class="col-12 col-md-6 mb-3">
                                <label class="mb-2" for="present_province">State / Province</label>
                                <input type="text" readonly wire:model="records.present_province.new" id="present_province" class="restricted {{$records['present_province']['changed'] ? 'border-danger border-3' : ''}} form-control text-uppercase">
                                <div class="error-field">
                                    @error('records.present_province.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                            <div class="col-12 col-md-6 mb-3">
                                <label class="mb-2" for="present_city">City / Municipality</label>
                                <input type="text" readonly wire:model="records.present_city.new" id="present_city" class="{{$records['present_city']['changed'] ? 'border-danger border-3' : ''}} form-control text-uppercase">
                                <div class="error-field">
                                    @error('records.present_city.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div> 
                            <div class="col-12 mb-3">
                                <hr>
                            </div> 
                            <div class="col-12 col-md-12 mb-3">
                                <label class="mb-2" for="permanent_address">Permanent Address</label>
                                <input type="text" readonly wire:model="records.permanent_address.new" id="permanent_address" class="restricted {{$records['permanent_address']['changed'] ? 'border-danger border-3' : ''}} form-control text-uppercase" placeholder="House / Block / Lot / Street / Subdivision / Village / Barangay">
                                <div class="error-field">
                                    @error('records.permanent_address.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                            <div class="col-12 col-md-6 mb-3">
                                <label class="mb-2" for="permanent_province">State / Province</label>
                                <input type="text" readonly wire:model="records.permanent_province.new" id="permanent_province" class="restricted {{$records['permanent_province']['changed'] ? 'border-danger border-3' : ''}} form-control text-uppercase">
                                <div class="error-field">
                                    @error('records.permanent_province.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                            <div class="col-12 col-md-6 mb-3">
                                <label class="mb-2" for="permanent_city">City / Municipality</label>
                                <input type="text" readonly wire:model="records.permanent_city.new" id="permanent_city" class="restricted {{$records['permanent_city']['changed'] ? 'border-danger border-3' : ''}} form-control text-uppercase">
                                <div class="error-field">
                                    @error('records.permanent_city.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item mb-4">
                <h2 class="accordion-header">
                    <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-contact" aria-expanded="false" aria-controls="flush-contact">
                        Contact Information
                    </button>
                </h2>
                <div id="flush-contact" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-12 col-md-4 mb-3">
                                <label class="mb-2" for="mobile_number">Mobile No.</label>
                                <input type="text" readonly wire:model="records.mobile_number.new" id="mobile_number" class="restricted {{$records['mobile_number']['changed'] ? 'border-danger border-3' : ''}} form-control text-uppercase" data-mask="mobile">
                                <div class="error-field">
                                    @error('records.mobile_number.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                            <div class="col-12 col-md-4 mb-3">
                                <label class="mb-2" for="tel_no">Telephone No.</label>
                                <input type="text" readonly wire:model="records.tel_no.new" id="tel_no" class="restricted {{$records['tel_no']['changed'] ? 'border-danger border-3' : ''}} form-control text-uppercase">
                                <div class="error-field">
                                    @error('records.tel_no.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                            <div class="col-12 col-md-4 mb-3">
                                <label class="mb-2" for="email">Email</label>
                                <input type="email" readonly wire:model="records.email.new" id="email" class="restricted {{$records['email']['changed'] ? 'border-danger border-3' : ''}} form-control">
                                <div class="error-field">
                                    @error('records.email.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>  
                        </div>
                    </div>
                </div> 
            </div>
            <div class="accordion-item mb-4">
                <h2 class="accordion-header">
                    <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-appearance" aria-expanded="false" aria-controls="flush-appearance">
                        Appearance
                    </button>
                </h2>
                <div id="flush-appearance" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="mb-2" for="height">Height</label>
                                <input type="text" readonly wire:model="records.height.new" id="height" class="restricted {{$records['height']['changed'] ? 'border-danger border-3' : ''}} form-control text-uppercase">
                                <div class="error-field">
                                    @error('records.height.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="mb-2" for="weight">Weight</label>
                                <input type="text" readonly wire:model="records.weight.new" id="weight" class="restricted {{$records['weight']['changed'] ? 'border-danger border-3' : ''}} form-control text-uppercase">
                                <div class="error-field">
                                    @error('records.weight.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="mb-2" for="blood_type">Blood Type</label>
                                <input type="text" readonly wire:model="records.blood_type.new" id="blood_type" class="restricted {{$records['blood_type']['changed'] ? 'border-danger border-3' : ''}} form-control text-uppercase">
                                <div class="error-field">
                                    @error('records.blood_type.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>