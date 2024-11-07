<form wire:submit.prevent='register'>
    <div class="card shadow p-3">
        <div class="card-header bg-transparent py-2 border-0">
            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link {{$activeTab == 'personal' ? 'active' : ''}}" id="pills-personal-tab" data-bs-toggle="pill" data-bs-target="#pills-personal" type="button" role="tab" aria-controls="pills-personal" aria-selected="true">Personal Information</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link {{$activeTab == 'account' ? 'active' : ''}}" id="pills-account-tab" data-bs-toggle="pill" data-bs-target="#pills-account" type="button" role="tab" aria-controls="pills-account" aria-selected="false">Account Information</button>
                </li>
            </ul>                  
            <div class="note my-3">
                By creating an account or signing in, you agree to Symphony's Terms. You also acknowledge our Cookie and Privacy policies. Symphony will send you marketing messages, and you can opt out at any time by following the unsubscribe link in those messages or as described in our terms.
            </div>                
        </div>
        <hr class="my-2">
        <div class="card-body">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade {{$activeTab == 'personal' ? 'show active' : ''}}" id="pills-personal" role="tabpanel" aria-labelledby="pills-personal" tabindex="0">
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label for="firstname" class="mb-2">First Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.personal.firstname" id="firstname" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.personal.firstname') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-4 mb-3">
                            <label for="middlename" class="mb-2">Middle Name</label>
                            <input type="text" wire:model="fields.personal.middlename" id="middlename" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.personal.middlename') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-4 mb-3">
                            <label for="lastname" class="mb-2">Last Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.personal.lastname" id="lastname" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.personal.lastname') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6 mb-3">
                            <label for="phone_no" class="mb-2">Mobile No. <span class="text-danger">*</span></label>
                            <input type="number" wire:model="fields.personal.phone_no" id="phone_no" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.personal.phone_no') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6 mb-3">
                            <label for="tel_no" class="mb-2">Telephone No.</label>
                            <input type="text" wire:model="fields.personal.tel_no" id="tel_no" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.personal.tel_no') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-4 mb-3">
                            <label for="sex" class="mb-2">Sex <span class="text-danger">*</span></label>
                            <select wire:model="fields.personal.sex" id="sex" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="not to say">Prefer not to say</option>
                            </select>
                            <div class="error-field">
                                @error('fields.personal.sex') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-4 mb-3">
                            <label for="birthday" class="mb-2">Birth Day <span class="text-danger">*</span></label>
                            <input type="date" wire:model="fields.personal.birthday" id="birthday" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.personal.birthday') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-4 mb-3">
                            <label for="civil_status" class="mb-2">Civil Status <span class="text-danger">*</span></label>
                            <select wire:model="fields.personal.civil_status" id="civil_status" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="seperated">Legally Separated</option>
                            </select>
                            <div class="error-field">
                                @error('fields.personal.civil_status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="address" class="mb-2">Address <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.personal.address" id="address" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.personal.address') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6 mb-3">
                            <label for="province" class="mb-2">Province <span class="text-danger">*</span></label>
                            <select wire:model="fields.personal.province" id="province" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <option value="metro manila">Metro Manila</option>
                            </select>
                            <div class="error-field">
                                @error('fields.personal.province') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6 mb-3">
                            <label for="city" class="mb-2">City <span class="text-danger">*</span></label>
                            <select wire:model="fields.personal.city" id="city" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <option value="taguig city">Taguig City</option>
                            </select>
                            <div class="error-field">
                                @error('fields.personal.city') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="tab-pane fade {{$activeTab == 'account' ? 'show active' : ''}}" id="pills-account" role="tabpanel" aria-labelledby="pills-account" tabindex="0">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="droparea" class="mb-2">Attach Resume <span class="text-danger">*</span></label>
                            <div class="droparea" id="chooseFile">
                                <div>
                                    <div class="text-center">
                                        <div>
                                            <input type="file" wire:model="fields.account.resume" id="resume">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="droparea-preview"></div>
                            <div class="error-field">
                                @error('fields.account.resume') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="email" class="mb-2">Email Address <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.account.email" id="email" class="form-control">
                            <div class="error-field">
                                @error('fields.account.email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="password" class="mb-2">Password <span class="text-danger">*</span></label>
                            <input type="password" wire:model="fields.account.password" id="password" class="form-control">
                            <div class="error-field">
                                @error('fields.account.password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="password" class="mb-2">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" wire:model="fields.account.confirm_password" id="confirm_password" class="form-control">
                            <div class="error-field">
                                @error('fields.account.confirm_password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>                  
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 d-flex gap-2 justify-content-end">
            <button class="btn btn-primary px-4 py-2">Proceed</button>
        </div>
    </div>
</form>

