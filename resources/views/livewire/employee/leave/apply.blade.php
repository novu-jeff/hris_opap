<form wire:submit.prevent="save" wire:target="save">
    <div class="row">
        <div class="col-12">
            <div class="card shadow p-4">
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                </div>
                <hr class="mx-3">
                <div class="card-body">
                    <div class="row">
                        @if(!is_null($this->type)) 
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-end">
                                    <h6 class="text-uppercase fw-bold">Remaining Leave Credits: {{$this->remaining_credits}}</h6>
                                </div>
                            </div>
                        @endif
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="type">Type <span class="text-danger">*</span></label>
                            <select wire:model.live="type" wire:change="handleLeaveCredits" id="type" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                @foreach($leaveTypes as $leave)
                                    <option value="{{$leave->id}}">{{$leave->code . ' - ' . $leave->name}}</option>
                                @endforeach
                            </select>
                            <div class="error-field">
                                @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="duration">Duration <span class="text-danger">*</span></label>
                            <select wire:change="selectDuration" wire:model="duration" id="duration" class="form-select" {{$isDurationDisabled ? 'disabled' : ''}}>
                                <option value=""> - CHOOSE - </option>
                                <option value="1"> One Day </option>
                                <option value="2"> Two or More Days </option>
                            </select>
                            <div class="error-field">
                                @error('duration') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @if(!is_null($isMoreThanOne))
                            <div class="col-12 {{$isMoreThanOne ? 'col-md-6' : 'col-md-12'}} mb-4">
                                <label class="mb-2" for="from">{{!$isMoreThanOne ? 'Leave Date' : 'From'}} <span class="text-danger">*</span></label>
                                <input type="date" wire:model="from" id="from" class="form-control">
                                <div class="error-field">
                                    @error('from') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @if($isMoreThanOne)
                                <div class="col-12 col-md-6 mb-4">
                                    <label class="mb-2" for="to">To <span class="text-danger">*</span></label>
                                    <input type="date" wire:model="to" id="to" class="form-control">
                                    <div class="error-field">
                                        @error('to') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endif
                        @endif
                        @if($type == 1)
                            <div class="col-12 col-md-6 mb-4">
                                <label class="mb-2" for="location">Location <span class="text-danger">*</span></label>
                                <select wire:model.live="location" id="location" class="form-select">
                                    <option value=""> - CHOOSE -</option>
                                    <option value="ph">Within Philippines</option>
                                    <option value="abroad">Abroad</option>
                                </select>
                                <div class="error-field">
                                    @error('location') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <label class="mb-2" for="location_specific">Specific Location <span class="text-danger">*</span></label>
                                <input type="text" wire:model="location_specific" id="location_specific" class="form-control">
                                <div class="error-field">
                                    @error('location_specific') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        
                        @elseif($type == 2)
                            <div class="col-12 col-md-6 mb-4">
                                <label class="mb-2" for="confinement">Patient Type <span class="text-danger">*</span></label>
                                <select wire:model="confinement" id="confinement" class="form-select">
                                    <option value=""> - CHOOSE -</option>
                                    <option value="hospital">In Hospital</option>
                                    <option value="out-patient">Out Patient</option>
                                </select>
                                <div class="error-field">
                                    @error('confinement') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <label class="mb-2" for="illness">Illness (Specify) <span class="text-danger">*</span></label>
                                <input type="text" wire:model="illness" id="illness" class="form-control">
                                <div class="error-field">
                                    @error('illness') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @elseif($type == 8)
                            <div class="col-12 {{ $study == '' || $study != 'others' ? 'col-md-12' : 'col-md-4' }} mb-4">
                                <label class="mb-2" for="study">Purpose <span class="text-danger">*</span></label>
                                <select wire:model.live="study" id="study" class="form-select">
                                    <option value=""> - CHOOSE -</option>
                                    <option value="completion_masters">Completion of Master's Degree</option>
                                    <option value="examination">Bar/Board Examination Review</option>
                                    <option value="others">Other Purpose</option>
                                </select>
                                <div class="error-field">
                                    @error('study') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @if($study == 'others')
                                <div class="col-12 col-md-8 mb-4">
                                    <label class="mb-2" for="study_other_purpose">Other Purpose (Specify) <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="study_other_purpose" id="study_other_purpose" class="form-control">
                                    <div class="error-field">
                                        @error('study_other_purpose') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endif
                        @endif
                        <div class="col-12 col-md-12 mb-4">
                            <label class="mb-2" for="commutation">Commutation <span class="text-danger">*</span></label>
                            <select wire:model="commutation" id="commutation" class="form-select">
                                <option value=""> - CHOOSE -</option>
                                <option value="no">Not Requested</option>
                                <option value="yes">Requested</option>
                            </select>
                            <div class="error-field">
                                @error('commutation') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mx-3">
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="save">Proceed <i class="fa-solid fa-arrow-right ms-2"></i></span>
                        <span wire:loading wire:target="save">Proceeding <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
