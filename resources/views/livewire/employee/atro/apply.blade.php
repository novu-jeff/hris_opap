<form wire:submit.prevent="save" wire:target="save">
    <div class="row">
        <div class="col-12">
            <div class="card shadow p-4">
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                </div>
                <hr class="mx-3">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12 col-md-4 mb-3">
                            <label for="date" class="form-label"> Date <span class="text-danger fw-bold">*</span></label>
                            <input 
                                type="date" 
                                id="date" 
                                class="form-control @error('fields.date') is-invalid @enderror" 
                                wire:model="fields.date"
                            >
                            <div class="error-field">
                                @error('fields.date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label for="start-time" class="form-label">Start Time <span class="text-danger fw-bold">*</span></label>
                            <input 
                                type="text" 
                                id="start-time" 
                                class="timepicker form-control @error('fields.start_time') is-invalid @enderror" 
                                wire:model="fields.start_time"
                            >
                            <div class="error-field">
                                @error('fields.start_time') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label for="end-time" class="form-label">End Time <span class="text-danger fw-bold">*</span></label>
                            <input 
                                type="text" 
                                id="end-time" 
                                class="timepicker form-control @error('fields.end_time') is-invalid @enderror" 
                                wire:model="fields.end_time"
                            >
                            <div class="error-field">
                                @error('fields.end_time') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mb-3" wire:ignore>
                            <label for="relative-emp" class="form-label">Employees <span class="text-danger fw-bold">*</span></label>
                            <select class="form-select multi-select" multiple wire:model="fields.employees">
                                @foreach($OtherEmployees as $employee)
                                    <option value="{{ $employee->employee_no }}">
                                        ({{ $employee->employee_no }}) {{ $employee->personal->firstname }} {{ $employee->personal->lastname }}
                                    </option>
                                @endforeach
                            </select>                                
                            <div class="error-field select2-error">
                                
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mb-3">
                            <label for="justification" class="form-label">Justification <span class="text-danger fw-bold">*</span></label>
                            <textarea 
                                rows="5" 
                                placeholder="Write something..."
                                id="justification" 
                                class="form-control @error('fields.justification') is-invalid @enderror" 
                                wire:model="fields.justification"
                            ></textarea>
                            <div class="error-field">
                                @error('fields.justification') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>  
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

            $('.multi-select').on('change', function (e) {
                const data = $('.multi-select').select2('val');
                @this.dispatch('onChange', [data ?? null]);
            });


            Livewire.on('reloadSelect2', () => {
                $('.multi-select').select2();
            });

            console.log($('.multi-select').select2('val'));

            Livewire.on('select2Err', (error) => {
                $('.select2-error').html('<span class="text-danger">'+error+'</span>');
            });

        });
    </script>
@endsection