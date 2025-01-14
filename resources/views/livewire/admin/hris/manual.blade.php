<div>
    <div class="d-flex justify-content-end mb-5 gap-3">
        <a href="{{route('hris.index')}}" class="btn btn-outline-primary px-5 py-3 text-uppercase">Go Back</a>
    </div>
    <form wire:submit.prevent="save">
        <div class="card mb-4 border-0">
            <div class="card-header border-0 bg-transparent">
                <h5 class="mb-0 text-uppercase fw-bold pt-4 pb-0 px-3">Employee Details</h5>
            </div>
            <div class="card-body px-4">
                <div class="row my-3">
                    <div class="col-12 mb-5">
                        <div class="row">
                          
                        </div>
                    </div>  
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="employee_no">Employee No.</label>
                        <input type="text" wire:model="records.employee_information.employee_no" id="records.employee_information.employee_no" class="form-control">
                        <div class="error-field">
                            @error('records.employee_information.employee_no') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="biometrics_id">Biometrics ID</label>
                        <input type="number" wire:model="records.employee_information.biometrics_id" id="records.employee_information.biometrics_id" class="form-control">
                        <div class="error-field">
                            @error('records.employee_information.biometrics_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>  
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="date_hired">Date Hired</label>
                        <input type="date" wire:model="records.employee_information.date_hired" id="records.employee_information.date_hired" class="form-control">
                        <div class="error-field">
                            @error('records.employee_information.date_hired') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div> 
                    <div class="col-12 mb-4">
                        <hr>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="section_id">Office</label>
                        <select wire:model="records.employee_information.section_id" wire:change="select_change('section')" id="records.employee_information.section_id" class="form-select">
                            <option value=""> - CHOOSE - </option>
                            @foreach ($sections as $section)
                                <option value="{{$section->id}}">{{$section->code . ' - ' . $section->name}}</option>
                            @endforeach
                        </select>
                        <div class="error-field">
                            @error('records.employee_information.section_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>  
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="branch">Branch</label>
                        <input type="text" wire:model="records.employee_information.branch" id="records.employee_information.branch" class="form-control restricted" readonly>
                        <div class="error-field">
                            @error('records.employee_information.branch') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>  
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="department">Department</label>
                        <input type="text" wire:model="records.employee_information.department" id="records.employee_information.department" class="form-control restricted" readonly>
                        <div class="error-field">
                            @error('records.employee_information.department') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>  
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="position_id">Position</label>
                        <select wire:model="records.employee_information.position_id" id="records.employee_information.position_id" class="form-select">
                            <option value=""> - CHOOSE - </option>
                            @foreach ($positions as $position)
                                <option value="{{$position->id}}">{{$position->name}}</option>
                            @endforeach
                        </select>
                        <div class="error-field">
                            @error('records.employee_information.position_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>  
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="type">Employment Type</label>
                        <select wire:model="records.employee_information.type" id="records.employee_information.type" class="form-select">
                            <option value=""> - CHOOSE - </option>
                            @foreach ($employmentTypes as $types)
                                <option value="{{strtolower($types->id)}}">{{$types->name}}</option>
                            @endforeach
                        </select>
                        <div class="error-field">
                            @error('records.employee_information.type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div> 
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="status">Account Status</label>
                        <select wire:model="records.employee_information.status" id="records.employee_information.status" class="form-select">
                            <option value=""> - CHOOSE - </option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <div class="error-field">
                            @error('records.employee_information.status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div> 
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="salary_method">Salary Method</label>
                        <select wire:model="records.employee_information.salary_method" id="records.employee_information.salary_method" class="form-select">
                            <option value=""> - CHOOSE - </option>
                            <option value="cash">Cash</option>
                            <option value="bank transfer">Bank Transfer</option>
                            <option value="paycheck">Paycheck</option>
                            <option value="e-wallet">E-Wallet</option>
                        </select>
                        <div class="error-field">
                            @error('records.employee_information.salary_method') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div> 
                    <div class="col-12 col-md-3 mb-3">
                        <label class="mb-2" for="monthly_rate">Monthly Rate</label>
                        <input type="text" wire:model="records.employee_information.monthly_rate" id="records.employee_information.monthly_rate" class="form-control">
                        <div class="error-field">
                            @error('records.employee_information.monthly_rate') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>  
                    <div class="col-12 col-md-4 mb-3">
                        <label class="mb-2" for="payroll_account_number">Payroll Account No.</label>
                        <input type="text" wire:model="records.employee_information.payroll_account_number" id="records.employee_information.payroll_account_number" class="form-control">
                        <div class="error-field">
                            @error('records.employee_information.payroll_account_number') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>                  
                </div>
                <hr class="mt-5">
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <ul class="nav nav-pills mb-3 d-flex justify-content-center gap-3 py-4" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button wire:click="setActiveTab('details')" class="nav-link text-uppercase fw-bold {{$activeTab == 'details' ? 'active' : ''}}" id="pills-details-tab" data-bs-toggle="pill" data-bs-target="#pills-details" type="button" role="tab" aria-controls="pills-details" aria-selected="true">Employee Details</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="setActiveTab('family')" class="nav-link text-uppercase fw-bold {{$activeTab == 'family' ? 'active' : ''}}" id="pills-family-tab" data-bs-toggle="pill" data-bs-target="#pills-family" type="button" role="tab" aria-controls="pills-family" aria-selected="false">Family Background</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="setActiveTab('education')" class="nav-link text-uppercase fw-bold {{$activeTab == 'education' ? 'active' : ''}}" id="pills-education-tab" data-bs-toggle="pill" data-bs-target="#pills-education" type="button" role="tab" aria-controls="pills-education" aria-selected="false">Education Information</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="setActiveTab('history')" class="nav-link text-uppercase fw-bold {{$activeTab == 'history' ? 'active' : ''}}" id="pills-history-tab" data-bs-toggle="pill" data-bs-target="#pills-history" type="button" role="tab" aria-controls="pills-history" aria-selected="false">Employment History</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="setActiveTab('civil_service')" class="nav-link text-uppercase fw-bold {{$activeTab == 'civil_service' ? 'active' : ''}}" id="pills-civil-service-tab" data-bs-toggle="pill" data-bs-target="#pills-civil-service" type="button" role="tab" aria-controls="pills-civil-service" aria-selected="false">Civil Service Eligibility</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="setActiveTab('trainings')" class="nav-link text-uppercase fw-bold {{$activeTab == 'trainings' ? 'active' : ''}}" id="pills-trainings-tab" data-bs-toggle="pill" data-bs-target="#pills-trainings" type="button" role="tab" aria-controls="pills-trainings" aria-selected="false">Trainings</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="setActiveTab('others')" class="nav-link text-uppercase fw-bold {{$activeTab == 'others' ? 'active' : ''}}" id="pills-others-tab" data-bs-toggle="pill" data-bs-target="#pills-others" type="button" role="tab" aria-controls="pills-others" aria-selected="false">Other Voluntary Works</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="setActiveTab('skills')" class="nav-link text-uppercase fw-bold {{$activeTab == 'skills' ? 'active' : ''}}" id="pills-skills-tab" data-bs-toggle="pill" data-bs-target="#pills-skills" type="button" role="tab" aria-controls="pills-skills" aria-selected="false">Skills or Hobbies</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="setActiveTab('account')" class="nav-link text-uppercase fw-bold {{$activeTab == 'account' ? 'active' : ''}}" id="pills-account-tab" data-bs-toggle="pill" data-bs-target="#pills-account" type="button" role="tab" aria-controls="pills-account" aria-selected="false">Account</button>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade {{$activeTab == 'details' ? 'active show' : ''}} p-0" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab" tabindex="0">
                        <div class="row mt-3">
                            <div class="col-12 mb-4">
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
                                                        <input type="text" wire:model="records.employee_personal.lastname" id="lastname" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.lastname') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>  
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="firstname">First Name</label>
                                                        <input type="text" wire:model="records.employee_personal.firstname" id="firstname" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.firstname') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>  
                                                    <div class="col-12 col-md-3 mb-3">
                                                        <label class="mb-2" for="middlename">Middle Name</label>
                                                        <input type="text" wire:model="records.employee_personal.middlename" id="middlename" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.middlename') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>  
                                                    <div class="col-12 col-md-2 mb-3">
                                                        <label class="mb-2" for="suffix">Suffix</label>
                                                        <select wire:model="records.employee_personal.suffix" id="suffix" class="form-select">
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
                                                            @error('records.employee_personal.suffix') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>  
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="birthday">Date of Birth</label>
                                                        <input type="date" wire:model="records.employee_personal.birthday" id="birthday" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.birthday') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>  
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="civil_status">Civil Status</label>
                                                        <select wire:model="records.employee_personal.civil_status" id="civil_status" class="form-select">
                                                            <option value=""> - CHOOSE - </option>
                                                            <option value="single">Single</option>
                                                            <option value="married">Married</option>
                                                            <option value="divorced">Divorced</option>
                                                            <option value="separated">Separated</option>
                                                            <option value="widowed">Widowed</option>
                                                            <option value="annulled">Annulled</option>
                                                        </select>
                                                        <div class="error-field">
                                                            @error('records.employee_personal.civil_status') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>  
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="sex">Sex</label>
                                                        <select wire:model="records.employee_personal.sex" id="sex" class="form-select">
                                                            <option value=""> - CHOOSE - </option>
                                                            <option value="male">Male</option>
                                                            <option value="female">Female</option>
                                                        </select>
                                                        <div class="error-field">
                                                            @error('records.employee_personal.sex') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>  
                                                    <div class="col-12 mb-3">
                                                        <hr>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="citizenship">Citizenship</label>
                                                        <select wire:model="records.employee_personal.citizenship" wire:change="select_change('citizenship')"  id="citizenship" class="form-select">
                                                            <option value=""> - CHOOSE - </option>
                                                            <option value="filipino">Filipino</option>
                                                            <option value="dual_citizenship">Dual Citizenship</option>
                                                        </select>
                                                        <div class="error-field">
                                                            @error('records.employee_personal.citizenship') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    @if ($isDualCitizenship)
                                                        <div class="col-12 col-md-4 mb-3">    
                                                            <label class="mb-2" for="country">Country (Dual Citizenship)</label>
                                                                <select wire:model="records.employee_personal.country" id="citizenship_type" class="form-select">
                                                                    <option value=""> - CHOOSE - </option>
                                                                    @foreach ($countries as $country)
                                                                        <option value="{{$country['name']['common']}}">{{$country['name']['common']}}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="error-field">
                                                                    @error('records.employee_personal.country') <span class="text-danger">{{ $message }}</span> @enderror
                                                                </div>
                                                            </div>
                                                        @endif
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="citizenship_type">Citizenship Type</label>
                                                        <select wire:model="records.employee_personal.citizenship_type" id="citizenship_type" class="form-select">
                                                            <option value=""> - CHOOSE - </option>
                                                            <option value="by_birth">By Birth</option>
                                                            <option value="by_naturalization">By Naturalization</option>
                                                        </select>
                                                        <div class="error-field">
                                                            @error('records.employee_personal.citizenship_type') <span class="text-danger">{{ $message }}</span> @enderror
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
                                                        <input type="text" wire:model="records.employee_personal.present_address" id="present_address" class="form-control" placeholder="House / Block / Lot / Street / Subdivision / Village / Barangay">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.present_address') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>  
                                                    <div class="col-12 col-md-6 mb-3">
                                                        <label class="mb-2" for="present_province">State / Province</label>
                                                        <input type="text" wire:model="records.employee_personal.present_province" id="present_province" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.present_province') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>  
                                                    <div class="col-12 col-md-6 mb-3">
                                                        <label class="mb-2" for="present_city">City / Municipality</label>
                                                        <input type="text" wire:model="records.employee_personal.present_city" id="present_city" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.present_city') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div> 
                                                    <div class="col-12 mb-3">
                                                        <hr>
                                                    </div> 
                                                    <div class="col-12 col-md-12 mb-3">
                                                        <label class="mb-2" for="permanent_address">Permanent Address</label>
                                                        <input type="text" wire:model="records.employee_personal.permanent_address" id="permanent_address" class="form-control" placeholder="House / Block / Lot / Street / Subdivision / Village / Barangay">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.permanent_address') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>  
                                                    <div class="col-12 col-md-6 mb-3">
                                                        <label class="mb-2" for="permanent_province">State / Province</label>
                                                        <input type="text" wire:model="records.employee_personal.permanent_province" id="permanent_province" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.permanent_province') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>  
                                                    <div class="col-12 col-md-6 mb-3">
                                                        <label class="mb-2" for="permanent_city">City / Municipality</label>
                                                        <input type="text" wire:model="records.employee_personal.permanent_city" id="permanent_city" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.permanent_city') <span class="text-danger">{{ $message }}</span> @enderror
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
                                                        <input type="text" wire:model="records.employee_personal.mobile_number" id="mobile_number" class="form-control" data-mask="mobile">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.mobile_number') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>  
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="tel_no">Telephone No.</label>
                                                        <input type="text" wire:model="records.employee_personal.tel_no" id="tel_no" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.tel_no') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>  
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="email">Email</label>
                                                        <input type="text" wire:model="records.employee_personal.email" id="email" class="form-control text-lowercase">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.email') <span class="text-danger">{{ $message }}</span> @enderror
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
                                                        <input type="text" wire:model="records.employee_personal.height" id="height" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.height') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6 mb-3">
                                                        <label class="mb-2" for="weight">Weight</label>
                                                        <input type="text" wire:model="records.employee_personal.weight" id="weight" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.weight') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6 mb-3">
                                                        <label class="mb-2" for="blood_type">Blood Type</label>
                                                        <input type="text" wire:model="records.employee_personal.blood_type" id="blood_type" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.blood_type') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item mb-4">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button text-uppercase fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-benefits" aria-expanded="false" aria-controls="flush-benefits">
                                                Identification Numbers
                                            </button>
                                        </h2>
                                        <div id="flush-benefits" class="accordion-collapse collapse  {{$activeAccordion === 'identification' ? 'collapse show' : ''}}" data-bs-parent="#accordionTabPersonal">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="gsis_no">GSIS No.</label>
                                                        <input type="text" wire:model="records.employee_personal.gsis_no" id="gsis_no" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.gsis_no') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="pagibig_no">Pagibig No.</label>
                                                        <input type="text" wire:model="records.employee_personal.pagibig_no" id="pagibig_no" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.pagibig_no') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="philhealth_no">Philhealth No.</label>
                                                        <input type="text" wire:model="records.employee_personal.philhealth_no" id="philhealth_no" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.philhealth_no') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="sss_no">SSS No.</label>
                                                        <input type="text" wire:model="records.employee_personal.sss_no" id="sss_no" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.sss_no') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="tin_no">Tin No.</label>
                                                        <input type="text" wire:model="records.employee_personal.tin_no" id="tin_no" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_personal.tin_no') <span class="text-danger">{{ $message }}</span> @enderror
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
                                                        <label class="mb-2" for="records.employee_parents.spouse_surname">Spouse's Surname</label>
                                                        <input type="text" wire:model="records.employee_parents.spouse_surname" id="records.employee_parents.spouse_surname" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_parents.spouse_surname') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-3">
                                                        <label class="mb-2" for="records.employee_parents.spouse_firstname">First Name</label>
                                                        <input type="text" wire:model="records.employee_parents.spouse_firstname" id="records.employee_parents.spouse_firstname" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_parents.spouse_firstname') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-3">
                                                        <label class="mb-2" for="records.employee_parents.spouse_middlename">Middle Name</label>
                                                        <input type="text" wire:model="records.employee_parents.spouse_middlename" id="records.employee_parents.spouse_middlename" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_parents.spouse_middlename') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-3">
                                                        <label class="mb-2" for="suffix">Suffix</label>
                                                        <select wire:model="records.employee_parents.spouse_suffix" id="records.employee_parents.spouse_suffix" class="form-select">
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
                                                            @error('records.employee_parents.spouse_suffix') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-12 mb-3">
                                                        <label class="mb-2" for="records.employee_parents.spouse_occupation">Occupation</label>
                                                        <input type="text" wire:model="records.employee_parents.spouse_occupation" id="records.employee_parents.spouse_occupation" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_parents.spouse_occupation') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="records.employee_parents.spouse_business_name_employer">Employer / Business Name</label>
                                                        <input type="text" wire:model="records.employee_parents.spouse_business_name_employer" id="records.employee_parents.spouse_business_name_employer" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_parents.spouse_business_name_employer') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="records.employee_parents.spouse_business_address">Business Address</label>
                                                        <input type="text" wire:model="records.employee_parents.spouse_business_address" id="records.employee_parents.spouse_business_address" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_parents.spouse_business_address') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="records.employee_parents.spouse_contact_no">Contact Number</label>
                                                        <input type="text" wire:model="records.employee_parents.spouse_contact_no" id="records.employee_parents.spouse_contact_no" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_parents.spouse_contact_no') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-4">
                                                        <hr>
                                                    </div>

                                                    <div class="col-12 col-md-3 mb-3">
                                                        <label class="mb-2" for="records.employee_parents.father_surname">Father's Surname</label>
                                                        <input type="text" wire:model="records.employee_parents.father_surname" id="records.employee_parents.father_surname" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_parents.father_surname') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-3">
                                                        <label class="mb-2" for="records.employee_parents.father_firstname">First Name</label>
                                                        <input type="text" wire:model="records.employee_parents.father_firstname" id="records.employee_parents.father_firstname" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_parents.father_firstname') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-3">
                                                        <label class="mb-2" for="records.employee_parents.father_middlename">Middle Name</label>
                                                        <input type="text" wire:model="records.employee_parents.father_middlename" id="records.employee_parents.father_middlename" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_parents.father_middlename') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-3">
                                                        <label class="mb-2" for="suffix">Suffix</label>
                                                        <select wire:model="records.employee_parents.father_suffix" id="records.employee_parents.father_suffix" class="form-select">
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
                                                            @error('records.employee_parents.father_suffix') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-4">
                                                        <hr>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="records.employee_parents.records.employee_parents.mother_surname">Mother's Surname</label>
                                                        <input type="text" wire:model="records.employee_parents.mother_surname" id="records.employee_parents.mother_surname" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_parents.mother_surname') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="records.employee_parents.mother_firstname">First Name</label>
                                                        <input type="text" wire:model="records.employee_parents.mother_firstname" id="records.employee_parents.mother_firstname" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_parents.mother_firstname') <span class="text-danger">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <label class="mb-2" for="records.employee_parents.mother_middlename">Middle Name</label>
                                                        <input type="text" wire:model="records.employee_parents.mother_middlename" id="records.employee_parents.mother_middlename" class="form-control">
                                                        <div class="error-field">
                                                            @error('records.employee_parents.mother_middlename') <span class="text-danger">{{ $message }}</span> @enderror
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
                                                <div class="d-flex justify-content-end mb-4">
                                                    <button type="button" class="btn btn-info ms-auto text-white" wire:click="addRecord('family', 'employee_children', 'children')">Add Record</button>
                                                </div>
                                                @if (!empty($records['employee_children']))
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered mt-3">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
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
                                                                            <button type="button" class="btn btn-danger" wire:click="removeRecord('family', 'employee_children', '{{$key}}')">
                                                                                <i class="fa-solid fa-circle-minus"></i>
                                                                            </button>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" wire:model="records.employee_children.{{$key}}.firstname" id="records.employee_children.{{$key}}.firstname" class="form-control">
                                                                            <div class="error-field">
                                                                                @error('records.employee_children.'.$key.'.firstname') <span class="text-danger">{{ $message }}</span> @enderror
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" wire:model="records.employee_children.{{$key}}.middlename" id="records.employee_children.{{$key}}.middlename" class="form-control">
                                                                            <div class="error-field">
                                                                                @error('records.employee_children.'.$key.'.middlename') <span class="text-danger">{{ $message }}</span> @enderror
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" wire:model="records.employee_children.{{$key}}.lastname" id="records.employee_children.{{$key}}.lastname" class="form-control">
                                                                            <div class="error-field">
                                                                                @error('records.employee_children.'.$key.'.lastname') <span class="text-danger">{{ $message }}</span> @enderror
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <input type="date" wire:model="records.employee_children.{{$key}}.birthdate" id="records.employee_children.{{$key}}.birthdate" class="form-control">
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

                        <div class="d-flex justify-content-end mb-4">
                            <button type="button" class="btn btn-info ms-auto text-white" wire:click="addRecord('education', 'employee_education')">Add Record</button>
                        </div>

                        @if (!empty($records['employee_education']))
                            <div class="table-responsive">
                                <table class="table table-bordered mt-3">
                                    <thead>
                                        <tr>
                                            <th></th>
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
                                                <button type="button" class="btn btn-danger" wire:click="removeRecord('education', 'employee_education', {{$key}})">
                                                    <i class="fa-solid fa-circle-minus"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <select style="width: 300px" wire:model="records.employee_education.{{$key}}.level" id="records.employee_education.{{$key}}.level" class="form-select">
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
                                                    @error('records.employee_education.'.$key.'.level') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" style="width: 300px" wire:model="records.employee_education.{{$key}}.school_name" id="records.employee_education.{{$key}}.school_name" class="form-control">
                                                <div class="error-field">
                                                    @error('records.employee_education.'.$key.'.school_name') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" style="width: 300px" wire:model="records.employee_education.{{$key}}.course" id="records.employee_education.{{$key}}.course" class="form-control">
                                                <div class="error-field">
                                                    @error('records.employee_education.'.$key.'.course') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input type="date" style="width: 300px" wire:model="records.employee_education.{{$key}}.from_year" id="records.employee_education.{{$key}}.from_year" class="form-control">
                                                <div class="error-field">
                                                    @error('records.employee_education.'.$key.'.from_year') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <input type="date" style="width: 300px" wire:model="records.employee_education.{{$key}}.to_year" id="records.employee_education.{{$key}}.to_year" class="form-control">
                                                <div class="error-field">
                                                    @error('records.employee_education.'.$key.'.to_year') <span class="text-danger">{{ $message }}</span> @enderror
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
                        
                        <div class="d-flex justify-content-end mb-4">
                            <button type="button" class="btn btn-info ms-auto text-white" wire:click="addRecord('history', 'employee_employment_history')">Add Record</button>
                        </div>

                        @if (!empty($records['employee_employment_history']))
                            <div class="table-responsive">
                                <table class="table table-bordered mt-3">
                                    <thead>
                                        <tr>
                                            <th></th>
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
                                                    <button type="button" class="btn btn-danger" wire:click="removeRecord('history', 'employee_employment_history', {{$key}})">
                                                        <i class="fa-solid fa-circle-minus"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_employment_history.{{$key}}.position" id="records.employee_employment_history.{{$key}}.position" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_employment_history.'.$key.'.position') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_employment_history.{{$key}}.department" id="records.employee_employment_history.{{$key}}.department" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_employment_history.'.$key.'.department') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_employment_history.{{$key}}.company_name" id="records.employee_employment_history.{{$key}}.company_name" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_employment_history.'.$key.'.company_name') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_employment_history.{{$key}}.monthly_salary" id="records.employee_employment_history.{{$key}}.monthly_salary" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_employment_history.'.$key.'.monthly_salary') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <select style="width: 300px" wire:model="records.employee_employment_history.{{$key}}.employment_status" id="records.employee_employment_history.{{$key}}.employment_status" class="form-select">
                                                        <option value=""> - CHOOSE - </option>
                                                        <option value="regular">Regular</option>
                                                        <option value="part time">Part Time</option>
                                                        <option value="freelance">Freelance</option>
                                                        <option value="project base">Project Base</option>
                                                    </select>
                                                    <div class="error-field">
                                                        @error('records.employee_employment_history.'.$key.'.employment_status') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <select style="width: 300px" wire:model="records.employee_employment_history.{{$key}}.isGovernment" id="records.employee_employment_history.{{$key}}.isGovernment" class="form-select">
                                                        <option value=""> - CHOOSE - </option>
                                                        <option value="yes">Yes</option>
                                                        <option value="no">No</option>
                                                    </select>
                                                    <div class="error-field">
                                                        @error('records.employee_employment_history.'.$key.'.isGovernment') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>                                                
                                                <td>
                                                    <input style="width: 300px" type="date" wire:model="records.employee_employment_history.{{$key}}.from_year" id="records.employee_employment_history.{{$key}}.from_year" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_employment_history.'.$key.'.from_year') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="date" wire:model="records.employee_employment_history.{{$key}}.to_year" id="records.employee_employment_history.{{$key}}.to_year" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_employment_history.'.$key.'.to_year') <span class="text-danger">{{ $message }}</span> @enderror
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
                        
                        <div class="d-flex justify-content-end mb-4">
                            <button type="button" class="btn btn-info ms-auto text-white" wire:click="addRecord('civil_service', 'employee_civil_service')">Add Record</button>
                        </div>

                        @if (!empty($records['employee_civil_service']))
                            <div class="table-responsive">
                                <table class="table table-bordered mt-3">
                                    <thead>
                                        <tr>
                                            <th></th>
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
                                                    <button type="button" class="btn btn-danger" wire:click="removeRecord('civil_service', 'employee_civil_service', {{$key}})">
                                                        <i class="fa-solid fa-circle-minus"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_civil_service.{{$key}}.certification" id="records.employee_civil_service.{{$key}}.certification" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_civil_service.'.$key.'.certification') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_civil_service.{{$key}}.rating" id="records.employee_civil_service.{{$key}}.rating" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_civil_service.'.$key.'.rating') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="date" wire:model="records.employee_civil_service.{{$key}}.date_exam" id="records.employee_civil_service.{{$key}}.date_exam" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_civil_service.'.$key.'.date_exam') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_civil_service.{{$key}}.place_exam" id="records.employee_civil_service.{{$key}}.place_exam" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_civil_service.'.$key.'.place_exam') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>                                         
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_civil_service.{{$key}}.license_no" id="records.employee_civil_service.{{$key}}.license_no" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_civil_service.'.$key.'.license_no') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="date" wire:model="records.employee_civil_service.{{$key}}.date_validity" id="records.employee_civil_service.{{$key}}.date_validity" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_civil_service.'.$key.'.date_validity') <span class="text-danger">{{ $message }}</span> @enderror
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
                        
                        <div class="d-flex justify-content-end mb-4">
                            <button type="button" class="btn btn-info ms-auto text-white" wire:click="addRecord('trainings', 'employee_trainings')">Add Record</button>
                        </div>

                        @if (!empty($records['employee_trainings']))
                            <div class="table-responsive">
                                <table class="table table-bordered mt-3">
                                    <thead>
                                        <tr>
                                            <th></th>
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
                                                    <button type="button" class="btn btn-danger" wire:click="removeRecord('civil_service', 'employee_trainings', {{$key}})">
                                                        <i class="fa-solid fa-circle-minus"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_trainings.{{$key}}.type" id="records.employee_trainings.{{$key}}.type" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_trainings.'.$key.'.type') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_trainings.{{$key}}.name" id="records.employee_trainings.{{$key}}.name" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_trainings.'.$key.'.name') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="date" wire:model="records.employee_trainings.{{$key}}.date_from" id="records.employee_trainings.{{$key}}.date_from" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_trainings.'.$key.'.date_from') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="date" wire:model="records.employee_trainings.{{$key}}.date_to" id="records.employee_trainings.{{$key}}.date_to" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_trainings.'.$key.'.date_to') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>  
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_trainings.{{$key}}.consumed_hours" id="records.employee_trainings.{{$key}}.consumed_hours" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_trainings.'.$key.'.consumed_hours') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>                                         
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_trainings.{{$key}}.sponsored_by" id="records.employee_trainings.{{$key}}.sponsored_by" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_trainings.'.$key.'.sponsored_by') <span class="text-danger">{{ $message }}</span> @enderror
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
                        
                        <div class="d-flex justify-content-end mb-4">
                            <button type="button" class="btn btn-info ms-auto text-white" wire:click="addRecord('others', 'employee_others')">Add Record</button>
                        </div>

                        @if (!empty($records['employee_others']))
                            <div class="table-responsive">
                                <table class="table table-bordered mt-3">
                                    <thead>
                                        <tr>
                                            <th></th>
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
                                                    <button type="button" class="btn btn-danger" wire:click="removeRecord('others', 'employee_others', {{$key}})">
                                                        <i class="fa-solid fa-circle-minus"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_others.{{$key}}.organization" id="records.employee_others.{{$key}}.organization" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_others.'.$key.'.organization') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_others.{{$key}}.address" id="records.employee_others.{{$key}}.address" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_others.'.$key.'.address') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="date" wire:model="records.employee_others.{{$key}}.date_from" id="records.employee_others.{{$key}}.date_from" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_others.'.$key.'.date_from') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="date" wire:model="records.employee_others.{{$key}}.date_to" id="records.employee_others.{{$key}}.date_to" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_others.'.$key.'.date_to') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>  
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_others.{{$key}}.consumed_hours" id="records.employee_others.{{$key}}.consumed_hours" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_others.'.$key.'.consumed_hours') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>                                         
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_others.{{$key}}.position" id="records.employee_others.{{$key}}.position" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_others.'.$key.'.position') <span class="text-danger">{{ $message }}</span> @enderror
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
                        
                        <div class="d-flex justify-content-end mb-4">
                            <button type="button" class="btn btn-info ms-auto text-white" wire:click="addRecord('skills', 'employee_skills')">Add Record</button>
                        </div>

                        @if (!empty($records['employee_skills']))
                            <div class="table-responsive">
                                <table class="table table-bordered mt-3 w-100">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Name</th>
                                            <th>Recognition</th>
                                            <th>Organization</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($records['employee_skills'] as $key => $item)
                                            <tr>
                                                <td>
                                                    <button type="button" class="btn btn-danger" wire:click="removeRecord('skills', 'employee_skills', {{$key}})">
                                                        <i class="fa-solid fa-circle-minus"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_skills.{{$key}}.name" id="records.employee_skills.{{$key}}.name" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_skills.'.$key.'.name') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_skills.{{$key}}.recognition" id="records.employee_skills.{{$key}}.recognition" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_skills.'.$key.'.recognition') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <input style="width: 300px" type="text" wire:model="records.employee_skills.{{$key}}.organization" id="records.employee_skills.{{$key}}.organization" class="form-control">
                                                    <div class="error-field">
                                                        @error('records.employee_skills.'.$key.'.organization') <span class="text-danger">{{ $message }}</span> @enderror
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

                    <div class="tab-pane fade {{$activeTab == 'account' ? 'active show' : ''}} " id="pills-account" role="tabpanel" aria-labelledby="pills-account-tab" tabindex="0">
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-12 col-md-12 mb-3">
                                    <label class="mb-2" for="records.employee_account.email">Email ID</label>
                                    <input type="email" class="form-control restricted text-uppercase" placeholder="System Generated" readonly>
                                    <div class="error-field">
                                        @error('records.employee_account.email') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 mb-3">
                                    <label class="mb-2" for="records.employee_account.password">Password</label>
                                    <input type="password" wire:model="records.employee_account.password" id="records.employee_account.password" class="form-control">
                                    <div class="error-field">
                                        @error('records.employee_account.password') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 mb-3">
                                    <label class="mb-2" for="records.employee_account.confirm_password">Confirm Password</label>
                                    <input type="password" wire:model="records.employee_account.confirm_password" id="records.employee_account.confirm_password" class="form-control">
                                    <div class="error-field">
                                        @error('records.employee_account.confirm_password') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 mb-3">
                                    <p class="text-uppercase fw-bold text-muted" style="font-size:10px">By saving this account, we will send a notification to the employee's email associated with their username and password.</p>
                                </div>    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end bg-transparent border-0">
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                        <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                    </button>
                    <div class="mt-3 pb-5">
                        @if ($errors->any())
                            <small class="text-danger">There's an error upon submitting, please review your form.</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form> 
</div>