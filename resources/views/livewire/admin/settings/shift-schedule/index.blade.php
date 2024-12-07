<div class="mt-4">
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-12">
                <div class="card shadow p-4">
                    <div class="card-header bg-transparent border-0">
                        <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                    </div>
                    <hr class="mx-3">
                    <div class="card-body">
                        <div class="mb-4 w-100">
                            <div style="font-size: 12px;" class="w-100 alert alert-info text-uppercase fw-bold">
                                Note: The {{$organization->name ?? 'organization'}} follows an 8-hour flexible work schedule.
                            </div>
                        </div>
                        <div>
                            <div class="header mb-4">
                                <h5 class="text-uppercase fw-bold">For Mobile Timekeeping</h5>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-4">
                                    <label class="mb-2" for="fields.mobile_earliest_clockin">Earliest Clock In <span class="text-danger">*</span></label>
                                    <select wire:change="updateField('mobile')" wire:model="fields.mobile_earliest_clockin" id="fields.mobile_earliest_clockin" class="form-select">
                                        <option value=""> - CHOOSE - </option>
                                        <option value="7">7:00 AM</option>
                                        <option value="8">8:00 AM</option>
                                        <option value="9">9:00 AM</option>
                                    </select>
                                    <div class="error-field">
                                        @error('fields.mobile_earliest_clockin') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>                            
                                <div class="col-12 col-md-6 mb-4">
                                    <label class="mb-2" for="fields.mobile_latest_clockin">Latest Clock In <span class="text-danger">*</span></label>
                                    <select wire:change="updateField('mobile_out')" wire:model="fields.mobile_latest_clockin" id="fields.mobile_latest_clockin" class="form-select">
                                        <option value=""> - CHOOSE - </option>
                                        @foreach($clockinMobTimes as $key => $time)
                                            <option value="{{$key}}">{{$time}}</option>
                                        @endforeach
                                    </select>
                                    <div class="error-field">
                                        @error('fields.mobile_latest_clockin') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 mb-4">
                                    <label class="mb-2" for="fields.mobile_expected_clockout">Expected Clock Out</label>
                                    <input type="text" wire:model="fields.mobile_expected_clockout" id="fields.mobile_expected_clockout" class="form-control restricted" readonly>
                                    <div class="error-field">
                                        @error('fields.mobile_expected_clockout') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <hr class="mx-3 mb-5">
                            <div class="header mb-4">
                                <h5 class="text-uppercase fw-bold">For Website Timekeeping</h5>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-4">
                                    <label class="mb-2" for="fields.web_earliest_clockin">Earliest Clock In <span class="text-danger">*</span></label>
                                    <select wire:change="updateField('web')" wire:model="fields.web_earliest_clockin" id="fields.mobile_earliest_clockin" class="form-select">
                                        <option value=""> - CHOOSE - </option>
                                        <option value="7">7:00 AM</option>
                                        <option value="8">8:00 AM</option>
                                        <option value="9">9:00 AM</option>
                                    </select>
                                    <div class="error-field">
                                        @error('fields.web_earliest_clockin') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-4">
                                    <label class="mb-2" for="fields.web_latest_clockin">Latest Clock In <span class="text-danger">*</span></label>
                                    <select wire:change="updateField('web_out')" wire:model="fields.web_latest_clockin" id="fields.web_latest_clockin" class="form-select">
                                        <option value=""> - CHOOSE - </option>
                                        @foreach($clockinWebTimes as $key => $time)
                                            <option value="{{$key}}">{{$time}}</option>
                                        @endforeach
                                    </select>
                                    <div class="error-field">
                                        @error('fields.web_latest_clockin') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 mb-4">
                                    <label class="mb-2" for="fields.web_expected_clockout">Expected Clock Out</label>
                                    <input type="text" wire:model="fields.web_expected_clockout" id="fields.web_expected_clockout" class="form-control restricted" readonly>
                                    <div class="error-field">
                                        @error('fields.web_expected_clockout') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>   
                            <hr class="mx-3 mb-5">
                            <div class="header mb-4">
                                <h5 class="text-uppercase fw-bold">Break Time Hours</h5>
                            </div>       
                            <div class="row">
                                <div class="col-12 col-md-6 mb-4">
                                    <label for="fields.break_from" class="mb-2">From <span class="text-danger">*</span></label>
                                    <select wire:model="fields.break_from" wire:change="updateField('break_from')" class="form-select">
                                        <option value="">- CHOOSE -</option>
                                        <option value="11">11 AM</option>
                                        <option value="12">12 PM</option>
                                    </select>
                                    <div class="error-field">
                                        @error('fields.break_from') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-4">
                                    <label for="fields.break_to" class="mb-2">To <span class="text-danger">*</span></label>
                                    <select wire:model="fields.break_to" class="form-select">
                                        <option value="">- CHOOSE -</option>
                                        @foreach($breakToOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="error-field">
                                        @error('fields.break_to') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>                                                       
                            <hr class="mx-3 mb-5">
                            <div class="header mb-4">
                                <h5 class="text-uppercase fw-bold">Others</h5>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4 mb-4">
                                    <label class="mb-2" for="fields.min_ot_mins" id="fields.min_ot_mins">Minimum Overtime Hours (in minutes) <span class="text-danger">*</span></label>
                                    <input type="number" wire:model="fields.min_ot_mins" id="fields.min_ot_mins" class="form-control text-uppercase">
                                    <div class="error-field">
                                        @error('fields.min_ot_mins') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-4">
                                    <label class="mb-2" for="fields.max_ot_time" id="fields.max_ot_time">Overtime Time Until <span class="text-danger">*</span></label>
                                    <select wire:model="fields.max_ot_time" id="fields.max_ot_time" class="form-select">
                                        <option value=""> - CHOOSE - </option>
                                        <option value="8">8 PM</option>
                                        <option value="9">9 PM</option>
                                        <option value="10">10 PM</option>
                                        <option value="11">11 PM</option>
                                        <option value="12">12 AM</option>
                                    </select>
                                    <div class="error-field">
                                        @error('fields.max_ot_time') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-4">
                                    <label class="mb-2" for="fields.is_late_strict" id="fields.is_late_strict">Strict for Late? <span class="text-danger">*</span></label>
                                    <select wire:model="fields.is_late_strict" id="fields.is_late_strict" class="form-select">
                                        <option value=""> - CHOOSE - </option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                    <div class="error-field">
                                        @error('fields.is_late_strict') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-4">
                                    <label class="mb-2" for="fields.is_strict_undertime" id="fields.is_strict_undertime">Strict for Undertime? <span class="text-danger">*</span></label>
                                    <select wire:model="fields.is_strict_undertime" id="fields.is_strict_undertime" class="form-select">
                                        <option value=""> - CHOOSE - </option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                    <div class="error-field">
                                        @error('fields.is_strict_undertime') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="mx-3">
                    <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
</div>