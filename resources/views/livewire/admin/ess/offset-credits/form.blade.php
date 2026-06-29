<form wire:submit.prevent="save">

    <div class="card shadow border-0">
    
        <div class="card-header">
            <h5 class="mb-0">
                {{ $record_id ? 'Edit Offset Credit' : 'Add Offset Credit' }}
            </h5>
        </div>
    
        <div class="card-body">
    
            <div class="row">
    
                <div class="col-md-6 mb-3">
    
                        <label class="form-label">
                            Employee
                            <span class="text-danger">*</span>
                        </label>
                    
                        @if($record_id)

                        <input
                            type="text"
                            class="form-control"
                            value="({{ $record->employee_no }}) {{ $record->employee->personal->firstname }} {{ $record->employee->personal->lastname }}"
                            readonly>

                    @else

                        <div wire:ignore>
                            <select
                                class="form-select multi-select"
                                data-placeholder="Select Employee">
                            </select>
                        </div>

                        @error('fields.employee_no')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror

                    @endif

                    
                    
    
                </div>
    
                <div class="col-md-3 mb-3">
    
                    <label>Earned Hours</label>
    
                    <input
                        type="number"
                        step="0.5"
                        class="form-control"
                        wire:model="fields.earned_hours">
    
                    @error('fields.earned_hours')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
    
                </div>
    
                <div class="col-md-3 mb-3">
    
                    <label>Earned Date</label>
    
                    <input
                        type="date"
                        class="form-control"
                        wire:model="fields.earned_date">
    
                </div>
    
            </div>
    
            <div class="row">
    
                <div class="col-md-4 mb-3">
    
                    <label>Source</label>
    
                    <select
                        class="form-select"
                        wire:model="fields.source">
    
                        <option value="Manual">Manual</option>
    
                        <option value="Adjustment">Adjustment</option>
    
                        <option value="Overtime">Overtime</option>
    
                    </select>
    
                </div>
    
                <div class="col-md-8 mb-3">
    
                    <label>Reference No.</label>
    
                    <input
                        type="text"
                        class="form-control"
                        wire:model="fields.reference_no" readonly>
    
                </div>
    
            </div>
    
            <div class="mb-3">
    
                <label>Remarks</label>
    
                <textarea
                    rows="4"
                    class="form-control"
                    wire:model="fields.remarks"></textarea>
    
            </div>
    
        </div>
    
        <div class="card-footer text-end">
    
            <a
                href="{{ route('ess.offset-credits') }}"
                class="btn btn-secondary">
    
                Cancel
    
            </a>
    
            <button
                type="submit"
                class="btn btn-primary">
    
                {{ $record_id ? 'Update Credit' : 'Save Credit' }}
    
            </button>
    
        </div>
    
    </div>
    
    </form>
 
    @section('script')
    <script>
        document.addEventListener('livewire:initialized', () => {
            console.log('initialized');
    Livewire.on('init-select', (data) => {

        setTimeout(() => {
console.log(data.employees);
            let el = $('.multi-select');

            if (el.length === 0) return;

            el.empty();

            data.employees.forEach(emp => {
                let option = new Option(
                    `(${emp.employee_no}) ${emp.personal.firstname} ${emp.personal.lastname}`,
                    emp.employee_no,
                    false,
                    false
                );
                el.append(option);
            });

            if (el.hasClass("select2-hidden-accessible")) {
                el.select2('destroy');
            }
            
            
                el.select2({
                    width: '100%',
                    placeholder: 'Select employee(s)', // 🔥 ADD
                });

                el.off('change').on('change', function () {

                let employeeNo = $(this).val();

                @this.set('fields.employee_no', employeeNo);

                });

               
               // toggleSaveButton();
            // 🔥 ADD THIS (CRITICAL FIX)
            let selected = data.selected || [];

            if (selected.length) {
                el.val(selected).trigger('change');
                window.selectedEmployees = selected; // keep JS in sync
            }

        }, 300);
    });

});


    </script>
@endsection