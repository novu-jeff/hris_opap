<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-12">
            <div class="card shadow p-4">
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                </div>
                <hr class="mx-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2" for="fields.code">Code <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.code" id="fields.code" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.code') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="fields.name">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.name" id="fields.name" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2" for="fields.amount_basis">Amount Basis <span class="text-danger">*</span></label>
                            <select wire:model="fields.amount_basis" wire:change="onChangeSelect('amount_basis', event.target.value)" id="amount_basis" class="form-select">
                                <option value=""> - Choose - </option>
                                <option value="entry">Data Entry</option>
                                <option value="basic_salary">Basic Salary</option>
                                <option value="percentage">Percentage</option>
                            </select>
                            <div class="error-field">
                                @error('fields.amount_basis') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @if($hasAmount)
                            <div class="col-12 col-md-3 mb-4">
                                <label class="mb-2" for="fields.amount">Amount <span class="text-danger">*</span></label>
                                <input type="number" wire:model="fields.amount" id="fields.amount" class="form-control text-uppercase">
                                <div class="error-field">
                                    @error('fields.amount') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @else

                        @endif
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2" for="fields.frequency_basis">Frequency Basis <span class="text-danger">*</span></label>
                            <select wire:model="fields.frequency_basis" wire:change="onChangeSelect('frequency_basis', event.target.value)" id="frequency_basis" class="form-select">
                                <option value=""> - Choose - </option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                                <option value="month_picked">Monthly Picked</option>
                            </select>
                            <div class="error-field">
                                @error('fields.frequency_basis') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @if($hasMonthPicked)
                            <div class="col-12 col-md-3 mb-4">
                                <label class="mb-2" for="fields.frequency">Monthly Picked Frequency<span class="text-danger">*</span></label>
                                <select 
                                    wire:model="fields.frequency" 
                                    id="frequency" 
                                    class="form-control text-uppercase select-2" 
                                    multiple="multiple">
                                    <option value="January">January</option>
                                    <option value="February">February</option>
                                    <option value="March">March</option>
                                    <option value="April">April</option>
                                    <option value="May">May</option>
                                    <option value="June">June</option>
                                    <option value="July">July</option>
                                    <option value="August">August</option>
                                    <option value="September">September</option>
                                    <option value="October">October</option>
                                    <option value="November">November</option>
                                    <option value="December">December</option>
                                </select>
                                <div class="error-field">
                                    @error('fields.frequency') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2" for="fields.eligible">Eligible <span class="text-danger">*</span></label>
                            <select wire:model="fields.eligible" wire:change="onChangeSelect('eligible', event.target.value)" id="eligible" class="form-select select-2" multiple>
                                @foreach ($job_category as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                            <div class="error-field">
                                @error('fields.eligible') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2" for="fields.is_taxable">Is Taxable? <span class="text-danger">*</span></label>
                            <select wire:model="fields.is_taxable" id="is_taxable" class="form-select">
                                <option value=""> - Choose - </option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <div class="error-field">
                                @error('fields.is_taxable') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2" for="fields.is_forecasted">Based on forcasted? <span class="text-danger">*</span></label>
                            <select wire:model="fields.is_forecasted" id="is_forecasted" class="form-select">
                                <option value=""> - Choose - </option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <div class="error-field">
                                @error('fields.is_forecasted') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-4">
                            <label class="mb-2" for="fields.has_parameter">Has Parameter? <span class="text-danger">*</span></label>
                            <select wire:model="fields.has_parameter" wire:change="onChangeSelect('has_parameter', event.target.value)" id="has_parameter" class="form-select">
                                <option value=""> - Choose - </option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <div class="error-field">
                                @error('fields.has_parameter') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @if($hasParameter)
                            <hr>
                            <div class="col-12 col-md-3 mb-4">
                                <label class="mb-2" for="fields.duration">Service Duration Unit  <span class="text-danger">*</span></label>
                                <select wire:model="fields.duration" wire:change="onChangeSelect('duration', event.target.value)" id="duration" class="form-select">
                                    <option value=""> - Choose - </option>
                                    <option value="days">Days</option>
                                    <option value="months">Months</option>
                                    <option value="years">Years</option>
                                </select>
                                <div class="error-field">
                                    @error('fields.duration') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-3 mb-4">
                                <label class="mb-2" for="fields.count">Service Duration Amount</label>
                                <input type="number" wire:model="fields.count" id="count" class="form-control">
                                <div class="error-field">
                                    @error('fields.count') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-3 mb-4">
                                <label class="mb-2" for="fields.context">Service Context <span class="text-danger">*</span></label>
                                <select wire:model="fields.context" wire:change="onChangeSelect('context', event.target.value)" id="context" class="form-select">
                                    <option value=""> - Choose - </option>
                                    <option value="from">From</option>
                                    <option value="prior_to">Prior To</option>
                                    <option value="subsequent_to">Subsequent To</option>
                                </select>
                                <div class="error-field">
                                    @error('fields.context') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-3 mb-4">
                                <label class="mb-2" for="fields.date">Date</label>
                                <input type="date" wire:model="fields.date" id="date" class="form-control">
                                <div class="error-field">
                                    @error('fields.date') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                        @endif
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
        </div>
    </div>
</form>
