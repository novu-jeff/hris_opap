<div>
   
        <div class="d-flex justify-content-end gap-3 actions w-100 mb-5">
            <!-- <a href="{{route('other-earnings.index')}}" class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a> -->
            <button wire:click="openModal('create')" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Add New</button>
        </div>
        <div class="card border-0 mt-3">
            <div class="card-body p-0">
                <div class="row mb-4">
                    <div class="col-md-6 d-flex align-items-center gap-2">
                        <label for="entries" class="form-label mb-0">Show entries:</label>
                        <select id="entries" wire:model.live="entries" class="form-select w-auto">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="200">200</option>
                            <option value="300">300</option>
                            <option value="300">400</option>
                            <option value="300">500</option>
                            <option value="1000">1000</option>
                        </select>
                    </div>
                    <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
                        <label for="search" class="form-label mb-0">Search:</label>
                        <input id="search" wire:model.live="search" type="text" class="form-control w-50" placeholder="Search something...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>Employee No</th>
                                <th>Employee Name</th>
                                <th>Amount</th>
                                <th>Last Update</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $record)
                                <tr>
                                    <td>{{ $record->employee_no }}</td>
                                    <td>{{ $record->personal->firstname . ' ' . $record->personal->lastname }}</td>
                                    <td>
                                        PHP {{ number_format($record->amount, 2) }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($record->updated_at)->format('F d, Y \•\ H:i A') }}
                                    </td>   
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <button wire:click="openModal('edit', '{{$record->employee_no}}')" class="btn btn-info mx-1">
                                                <i class="fa-solid fa-edit"></i>
                                            </button>
                                            <button wire:click="remove('true', '{{$record->employee_no}}')" class="btn btn-danger mx-1">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">No records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $records->links(data: ['scrollTo' => false]) }}
                </div>
            </div>
        </div>
        @if($showModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5)">
            <div class="modal-dialog">
                <div class="modal-content">
        
                    <div class="modal-header">
                        <h5>{{ $mode == 'edit' ? 'Edit' : 'Add' }} Earning</h5>
                        <button wire:click="closeModal" class="btn-close"></button>
                    </div>
        
                    <div class="modal-body">
                        <div class="col-12 col-md-12 mb-3 w-100">
                            <label for="employee_no" class="form-label">Choose Employees</label>
                            <div wire:ignore.self>
                                <select class="form-select multi-select w-100" multiple data-placeholder="Select employee(s)"></select>
                                <input type="hidden" id="selectedEmployees">
                            </div>
                            @error('fields.employee_no') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 col-md-12 mb-3">
                            <label for="amount_type" class="form-label">Amount Type</label>
                            <input type="text" id="amount_type" wire:model="fields.amount_type" class="form-control restricted" readonly>
                            @error('fields.amount_type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 col-md-12 mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="text" id="amount" wire:model="fields.amount" class="form-control">
                            @error('fields.amount') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
        
                    <div class="modal-footer">
                        <button wire:click="closeModal" class="btn btn-secondary">Cancel</button>
                        <button 
                            wire:click="save"
                            onclick="return beforeSave(@this)"
                            id="saveBtn"
                            class="btn btn-primary"
                            disabled>
                            Save
                        </button>
                    </div>
        
                </div>
            </div>
        </div>
        @endif
</div>


@section('script')
<script>
    document.addEventListener('livewire:initialized', () => {
    
        Livewire.on('init-select', (data) => {
    
            setTimeout(() => {
    
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
                        dropdownParent: el.closest('.modal-content'),
                        width: '100%',
                        placeholder: 'Select employee(s)', // 🔥 ADD
                        allowClear: true // 🔥 ADD
                    });
    
                    // ✅ DO NOT reset if already has value
                    window.selectedEmployees = window.selectedEmployees || [];
    
                    el.on('select2:select select2:unselect', function () {
                        window.selectedEmployees = $(this).val();
                        toggleSaveButton(); // 🔥 ADD THIS
                    });
                

                // 🔥 ADD THIS (CRITICAL FIX)
                let selected = data.selected || [];

                if (selected.length) {
                    el.val(selected).trigger('change');
                    window.selectedEmployees = selected; // keep JS in sync
                }
    
            }, 300);
        });
    
    });
    document.addEventListener('input', function (e) {
        if (e.target.id === 'amount') {
            toggleSaveButton();
        }
    });
    function toggleSaveButton() {
        let employees = window.selectedEmployees || [];
        let amount = document.getElementById('amount')?.value || '';

        let btn = document.getElementById('saveBtn');

        if (employees.length > 0 && amount.trim() !== '') {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    }

function beforeSave(component) {
    component.set('fields.employee_no', window.selectedEmployees || []);
    return true;
}
    function validateBeforeSave(component) {
        let employees = window.selectedEmployees || [];
        let amount = document.getElementById('amount').value;

        if (!employees.length) {
            alert('Please select at least one employee.');
            return false;
        }

        if (!amount) {
            alert('Amount is required.');
            return false;
        }

        component.set('fields.employee_no', employees);

        return true;
    }
    </script>
@endsection