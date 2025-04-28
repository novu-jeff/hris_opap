<form wire:submit.prevent="save" wire:target="save">
    <div class="row">
        <div class="col-12">
            <div class="card shadow p-4">
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                </div>
                <hr class="mx-3">
                <div class="card-body">
                    @foreach($fields as $index => $field)
                        <div class="row mb-3">
                            <div class="col-12 col-md-4 mb-3">
                                <label for="date-{{ $index }}" class="form-label"> Date <span class="text-danger fw-bold">*</span></label>
                                <input 
                                    type="date" 
                                    id="date-{{ $index }}" 
                                    class="form-control @error('fields.' . $index . '.date') is-invalid @enderror" 
                                    wire:model="fields.{{ $index }}.date"
                                >
                                <div class="error-field">
                                    @error('fields.' . $index . '.date') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="start-time-{{ $index }}" class="form-label">Start Time <span class="text-danger fw-bold">*</span></label>
                                <input 
                                    type="time" 
                                    id="start-time-{{ $index }}" 
                                    class="form-control @error('fields.' . $index . '.start_time') is-invalid @enderror" 
                                    wire:model="fields.{{ $index }}.start_time"
                                >
                                <div class="error-field">
                                    @error('fields.' . $index . '.start_time') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="end-time-{{ $index }}" class="form-label">End Time <span class="text-danger fw-bold">*</span></label>
                                <input 
                                    type="time" 
                                    id="end-time-{{ $index }}" 
                                    class="form-control @error('fields.' . $index . '.end_time') is-invalid @enderror" 
                                    wire:model="fields.{{ $index }}.end_time"
                                >
                                <div class="error-field">
                                    @error('fields.' . $index . '.end_time') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-12 mb-3">
                                <label for="co-employee-{{ $index }}" class="form-label">Employees <span class="text-danger fw-bold">*</span></label>
                                <select class="form-select multi-select" name="states[]" multiple="multiple">
                                    <option value=""> - CHOOSE - </option>
                                    @foreach($OtherEmployees as $employee)
                                        <option value="{{ $employee->employee_no }}">{{ '(' . $employee->employee_no . ') ' . $employee->personal->firstname . ' ' . $employee->personal->lastname }}</option>
                                    @endforeach
                                </select>
                                <div class="error-field">
                                    @error('fields.' . $index . '.justification') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-12 mb-3">
                                <label for="justification-{{ $index }}" class="form-label">Justification <span class="text-danger fw-bold">*</span></label>
                                <textarea 
                                    rows="5" 
                                    placeholder="Write something..."
                                    id="justification-{{ $index }}" 
                                    class="form-control @error('fields.' . $index . '.justification') is-invalid @enderror" 
                                    wire:model="fields.{{ $index }}.justification"
                                ></textarea>
                                <div class="error-field">
                                    @error('fields.' . $index . '.justification') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-12 text-end mt-3">
                                @if($index > 0)
                                    <button type="button" class="btn btn-danger float-end px-3 py-2 text-uppercase" wire:click="removeField({{ $index }})">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </div>                    
                        <hr>
                    @endforeach
                    <button type="button" class="btn btn-dark float-end px-3 py-2 text-uppercase" wire:click="addField">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
                <hr class="mx-3">
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end gap-3">
                    <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="save">Proceed <i class="fa-solid fa-arrow-right ms-2"></i></span>
                        <span wire:loading wire:target="save">Proceeding <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@section('script')
    <script>
        $(function() {
            $('.multi-select').select2();
        });
    </script>
@endsection