<div>
    <div class="d-flex justify-content-end mb-5 gap-3">
        <a href="{{route('hris.index')}}" class="btn btn-outline-primary px-5 py-3 text-uppercase">Go Back</a>
    </div>
    <div class="card mb-4">
        <div class="card-body px-5">
            <ul class="nav nav-pills mb-3 d-flex justify-content-center gap-3 py-4" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button wire:target="setActiveTab('details')" wire:click="setActiveTab('details')" class="nav-link text-uppercase fw-bold {{$activeTab == 'details' ? 'active' : ''}}" id="pills-details-tab" data-bs-toggle="pill" data-bs-target="#pills-details" type="button" role="tab" aria-controls="pills-details" aria-selected="true">Employee Details</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button wire:target="setActiveTab('family')" wire:click="setActiveTab('family')" class="nav-link text-uppercase fw-bold {{$activeTab == 'family' ? 'active' : ''}}" id="pills-family-tab" data-bs-toggle="pill" data-bs-target="#pills-family" type="button" role="tab" aria-controls="pills-family" aria-selected="false">Family Background</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button wire:target="setActiveTab('education')" wire:click="setActiveTab('education')" class="nav-link text-uppercase fw-bold {{$activeTab == 'education' ? 'active' : ''}}" id="pills-education-tab" data-bs-toggle="pill" data-bs-target="#pills-education" type="button" role="tab" aria-controls="pills-education" aria-selected="false">Education Information</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button wire:target="setActiveTab('history')" wire:click="setActiveTab('history')" class="nav-link text-uppercase fw-bold {{$activeTab == 'history' ? 'active' : ''}}" id="pills-history-tab" data-bs-toggle="pill" data-bs-target="#pills-history" type="button" role="tab" aria-controls="pills-history" aria-selected="false">Employment History</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button wire:target="setActiveTab('civil_service')" wire:click="setActiveTab('civil_service')" class="nav-link text-uppercase fw-bold {{$activeTab == 'civil_service' ? 'active' : ''}}" id="pills-civil-service-tab" data-bs-toggle="pill" data-bs-target="#pills-civil-service" type="button" role="tab" aria-controls="pills-civil-service" aria-selected="false">Civil Service Eligibility</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button wire:target="setActiveTab('trainings')" wire:click="setActiveTab('trainings')" class="nav-link text-uppercase fw-bold {{$activeTab == 'trainings' ? 'active' : ''}}" id="pills-trainings-tab" data-bs-toggle="pill" data-bs-target="#pills-trainings" type="button" role="tab" aria-controls="pills-trainings" aria-selected="false">Trainings</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button wire:target="setActiveTab('others')" wire:click="setActiveTab('others')" class="nav-link text-uppercase fw-bold {{$activeTab == 'others' ? 'active' : ''}}" id="pills-others-tab" data-bs-toggle="pill" data-bs-target="#pills-others" type="button" role="tab" aria-controls="pills-others" aria-selected="false">Other Voluntary Works</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button wire:target="setActiveTab('skills')" wire:click="setActiveTab('skills')" class="nav-link text-uppercase fw-bold {{$activeTab == 'skills' ? 'active' : ''}}" id="pills-skills-tab" data-bs-toggle="pill" data-bs-target="#pills-skills" type="button" role="tab" aria-controls="pills-skills" aria-selected="false">Skills or Hobbies</button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade {{$activeTab == 'details' ? 'active show' : ''}} p-0" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab" tabindex="0">
                    <div class="row mt-3">
                        <div class="col-12 mb-4">
                            <div class="alert alert-info mb-5 mt-3 text-uppercase fw-bold text-center fst-italic">Note: Fields highlighted with a border indicate recent updates made by the employee.</div>
                            <div class="accordion" id="accordionTabPersonal">
                                <div class="accordion-item mb-4">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-uppercase fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-personal" aria-expanded="false" aria-controls="flush-personal">
                                            Personal Information
                                        </button>
                                    </h2>
                                    <div id="flush-personal" class="accordion-collapse collapse  {{$activeAccordion === 'personal' ? 'collapse show' : ''}}" data-bs-parent="#accordionTabPersonal">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="mb-2" for="lastname">Surname</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.lastname.new" id="lastname" class="form-control restricted {{$records['employee_personal']['lastname']['new'] !== $records['employee_personal']['lastname']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.lastname.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="firstname">First Name</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.firstname.new" id="firstname" class="form-control restricted {{$records['employee_personal']['firstname']['new'] !== $records['employee_personal']['firstname']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.firstname.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="mb-2" for="middlename">Middle Name</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.middlename.new" id="middlename" class="form-control restricted {{$records['employee_personal']['middlename']['new'] !== $records['employee_personal']['middlename']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.middlename.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                                <div class="col-12 col-md-2 mb-3">
                                                    <label class="mb-2" for="suffix">Suffix</label>
                                                    <select disabled wire:model="records.employee_personal.suffix.new" id="suffix" class="form-select restricted {{$records['employee_personal']['suffix']['new'] !== $records['employee_personal']['suffix']['old'] ? 'border-danger border-3' : ''}}">
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
                                                        @error('records.employee_personal.suffix.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="birthday">Date of Birth</label>
                                                    <input type="text" wire:model="records.employee_personal.birthday.new" id="birthday" class="form-control restricted {{$records['employee_personal']['birthday']['new'] !== $records['employee_personal']['birthday']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.birthday.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="civil_status">Civil Status</label>
                                                    <select disabled wire:model="records.employee_personal.civil_status.new" id="civil_status" class="form-select restricted {{$records['employee_personal']['civil_status']['new'] !== $records['employee_personal']['civil_status']['old'] ? 'border-danger border-3' : ''}}">
                                                        <option value=""> - CHOOSE - </option>
                                                        <option value="single">Single</option>
                                                        <option value="married">Married</option>
                                                        <option value="divorced">Divorced</option>
                                                        <option value="separated">Separated</option>
                                                        <option value="widowed">Widowed</option>
                                                        <option value="annulled">Annulled</option>
                                                    </select>
                                                    <div class="error-field">
                                                        @error('records.employee_personal.civil_status.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="sex">Sex</label>
                                                    <select disabled wire:model="records.employee_personal.sex.new" id="sex" class="form-select restricted {{$records['employee_personal']['sex']['new'] !== $records['employee_personal']['sex']['old'] ? 'border-danger border-3' : ''}}">
                                                        <option value=""> - CHOOSE - </option>
                                                        <option value="male">Male</option>
                                                        <option value="female">Female</option>
                                                    </select>
                                                    <div class="error-field">
                                                        @error('records.employee_personal.sex.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                                <div class="col-12 mb-3">
                                                    <hr>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="citizenship">Citizenship</label>
                                                    <select disabled wire:model="records.employee_personal.citizenship.new" wire:change="select_change('citizenship')"  id="citizenship" class="form-select restricted {{$records['employee_personal']['citizenship']['new'] !== $records['employee_personal']['citizenship']['old'] ? 'border-danger border-3' : ''}}">
                                                        <option value=""> - CHOOSE - </option>
                                                        <option value="filipino">Filipino</option>
                                                        <option value="dual_citizenship">Dual Citizenship</option>
                                                    </select>
                                                    <div class="error-field">
                                                        @error('records.employee_personal.citizenship.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                @if ($isDualCitizenship)
                                                    <div class="col-12 col-md-4 mb-3">    
                                                        <label class="mb-2" for="country">Country (Dual Citizenship)</label>
                                                            <select disabled wire:model="records.employee_personal.country.new" id="citizenship_type" class="form-select restricted {{$records['employee_personal']['country']['new'] !== $records['employee_personal']['country']['old'] ? 'border-danger border-3' : ''}}">
                                                                <option value=""> - CHOOSE - </option>
                                                                @foreach ($countries as $country)
                                                                    <option value="{{$country['name']['common']}}">{{$country['name']['common']}}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="error-field">
                                                                @error('records.employee_personal.country.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                            </div>
                                                        </div>
                                                    @endif
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="citizenship_type">Citizenship Type</label>
                                                    <select disabled wire:model="records.employee_personal.citizenship_type.new" id="citizenship_type" class="form-select restricted {{$records['employee_personal']['citizenship_type']['new'] !== $records['employee_personal']['citizenship_type']['old'] ? 'border-danger border-3' : ''}}">
                                                        <option value=""> - CHOOSE - </option>
                                                        <option value="by_birth">By Birth</option>
                                                        <option value="by_naturalization">By Naturalization</option>
                                                    </select>
                                                    <div class="error-field">
                                                        @error('records.employee_personal.citizenship_type.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item mb-4">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-uppercase fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-address" aria-expanded="false" aria-controls="flush-address">
                                            Address
                                        </button>
                                    </h2>
                                    <div id="flush-address" class="accordion-collapse collapse {{$activeAccordion === 'address' ? 'collapse show' : ''}}" data-bs-parent="#accordionTabPersonal">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-12 col-md-12 mb-3">
                                                    <label class="mb-2" for="present_address">Residential Address</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.present_address.new" id="present_address" class="form-control restricted {{$records['employee_personal']['present_address']['new'] !== $records['employee_personal']['present_address']['old'] ? 'border-danger border-3' : ''}}" placeholder="House / Block / Lot / Street / Subdivision / Village / Barangay">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.present_address.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="mb-2" for="present_province">State / Province</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.present_province.new" id="present_province" class="form-control restricted {{$records['employee_personal']['present_province']['new'] !== $records['employee_personal']['present_province']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.present_province.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="mb-2" for="present_city">City / Municipality</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.present_city.new" id="present_city" class="form-control restricted {{$records['employee_personal']['present_city']['new'] !== $records['employee_personal']['present_city']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.present_city.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div> 
                                                <div class="col-12 mb-3">
                                                    <hr>
                                                </div> 
                                                <div class="col-12 col-md-12 mb-3">
                                                    <label class="mb-2" for="permanent_address">Permanent Address</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.permanent_address.new" id="permanent_address" class="form-control restricted {{$records['employee_personal']['permanent_address']['new'] !== $records['employee_personal']['permanent_address']['old'] ? 'border-danger border-3' : ''}}" placeholder="House / Block / Lot / Street / Subdivision / Village / Barangay">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.permanent_address.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="mb-2" for="permanent_province">State / Province</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.permanent_province.new" id="permanent_province" class="form-control restricted {{$records['employee_personal']['permanent_province']['new'] !== $records['employee_personal']['permanent_province']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.permanent_province.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="mb-2" for="permanent_city">City / Municipality</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.permanent_city.new" id="permanent_city" class="form-control restricted {{$records['employee_personal']['permanent_city']['new'] !== $records['employee_personal']['permanent_city']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.permanent_city.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item mb-4">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-uppercase fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-contact" aria-expanded="false" aria-controls="flush-contact">
                                            Contact Information
                                        </button>
                                    </h2>
                                    <div id="flush-contact" class="accordion-collapse collapse {{$activeAccordion === 'contact' ? 'collapse show' : ''}}" data-bs-parent="#accordionTabPersonal">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="mobile_number">Mobile No.</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.mobile_number.new" id="mobile_number" class="form-control restricted {{$records['employee_personal']['mobile_number']['new'] !== $records['employee_personal']['mobile_number']['old'] ? 'border-danger border-3' : ''}}" data-mask="mobile">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.mobile_number.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="tel_no">Telephone No.</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.tel_no.new" id="tel_no" class="form-control restricted {{$records['employee_personal']['tel_no']['new'] !== $records['employee_personal']['tel_no']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.tel_no.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="email">Email</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.email.new" id="email" class="form-control restricted text-lowercase {{$records['employee_personal']['email']['new'] !== $records['employee_personal']['email']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.email.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item mb-4">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-uppercase fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-appearance" aria-expanded="false" aria-controls="flush-appearance">
                                            Appearance
                                        </button>
                                    </h2>
                                    <div id="flush-appearance" class="accordion-collapse collapse {{$activeAccordion === 'appearance' ? 'collapse show' : ''}}" data-bs-parent="#accordionTabPersonal">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="mb-2" for="height">Height</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.height.new" id="height" class="form-control restricted {{$records['employee_personal']['height']['new'] !== $records['employee_personal']['height']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.height.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="mb-2" for="weight">Weight</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.weight.new" id="weight" class="form-control restricted {{$records['employee_personal']['weight']['new'] !== $records['employee_personal']['weight']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.weight.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="mb-2" for="blood_type">Blood Type</label>
                                                    <input type="text" readonly wire:model="records.employee_personal.blood_type.new" id="blood_type" class="form-control restricted {{$records['employee_personal']['blood_type']['new'] !== $records['employee_personal']['blood_type']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_personal.blood_type.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>              
                    </div>
                </div>
                <div class="tab-pane fade {{$activeTab == 'family' ? 'active show' : ''}} " id="pills-family" role="tabpanel" aria-labelledby="pills-family-tab" tabindex="0">
                    <div class="row mt-3">
                        <div class="col-12 mb-4">
                            <div class="accordion" id="accordionTabFamily">
                                <div class="accordion-item mb-4">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-uppercase fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-parents" aria-expanded="false" aria-controls="flush-parents">
                                            Parents Details
                                        </button>
                                    </h2>
                                    <div id="flush-parents" class="accordion-collapse {{$activeAccordion == 'parents' ? 'collapse show' : ''}}" data-bs-parent="#accordionTabFamily">
                                        <div class="accordion-body">
                                            <div class="row">

                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="mb-2" for="records.employee_parents.spouse_surname.new">Spouse's Surname</label>
                                                    <input type="text" readonly wire:model="records.employee_parents.spouse_surname.new" id="records.employee_parents.spouse_surname.new" class="form-control restricted {{$records['employee_parents']['spouse_surname']['new'] !== $records['employee_parents']['spouse_surname']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_parents.spouse_surname.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="mb-2" for="records.employee_parents.spouse_firstname.new">First Name</label>
                                                    <input type="text" readonly wire:model="records.employee_parents.spouse_firstname.new" id="records.employee_parents.spouse_firstname.new" class="form-control restricted {{$records['employee_parents']['spouse_firstname']['new'] !== $records['employee_parents']['spouse_firstname']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_parents.spouse_firstname.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="mb-2" for="records.employee_parents.spouse_middlename.new">Middle Name</label>
                                                    <input type="text" readonly wire:model="records.employee_parents.spouse_middlename.new" id="records.employee_parents.spouse_middlename.new" class="form-control restricted {{$records['employee_parents']['spouse_middlename']['new'] !== $records['employee_parents']['spouse_middlename']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_parents.spouse_middlename.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="mb-2" for="suffix">Suffix</label>
                                                    <select disabled wire:model="records.employee_parents.spouse_suffix.new" id="records.employee_parents.spouse_suffix.new" class="form-select restricted {{$records['employee_parents']['spouse_suffix']['new'] !== $records['employee_parents']['spouse_suffix']['old'] ? 'border-danger border-3' : ''}}">
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
                                                        @error('records.employee_parents.spouse_suffix.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-12 mb-3">
                                                    <label class="mb-2" for="records.employee_parents.spouse_occupation.new">Occupation</label>
                                                    <input type="text" readonly wire:model="records.employee_parents.spouse_occupation.new" id="records.employee_parents.spouse_occupation.new" class="form-control restricted {{$records['employee_parents']['spouse_occupation']['new'] !== $records['employee_parents']['spouse_occupation']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_parents.spouse_occupation.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="records.employee_parents.spouse_business_name_employer.new">Employer / Business Name</label>
                                                    <input type="text" readonly wire:model="records.employee_parents.spouse_business_name_employer.new" id="records.employee_parents.spouse_business_name_employer.new" class="form-control restricted {{$records['employee_parents']['spouse_business_name_employer']['new'] !== $records['employee_parents']['spouse_business_name_employer']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_parents.spouse_business_name_employer.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="records.employee_parents.spouse_business_address.new">Business Address</label>
                                                    <input type="text" readonly wire:model="records.employee_parents.spouse_business_address.new" id="records.employee_parents.spouse_business_address.new" class="form-control restricted {{$records['employee_parents']['spouse_business_address']['new'] !== $records['employee_parents']['spouse_business_address']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_parents.spouse_business_address.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="records.employee_parents.spouse_contact_no.new">Contact Number</label>
                                                    <input type="text" readonly wire:model="records.employee_parents.spouse_contact_no.new" id="records.employee_parents.spouse_contact_no.new" class="form-control restricted {{$records['employee_parents']['spouse_contact_no']['new'] !== $records['employee_parents']['spouse_contact_no']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_parents.spouse_contact_no.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-4">
                                                    <hr>
                                                </div>

                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="mb-2" for="records.employee_parents.father_surname.new">Father's Surname</label>
                                                    <input type="text" readonly wire:model="records.employee_parents.father_surname.new" id="records.employee_parents.father_surname.new" class="form-control restricted {{$records['employee_parents']['father_surname']['new'] !== $records['employee_parents']['father_surname']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_parents.father_surname.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="mb-2" for="records.employee_parents.father_firstname.new">First Name</label>
                                                    <input type="text" readonly wire:model="records.employee_parents.father_firstname.new" id="records.employee_parents.father_firstname.new" class="form-control restricted {{$records['employee_parents']['father_firstname']['new'] !== $records['employee_parents']['father_firstname']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_parents.father_firstname.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="mb-2" for="records.employee_parents.father_middlename.new">Middle Name</label>
                                                    <input type="text" readonly wire:model="records.employee_parents.father_middlename.new" id="records.employee_parents.father_middlename.new" class="form-control restricted {{$records['employee_parents']['father_middlename']['new'] !== $records['employee_parents']['father_middlename']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_parents.father_middlename.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="mb-2" for="suffix">Suffix</label>
                                                    <select disabled wire:model="records.employee_parents.father_suffix.new" id="records.employee_parents.father_suffix.new" class="form-select restricted {{$records['employee_parents']['father_suffix']['new'] !== $records['employee_parents']['father_suffix']['old'] ? 'border-danger border-3' : ''}}">
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
                                                        @error('records.employee_parents.father_suffix.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-4">
                                                    <hr>
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="records.employee_parents.records.new.employee_parents.mother_surname.new">Mother's Surname</label>
                                                    <input type="text" readonly wire:model="records.employee_parents.mother_surname.new" id="records.employee_parents.mother_surname.new" class="form-control restricted {{$records['employee_parents']['mother_surname']['new'] !== $records['employee_parents']['mother_surname']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_parents.mother_surname.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="records.employee_parents.mother_firstname.new">First Name</label>
                                                    <input type="text" readonly wire:model="records.employee_parents.mother_firstname.new" id="records.employee_parents.mother_firstname.new" class="form-control restricted {{$records['employee_parents']['mother_firstname']['new'] !== $records['employee_parents']['mother_firstname']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_parents.mother_firstname.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label class="mb-2" for="records.employee_parents.mother_middlename.new">Middle Name</label>
                                                    <input type="text" readonly wire:model="records.employee_parents.mother_middlename.new" id="records.employee_parents.mother_middlename.new" class="form-control restricted {{$records['employee_parents']['mother_middlename']['new'] !== $records['employee_parents']['mother_middlename']['old'] ? 'border-danger border-3' : ''}}">
                                                    <div class="error-field">
                                                        @error('records.employee_parents.mother_middlename.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item mb-4">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-uppercase fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-children" aria-expanded="false" aria-controls="flush-children">
                                            Children Details
                                        </button>
                                    </h2>
                                    <div id="flush-children" class="accordion-collapse {{$activeAccordion == 'children' ? 'collapse show' : 'collapse'}}" data-bs-parent="#accordionTabFamily">
                                        <div class="accordion-body mt-4">
                                            @if (!empty($records['employee_children']))
                                                <div class="table-responsive">
                                                    <table class="table table-bordered mt-3">
                                                        <thead>
                                                            <tr>
                                                                <th>First Name</th>
                                                                <th>Middle Name</th>
                                                                <th>Last Name</th>
                                                                <th>Date of Birth</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($records['employee_children'] as $key => $item)
                                                                <tr>
                                                                    <td>
                                                                        <input type="text" readonly wire:model="records.employee_children.{{$key}}.firstname.new" id="records.employee_children.{{$key}}.firstname.new" class="form-control restricted {{$records['employee_children'][$key]['firstname']['new'] !== $records['employee_children'][$key]['firstname']['old'] ? 'border-danger border-3' : ''}}">
                                                                        <div class="error-field">
                                                                            @error('records.employee_children.'.$key.'.firstname') <span class="text-danger">{{ $message }}</span> @enderror
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" readonly wire:model="records.employee_children.{{$key}}.middlename.new" id="records.employee_children.{{$key}}.middlename.new" class="form-control restricted {{$records['employee_children'][$key]['middlename']['new'] !== $records['employee_children'][$key]['middlename']['old'] ? 'border-danger border-3' : ''}}">
                                                                        <div class="error-field">
                                                                            @error('records.employee_children.'.$key.'.middlename') <span class="text-danger">{{ $message }}</span> @enderror
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" readonly wire:model="records.employee_children.{{$key}}.lastname.new" id="records.employee_children.{{$key}}.lastname.new" class="form-control restricted {{$records['employee_children'][$key]['lastname']['new'] !== $records['employee_children'][$key]['lastname']['old'] ? 'border-danger border-3' : ''}}">
                                                                        <div class="error-field">
                                                                            @error('records.employee_children.'.$key.'.lastname') <span class="text-danger">{{ $message }}</span> @enderror
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" wire:model="records.employee_children.{{$key}}.birthdate.new" id="records.employee_children.{{$key}}.birthdate.new" class="form-control restricted {{$records['employee_children'][$key]['birthdate']['new'] !== $records['employee_children'][$key]['birthdate']['old'] ? 'border-danger border-3' : ''}}">
                                                                        <div class="error-field">
                                                                            @error('records.employee_children.'.$key.'.birthdate') <span class="text-danger">{{ $message }}</span> @enderror
                                                                        </div>
                                                                    </td>                                                                        
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>    
                                            @else
                                                <div class="alert alert-info text-uppercase fw-medium text-center">No Children Found.</div>
                                            @endif                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade {{$activeTab == 'education' ? 'active show' : ''}} " id="pills-education" role="tabpanel" aria-labelledby="pills-education-tab" tabindex="0">

                    @if (!empty($records['employee_education']))
                        <div class="table-responsive">
                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>Level</th>
                                        <th>Name of School</th>
                                        <th>Basic Education / Strand / Degree / Course</th>
                                        <th>Attended From</th>
                                        <th>Attended To</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records['employee_education'] as $key => $item)
                                    <tr>
                                        <td>
                                            <select disabled style="width: 300px" wire:model="records.employee_education.{{$key}}.level.new" id="records.employee_education.{{$key}}.level.new" class="form-select restricted {{$records['employee_education'][$key]['level']['new'] !== $records['employee_education'][$key]['level']['old'] ? 'border-danger border-3' : ''}}">
                                                <option value=""> - CHOOSE - </option>
                                                <option value="elementary">Elementary</option>
                                                <option value="secondary">Secondary</option>
                                                <option value="vocational">Vocational</option>
                                                <option value="highschool">High School</option>
                                                <option value="senior_highschool">Senior High School</option>
                                                <option value="college">College</option>
                                                <option value="masters">Masters</option>
                                                <option value="doctoral">Doctoral</option>
                                            </select>                                                
                                            <div class="error-field">
                                                @error('records.employee_education.'.$key.'.level.new') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" readonly style="width: 300px" wire:model="records.employee_education.{{$key}}.school_name.new" id="records.employee_education.{{$key}}.school_name.new" class="form-control restricted {{$records['employee_education'][$key]['school_name']['new'] !== $records['employee_education'][$key]['school_name']['old'] ? 'border-danger border-3' : ''}}">
                                            <div class="error-field">
                                                @error('records.employee_education.'.$key.'.school_name.new') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" readonly style="width: 300px" wire:model="records.employee_education.{{$key}}.course.new" id="records.employee_education.{{$key}}.course.new" class="form-control restricted {{$records['employee_education'][$key]['course']['new'] !== $records['employee_education'][$key]['course']['old'] ? 'border-danger border-3' : ''}}">
                                            <div class="error-field">
                                                @error('records.employee_education.'.$key.'.course.new') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" style="width: 300px" wire:model="records.employee_education.{{$key}}.from_year.new" id="records.employee_education.{{$key}}.from_year.new" class="form-control restricted {{$records['employee_education'][$key]['from_year']['new'] !== $records['employee_education'][$key]['from_year']['old'] ? 'border-danger border-3' : ''}}">
                                            <div class="error-field">
                                                @error('records.employee_education.'.$key.'.from_year.new') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" style="width: 300px" wire:model="records.employee_education.{{$key}}.to_year.new" id="records.employee_education.{{$key}}.to_year.new" class="form-control restricted {{$records['employee_education'][$key]['to_year']['new'] !== $records['employee_education'][$key]['to_year']['old'] ? 'border-danger border-3' : ''}}">
                                            <div class="error-field">
                                                @error('records.employee_education.'.$key.'.to_year.new') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-uppercase fw-medium text-center">No Education Found.</div>
                    @endif

                </div>
                <div class="tab-pane fade {{$activeTab == 'history' ? 'active show' : ''}}" id="pills-history" role="tabpanel" aria-labelledby="pills-history-tab" tabindex="0">
                    @if (!empty($records['employee_employment_history']))
                        <div class="table-responsive">
                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Company Name</th>
                                        <th>Monthly Salary</th>
                                        <th>Employment Status</th>
                                        <th>Is Government?</th>
                                        <th>From</th>
                                        <th>To</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records['employee_employment_history'] as $key => $item)
                                        <tr>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_employment_history.{{$key}}.position.new" id="records.employee_employment_history.{{$key}}.position.new" class="form-control restricted {{$records['employee_employment_history'][$key]['position']['new'] !== $records['employee_employment_history'][$key]['position']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_employment_history.'.$key.'.position.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_employment_history.{{$key}}.department.new" id="records.employee_employment_history.{{$key}}.department.new" class="form-control restricted {{$records['employee_employment_history'][$key]['department']['new'] !== $records['employee_employment_history'][$key]['department']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_employment_history.'.$key.'.department.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_employment_history.{{$key}}.company_name.new" id="records.employee_employment_history.{{$key}}.company_name.new" class="form-control restricted {{$records['employee_employment_history'][$key]['company_name']['new'] !== $records['employee_employment_history'][$key]['company_name']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_employment_history.'.$key.'.company_name.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_employment_history.{{$key}}.monthly_salary.new" id="records.employee_employment_history.{{$key}}.monthly_salary.new" class="form-control restricted {{$records['employee_employment_history'][$key]['monthly_salary']['new'] !== $records['employee_employment_history'][$key]['monthly_salary']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_employment_history.'.$key.'.monthly_salary.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <select disabled style="width: 300px" wire:model="records.employee_employment_history.{{$key}}.employment_status.new" id="records.employee_employment_history.{{$key}}.employment_status.new" class="form-select restricted {{$records['employee_employment_history'][$key]['employment_status']['new'] !== $records['employee_employment_history'][$key]['employment_status']['old'] ? 'border-danger border-3' : ''}}">
                                                    <option value=""> - CHOOSE - </option>
                                                    <option value="regular">Regular</option>
                                                    <option value="part time">Part Time</option>
                                                    <option value="freelance">Freelance</option>
                                                    <option value="project base">Project Base</option>
                                                </select>
                                                <div class="error-field">
                                                    @error('records.employee_employment_history.'.$key.'.employment_status.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <select disabled style="width: 300px" wire:model="records.employee_employment_history.{{$key}}.isGovernment.new" id="records.employee_employment_history.{{$key}}.isGovernment.new" class="form-select restricted {{$records['employee_employment_history'][$key]['isGovernment']['new'] !== $records['employee_employment_history'][$key]['isGovernment']['old'] ? 'border-danger border-3' : ''}}">
                                                    <option value=""> - CHOOSE - </option>
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>
                                                <div class="error-field">
                                                    @error('records.employee_employment_history.'.$key.'.isGovernment.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>                                                
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_employment_history.{{$key}}.from_year.new" id="records.employee_employment_history.{{$key}}.from_year.new" class="form-control restricted {{$records['employee_employment_history'][$key]['from_year']['new'] !== $records['employee_employment_history'][$key]['from_year']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_employment_history.'.$key.'.from_year.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_employment_history.{{$key}}.to_year.new" id="records.employee_employment_history.{{$key}}.to_year.new" class="form-control restricted {{$records['employee_employment_history'][$key]['to_year']['new'] !== $records['employee_employment_history'][$key]['to_year']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_employment_history.'.$key.'.to_year.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>                                                
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-uppercase fw-medium text-center">No Employment History Found.</div>
                    @endif

                </div>
                <div class="tab-pane fade {{$activeTab == 'civil_service' ? 'active show' : ''}}" id="pills-civil-service" role="tabpanel" aria-labelledby="pills-history-tab" tabindex="0">
                
                    @if (!empty($records['employee_civil_service']))
                        <div class="table-responsive">
                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>Certification</th>
                                        <th>Rating</th>
                                        <th>Date of Exam</th>
                                        <th>Place of Exam</th>
                                        <th>License No.</th>
                                        <th>Date of Validity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records['employee_civil_service'] as $key => $item)
                                        <tr>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_civil_service.{{$key}}.certification.new" id="records.employee_civil_service.{{$key}}.certification.new" class="form-control restricted {{$records['employee_civil_service'][$key]['certification']['new'] !== $records['employee_civil_service'][$key]['certification']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_civil_service.'.$key.'.certification.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_civil_service.{{$key}}.rating.new" id="records.employee_civil_service.{{$key}}.rating.new" class="form-control restricted {{$records['employee_civil_service'][$key]['rating']['new'] !== $records['employee_civil_service'][$key]['rating']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_civil_service.'.$key.'.rating.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_civil_service.{{$key}}.date_exam.new" id="records.employee_civil_service.{{$key}}.date_exam.new" class="form-control restricted {{$records['employee_civil_service'][$key]['date_exam']['new'] !== $records['employee_civil_service'][$key]['date_exam']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_civil_service.'.$key.'.date_exam.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_civil_service.{{$key}}.place_exam.new" id="records.employee_civil_service.{{$key}}.place_exam.new" class="form-control restricted {{$records['employee_civil_service'][$key]['place_exam']['new'] !== $records['employee_civil_service'][$key]['place_exam']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_civil_service.'.$key.'.place_exam.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>                                         
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_civil_service.{{$key}}.license_no.new" id="records.employee_civil_service.{{$key}}.license_no.new" class="form-control restricted {{$records['employee_civil_service'][$key]['license_no']['new'] !== $records['employee_civil_service'][$key]['license_no']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_civil_service.'.$key.'.license_no.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_civil_service.{{$key}}.date_validity.new" id="records.employee_civil_service.{{$key}}.date_validity.new" class="form-control restricted {{$records['employee_civil_service'][$key]['date_validity']['new'] !== $records['employee_civil_service'][$key]['date_validity']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_civil_service.'.$key.'.date_validity.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>                                                
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-uppercase fw-medium text-center">No Civil Service Certification Found</div>
                    @endif

                </div>
                <div class="tab-pane fade {{$activeTab == 'trainings' ? 'active show' : ''}}" id="pills-trainings" role="tabpanel" aria-labelledby="pills-history-tab" tabindex="0">
                    
                    @if (!empty($records['employee_trainings']))
                        <div class="table-responsive">
                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Date From</th>
                                        <th>Date To</th>
                                        <th>Consumed Hours</th>
                                        <th>Sponsored By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records['employee_trainings'] as $key => $item)
                                        <tr>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_trainings.{{$key}}.type.new" id="records.employee_trainings.{{$key}}.type.new" class="form-control restricted {{$records['employee_trainings'][$key]['type']['new'] !== $records['employee_trainings'][$key]['type']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_trainings.'.$key.'.type.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_trainings.{{$key}}.name.new" id="records.employee_trainings.{{$key}}.name.new" class="form-control restricted {{$records['employee_trainings'][$key]['name']['new'] !== $records['employee_trainings'][$key]['name']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_trainings.'.$key.'.name.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_trainings.{{$key}}.date_from.new" id="records.employee_trainings.{{$key}}.date_from.new" class="form-control restricted {{$records['employee_trainings'][$key]['date_from']['new'] !== $records['employee_trainings'][$key]['date_from']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_trainings.'.$key.'.date_from.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_trainings.{{$key}}.date_to.new" id="records.employee_trainings.{{$key}}.date_to.new" class="form-control restricted {{$records['employee_trainings'][$key]['date_to']['new'] !== $records['employee_trainings'][$key]['date_to']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_trainings.'.$key.'.date_to.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>  
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_trainings.{{$key}}.consumed_hours.new" id="records.employee_trainings.{{$key}}.consumed_hours.new" class="form-control restricted {{$records['employee_trainings'][$key]['consumed_hours']['new'] !== $records['employee_trainings'][$key]['consumed_hours']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_trainings.'.$key.'.consumed_hours.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>                                         
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_trainings.{{$key}}.sponsored_by.new" id="records.employee_trainings.{{$key}}.sponsored_by.new" class="form-control restricted {{$records['employee_trainings'][$key]['sponsored_by']['new'] !== $records['employee_trainings'][$key]['sponsored_by']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_trainings.'.$key.'.sponsored_by.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>                                        
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-uppercase fw-medium text-center">No Trainings Found</div>
                    @endif

                </div>
                <div class="tab-pane fade {{$activeTab == 'others' ? 'active show' : ''}}" id="pills-others" role="tabpanel" aria-labelledby="pills-history-tab" tabindex="0">
                    @if (!empty($records['employee_others']))
                        <div class="table-responsive">
                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>Organization</th>
                                        <th>Address</th>
                                        <th>Date From</th>
                                        <th>Date To</th>
                                        <th>Consumed Hours</th>
                                        <th>Position</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records['employee_others'] as $key => $item)
                                        <tr>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_others.{{$key}}.organization.new" id="records.employee_others.{{$key}}.organization.new" class="form-control restricted {{$records['employee_others'][$key]['organization']['new'] !== $records['employee_others'][$key]['organization']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_others.'.$key.'.organization.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_others.{{$key}}.address.new" id="records.employee_others.{{$key}}.address.new" class="form-control restricted {{$records['employee_others'][$key]['address']['new'] !== $records['employee_others'][$key]['address']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_others.'.$key.'.address.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_others.{{$key}}.date_from.new" id="records.employee_others.{{$key}}.date_from.new" class="form-control restricted {{$records['employee_others'][$key]['date_from']['new'] !== $records['employee_others'][$key]['date_from']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_others.'.$key.'.date_from.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_others.{{$key}}.date_to.new" id="records.employee_others.{{$key}}.date_to.new" class="form-control restricted {{$records['employee_others'][$key]['date_to']['new'] !== $records['employee_others'][$key]['date_to']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_others.'.$key.'.date_to.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>  
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_others.{{$key}}.consumed_hours.new" id="records.employee_others.{{$key}}.consumed_hours.new" class="form-control restricted {{$records['employee_others'][$key]['consumed_hours']['new'] !== $records['employee_others'][$key]['consumed_hours']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_others.'.$key.'.consumed_hours.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>                                         
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_others.{{$key}}.position.new" id="records.employee_others.{{$key}}.position.new" class="form-control restricted {{$records['employee_others'][$key]['position']['new'] !== $records['employee_others'][$key]['position']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_others.'.$key.'.position.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>                                        
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-uppercase fw-medium text-center">No Voluntary Works Found</div>
                    @endif

                </div>
                <div class="tab-pane fade {{$activeTab == 'skills' ? 'active show' : ''}}" id="pills-skills" role="tabpanel" aria-labelledby="pills-history-tab" tabindex="0">
                
                    @if (!empty($records['employee_skills']))
                        <div class="table-responsive">
                            <table class="table table-bordered mt-3 w-100">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Recognition</th>
                                        <th>Organization</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records['employee_skills'] as $key => $item)
                                        <tr>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_skills.{{$key}}.name.new" id="records.employee_skills.{{$key}}.name.new" class="form-control restricted {{$records['employee_skills'][$key]['name']['new'] !== $records['employee_skills'][$key]['name']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_skills.'.$key.'.name.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_skills.{{$key}}.recognition.new" id="records.employee_skills.{{$key}}.recognition.new" class="form-control restricted {{$records['employee_skills'][$key]['recognition']['new'] !== $records['employee_skills'][$key]['recognition']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_skills.'.$key.'.recognition.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input style="width: 300px" type="text" wire:model="records.employee_skills.{{$key}}.organization.new" id="records.employee_skills.{{$key}}.organization.new" class="form-control restricted {{$records['employee_skills'][$key]['organization']['new'] !== $records['employee_skills'][$key]['organization']['old'] ? 'border-danger border-3' : ''}}">
                                                <div class="error-field">
                                                    @error('records.employee_skills.'.$key.'.organization.new') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>                                
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-uppercase fw-medium text-center">No Voluntary Works Found</div>
                    @endif
                </div>
            </div>
        </div>
        @if (!empty($records))
            <div class="card-footer px-5 d-flex gap-3 justify-content-end bg-transparent border-0">
                <div class="text-end">
                    <button type="submit" wire:click="disapproved" wire:target="disapproved" wire:loading.attr="disabled" class="btn btn-danger py-3 px-5 mt-2 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="disapproved">Disapproved</span>    
                        <span wire:loading wire:target="disapproved">Disapproving <i class="fa-solid fa-spinner fa-spin"></i>
                    </button>
                    <div class="mt-3 pb-5">
                        @if ($errors->any())
                            <small class="text-danger">There's an error upon submitting, please review your form.</small>
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" wire:click="approved" wire:target="approved" wire:loading.attr="disabled" class="btn btn-primary py-3 px-5 mt-2 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="approved">Approve</span>    
                        <span wire:loading wire:target="approved">Approving <i class="fa-solid fa-spinner fa-spin"></i>
                    </button>
                    <div class="mt-3 pb-5">
                        @if ($errors->any())
                            <small class="text-danger">There's an error upon submitting, please review your form.</small>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
