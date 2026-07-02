<form wire:submit.prevent="save">

    <div class="row">

        <div class="col-12">

            <div class="card shadow border-0">

                <div class="card-header bg-transparent">
                    <h5 class="mb-1">Authority to Render Offsetting</h5>
                    <small class="text-muted">
                        Fields marked with <span class="text-danger">*</span> are required.
                    </small>
                </div>

                <div class="card-body">

                    {{-- Employee Information --}}
                    <h6 class="border-bottom pb-2 mb-3">
                        Employee Information
                    </h6>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                Employee No.
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ auth('employee')->user()->employee_no }}"
                                readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                Employee Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ auth('employee')->user()->personal->firstname }} {{ auth('employee')->user()->personal->lastname }}"
                                readonly>
                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Date Filed
                            </label>

                            <input
                                type="date"
                                class="form-control"
                                wire:model="fields.filing_date">

                        </div>

                    </div>


                    {{-- Offset Summary --}}
                    <h6 class="border-bottom pb-2 mt-4 mb-3">
                        Offset Credit Summary
                    </h6>

                    <div class="row">

                        <div class="col-md-4">

                            <div class="card border-success text-center">

                                <div class="card-body">

                                    <small>Earned Hours</small>

                                    <h3 class="fw-bold">
                                        {{ number_format($earnedHours,2) }}
                                    </h3>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="card border-warning text-center">

                                <div class="card-body">

                                    <small>Used Hours</small>

                                    <h3 class="fw-bold">
                                        {{ number_format($usedHours,2) }}
                                    </h3>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="card border-primary text-center">

                                <div class="card-body">

                                    <small>Available Hours</small>

                                    <h3 class="fw-bold text-success">
                                        {{ number_format($remainingHours,2) }}
                                    </h3>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Offset Details --}}
                <h6 class="border-bottom pb-2 mt-5 mb-3">
                    Offset Details
                </h6>

                <div class="row">

                    {{-- Offset Date --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Offset Date
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            class="form-control @error('fields.offset_date') is-invalid @enderror"
                            wire:model="fields.offset_date">

                        @error('fields.offset_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                    {{-- Request Type --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Request Type
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            class="form-select @error('fields.request_type') is-invalid @enderror"
                            wire:model.live="fields.request_type">

                            <option value="">Select Request Type</option>
                            <option value="AM">AM Half Day (4 Hours)</option>
                            <option value="PM">PM Half Day (4 Hours)</option>
                            <option value="WHOLE_DAY">Whole Day (8 Hours)</option>

                        </select>

                        @error('fields.request_type')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                    {{-- Hours Requested --}}
                    <div class="col-md-2 mb-3">

                        <label class="form-label">
                            Hours
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            wire:model="fields.hours_requested"
                            readonly>

                            @error('fields.hours_requested')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror    

                    </div>

                    {{-- Remaining Balance --}}
                    <div class="col-md-2 mb-3">

                        <label class="form-label">
                            Balance
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ number_format($this->remainingBalance, 2) }}"
                            readonly>

                    </div>

                </div>

                    {{-- Purpose --}}
                    <div class="mb-4">

                        <label class="form-label">
                            Purpose / Remarks
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            rows="5"
                            class="form-control @error('fields.reason') is-invalid @enderror"
                            wire:model="fields.reason"></textarea>

                        @error('fields.reason')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>


                    {{-- Attachments --}}
                    <div class="col-12 mb-4">

                        <label class="form-label">
                            Supporting Attachments
                            <span class="text-danger">*</span>
                        </label>

                        <div class="mb-2">
                            <small class="text-muted fw-bold">
                                Upload any supporting documents such as Office Order,
                                Memorandum, Approved Overtime, or other relevant files.
                            </small>
                        </div>

                        <input
                            type="file"
                            wire:model="attachments"
                            class="form-control"
                            multiple>

                        <small class="text-muted fst-italic">
                            Accepted formats:
                            PDF, JPG, JPEG, PNG, DOC, DOCX
                            (Maximum 10MB per file)
                        </small>

                        @error('attachments')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror

                        @error('attachments.*')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror

                        {{-- Upload Progress --}}
                        <div wire:loading wire:target="attachments" class="mt-2">
                            <span class="text-primary">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                Uploading...
                            </span>
                        </div>

                        {{-- Existing Attachments --}}
                        @if(!empty($preview_attachments))

                            <div class="mt-3">

                                <label class="fw-bold">
                                    Uploaded Files
                                </label>

                                <ul class="list-group mt-2">

                                    @foreach($preview_attachments as $item)

                                        <li class="list-group-item d-flex justify-content-between align-items-center">

                                            <a
                                                href="{{ Storage::url($item['attachment']) }}"
                                                target="_blank">

                                                <i class="fa-solid fa-paperclip me-2"></i>

                                                {{ basename($item['attachment']) }}

                                            </a>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger"
                                                wire:click="removeAttachment({{ $item['id'] }})">

                                                <i class="fa-solid fa-trash"></i>

                                            </button>

                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        @endif

                    </div>

                    {{-- Disapproval --}}
                    @if(($fields['status'] ?? null) == 'disapproved')

                    <div class="mb-3">

                        <label>
                            Reason for Disapproval
                        </label>

                        <textarea
                            class="form-control"
                            rows="4"
                            readonly>{{ $fields['remarks'] }}</textarea>

                    </div>

                    @endif

                </div>

                @if(($fields['status'] ?? null) != 'disapproved')

                    <div class="card-footer bg-transparent text-end">

                        <a
                            href="{{ route('employee.offset.index') }}"
                            class="btn btn-light px-4">

                            Cancel

                        </a>

                        <button
                            class="btn btn-primary px-5">

                            <span wire:loading.remove wire:target="save">
                                Submit Application
                            </span>

                            <span wire:loading wire:target="save">
                                Saving...
                            </span>

                        </button>

                    </div>

                @endif

            </div>

        </div>

    </div>

</form>