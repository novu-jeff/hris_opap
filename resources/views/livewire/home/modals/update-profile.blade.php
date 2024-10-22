<div>
    <div class="modal fade" wire:ignore.self id="update-profile-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-profile-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content p-3">
                <div class="modal-header d-block">
                    <div class="d-flex">
                        <h1 class="modal-title fs-5 text-uppercase fw-bold" id="update-profile-modalLabel">Update Profile</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <ul class="nav nav-pills mt-4 mb-1" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-uppercase fw-bold px-4 {{$activeTab == 'information' ? 'active' : ''}}" id="pills-personal-tab" data-bs-toggle="pill" data-bs-target="#pills-personal" type="button" role="tab" aria-controls="pills-personal" aria-selected="true">Basic Information</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-uppercase fw-bold px-4 {{$activeTab == 'education' ? 'active' : ''}}" id="pills-education-tab" data-bs-toggle="pill" data-bs-target="#pills-education" type="button" role="tab" aria-controls="pills-education" aria-selected="false">Educational Information</button>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade {{$activeTab == 'information' ? 'show active' : ''}}" id="pills-personal" role="tabpanel" aria-labelledby="pills-personal-tab" tabindex="0">
                            <div class="row">
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="firstname" class="mb-2">First Name <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="fields.information.firstname" id="firstname" class="form-control text-uppercase">
                                    <div class="error-field">
                                        @error('fields.information.firstname') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="middlename" class="mb-2">Middle Name</label>
                                    <input type="text" wire:model="fields.information.middlename" id="middlename" class="form-control text-uppercase">
                                    <div class="error-field">
                                        @error('fields.information.middlename') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="lastname" class="mb-2">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="fields.information.lastname" id="lastname" class="form-control text-uppercase">
                                    <div class="error-field">
                                        @error('fields.information.lastname') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="phone_no" class="mb-2">Mobile No. <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="fields.information.phone_no" id="phone_no" class="form-control text-uppercase">
                                    <div class="error-field">
                                        @error('fields.information.phone_no') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="tel_no" class="mb-2">Telephone No.</label>
                                    <input type="text" wire:model="fields.information.tel_no" id="tel_no" class="form-control text-uppercase">
                                    <div class="error-field">
                                        @error('fields.information.tel_no') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="sex" class="mb-2">Sex <span class="text-danger">*</span></label>
                                    <select wire:model="fields.information.sex" id="sex" class="form-select">
                                        <option value=""> - CHOOSE - </option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="not to say">Prefer not to say</option>
                                    </select>
                                    <div class="error-field">
                                        @error('fields.information.sex') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="birthday" class="mb-2">Birth Day <span class="text-danger">*</span></label>
                                    <input type="date" wire:model="fields.information.birthday" id="birthday" class="form-control text-uppercase">
                                    <div class="error-field">
                                        @error('fields.information.birthday') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="civil_status" class="mb-2">Civil Status <span class="text-danger">*</span></label>
                                    <select wire:model="fields.information.civil_status" id="civil_status" class="form-select">
                                        <option value=""> - CHOOSE - </option>
                                        <option value="single">Single</option>
                                        <option value="married">Married</option>
                                        <option value="seperated">Legally Separated</option>
                                    </select>
                                    <div class="error-field">
                                        @error('fields.information.civil_status') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="address" class="mb-2">Address <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="fields.information.address" id="address" class="form-control text-uppercase">
                                    <div class="error-field">
                                        @error('fields.information.address') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="province" class="mb-2">Province <span class="text-danger">*</span></label>
                                    <select wire:model="fields.information.province" id="province" class="form-select">
                                        <option value=""> - CHOOSE - </option>
                                        <option value="metro manila">Metro Manila</option>
                                    </select>
                                    <div class="error-field">
                                        @error('fields.information.province') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="city" class="mb-2">City <span class="text-danger">*</span></label>
                                    <select wire:model="fields.information.city" id="city" class="form-select">
                                        <option value=""> - CHOOSE - </option>
                                        <option value="taguig city">Taguig City</option>
                                    </select>
                                    <div class="error-field">
                                        @error('fields.information.city') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>                            
                        </div>
                        <div class="tab-pane fade {{$activeTab == 'education' ? 'show active' : ''}}" id="pills-education" role="tabpanel" aria-labelledby="pills-education-tab" tabindex="0">
                            <div class="education">
                                <div class="row">
                                    <div class="col-12 col-md-12 mb-3">
                                        <label for="level" class="mb-2">Highest Attaintment</label>
                                        <select wire:model="fields.education.level" id="level" class="form-select">
                                            <option value=""> - CHOOSE - </option>
                                            <option value="highschool undergraduate">High School Undergraduate</option>
                                            <option value="vocational course">Vocational Course</option>
                                            <option value="highschool graduate">High School Graduate</option>
                                            <option value="college undergraduate">College Undergraduate</option>
                                            <option value="bachelor degree">Bachelor Degree</option>
                                            <option value="masteral degree">Masteral Degree</option>
                                            <option value="doctoral degree">Doctoral Degree</option>
                                        </select>
                                        <div class="error-field">
                                            @error('fields.education.level') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 mb-3">
                                        <label for="school_name" class="mb-2">School / University Name</label>
                                        <input type="text" wire:model="fields.education.school_name" id="school_name" class="form-control text-uppercase">
                                        <div class="error-field">
                                            @error('fields.education.school_name') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 mb-3">
                                        <label for="course" class="mb-2">Course</label>
                                        <input type="text" wire:model="fields.education.course" id="course" class="form-control text-uppercase">
                                        <div class="error-field">
                                            @error('fields.education.course') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="started" class="mb-2">Date Started</label>
                                        <input type="date" wire:model="fields.education.started" id="started" class="form-control text-uppercase">
                                        <div class="error-field">
                                            @error('fields.education.started') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="finished" class="mb-2">Date Finished</label>
                                        <input type="date" wire:model="fields.education.finished" id="finished" class="form-control text-uppercase">
                                        <div class="error-field">
                                            @error('fields.education.finished') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="modal-footer pb-0">
                <button type="submit" class="btn btn-primary" wire:click='save'>Proceed</button>
            </div>
        </div>
    </div> 
</div>