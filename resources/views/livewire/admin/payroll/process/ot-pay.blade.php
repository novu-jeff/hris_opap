
<div class="container-fluid px-4">

    {{-- STATUS HEADER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <h3 class="fw-bold mb-1 text-uppercase">
                        Overtime Payroll Details
                    </h3>
                    <p class="text-muted mb-0">
                        Review, manage and approve payroll entries
                    </p>
                </div>

                <div>
                    @if($isApproved)
                        <span class="badge bg-success px-4 py-3 fs-6">
                            <i class="fa fa-check-circle me-2"></i>
                            Approved
                        </span>
                    @else
                        <span class="badge bg-warning text-dark px-4 py-3 fs-6">
                            <i class="fa fa-clock me-2"></i>
                            Pending Approval
                        </span>
                    @endif
                </div>

            </div>

        </div>
    </div>

{{-- PAYROLL SUMMARY --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">

        <h5 class="fw-bold mb-4">
            Payroll Summary
        </h5>

        <div class="row g-4">

            <div class="col-md-6">
                <div class="border rounded-3 p-3 h-100">

                    <div class="mb-3">
                        <small class="text-muted text-uppercase">Type</small>
                        <div class="fw-bold fs-6">
                            {{ str_replace('_', ' ', $records['payroll']['type']) }}
                           
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted text-uppercase">Overtime Period </small>
                        <div class="fw-bold">
                         {{$records['payroll']['formatted_ot_period']}}
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted text-uppercase">Employee Type</small>
                        <div class="fw-bold">
                            {{ $records['payroll']['formatted_employment_type'] }}
                        </div>
                    </div>

                    <div>
                        <small class="text-muted text-uppercase">No. of Employees</small>
                        <div class="fw-bold">
                            {{$records['payroll']['no_employees']}}
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-md-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="mb-3">
                        <small class="text-muted text-uppercase">
                             Amount
                        </small>
                        <div class="fw-bold text-primary">
                            {{number_format($records['payroll']['total_amount'], 2)}}
                        </div>
                    </div>

                    
                        <div class="mb-3">
                            <small class="text-muted text-uppercase">Tax</small>
                            <div class="fw-bold text-danger">
                                {{number_format($records['payroll']['total_tax'], 2)}}
                            </div>
                        </div>
                

                    <div>
                        <small class="text-muted text-uppercase">Net Amount</small>
                        <div class="fw-bold fs-5 text-dark">
                            {{number_format($records['payroll']['total_net_amount'], 2)}}
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>


    @if($product == 'government')
    @if(!$isApproved)
        <div class="d-flex justify-content-between align-items-center mb-3">

            <button
                class="btn btn-success"
                wire:click="$set('showAddModal', true)"
            >
                <i class="fa fa-plus"></i>
                Add Employee
            </button>

        </div>
    @endif
    {{-- ADD EMPLOYEE MODAL --}}
@if($showAddModal)
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">

        <h5 class="fw-bold mb-3">
            Add Employee to Overtime Payroll
        </h5>

        {{-- SEARCH --}}
        <input
            type="text"
            class="form-control mb-3"
            placeholder="Search employee name or employee no..."
            wire:keyup="searchEmployeeAction($event.target.value)"
        >

        {{-- RESULTS --}}
        <div>

            @foreach($employeeResults ?? [] as $emp)
                <div
                    class="border rounded p-2 mb-2 cursor-pointer"
                    style="cursor:pointer"
                    wire:click="selectEmployee({{ $emp->id }})"
                >
                    <strong>{{ $emp->employee_no }}</strong>
                    —
                    {{ $emp->name }}
                </div>
            @endforeach

        </div>


        {{-- SELECTED EMPLOYEE --}}
        @if($selectedEmployee)

            <div class="border rounded p-3 mt-3 bg-light">
                <strong>{{ $selectedEmployee['name'] }}</strong>
                <br>
                {{ $selectedEmployee['position'] ?? 'N/A' }}
            </div>


            {{-- DUPLICATE WARNING --}}
            @if($showDuploicateLabel)
                <div class="alert alert-danger mt-3 mb-0">
                    {{ $duplicateMessage }}
                </div>
        
            @else
                <button
                    class="btn btn-primary mt-3"
                    wire:click="confirmAddEmployee"
                >
                    Confirm Add
                </button>
            @endif

        @endif


        {{-- CANCEL --}}
        <button
            class="btn btn-secondary mt-3"
            wire:click="$set('showAddModal', false)"
        >
            Cancel
        </button>

    </div>
</div>
@endif
        <div class="table-responsive pb-3">
            <table>
                <thead>
                    <tr>
                        @if(!$isApproved)
                            <th rowspan="2">Action</th>
                        @endif
                        <th rowspan="2" class="text-center">Name</th>
                        <th rowspan="2" class="text-center">Position</th>
                        <th rowspan="2" class="text-center">Basic Salary</th>
                        <th rowspan="2" class="text-center">Duration (HH:MM)</th>
                        <th rowspan="2" class="text-center">Amount</th>
                        <th rowspan="2" class="text-center">W/Tax</th>
                        <th rowspan="2" class="text-center">Net Amount Received</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($records['payroll_items'] as $sectionIndex => $sectionGroup)
                        <tr class="fw-bold bg-primary text-white sticky-top" style="top: 55px; z-index: 9;">
                            <td colspan="100%">
                                <div class="d-flex justify-content-between w-100 px-5">
                                    <span class="text-center flex-grow-1">{{ $sectionGroup['section_name'] ?? 'Unknown Section' }}</span>
                                </div>
                            </td>
                        </tr>
                        @foreach($sectionGroup['employees'] as $employeeIndex => $record)
                            <tr>
                                @if(!$isApproved)
                                <td>
                                    <button
                                        class="btn btn-sm btn-danger"
                                        wire:click="confirmDelete({{ $sectionIndex }}, {{ $employeeIndex }})"
                                    >
                                        Delete
                                    </button>
                                </td>
                                @endif
                                
                                <td>
                                    <a href="{{ route('hris.show', ['employee_no' => $record['employee_no'], 'form' => 'information']) }}"
                                    class="text-dark" target="_blank">
                                    #{{ $employeeIndex + 1 }} -  {{ $record['name'] }}
                                    </a>
                                </td>
                                <td>{{ $record['position'] }}</td>
                                <td style="min-width:150px;">
                                    <input
                                        type="text"
                                        class="form-control text-end"
                                        wire:model="basic_salary.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                        wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                    >
                                </td>
                                <td style="min-width:140px;">
                                    <input
                                        type="text"
                                        class="form-control text-center"
                                        placeholder="HH:MM"
                                        wire:model="duration.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                        wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                    >
                                
                                    
                                </td>
                                <td style="min-width:150px;">
                                    <input
                                        type="text"
                                        class="form-control text-end bg-light"
                                        wire:model="amount.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                        readonly
                                    >
                                </td>
                                
                                <td style="min-width:140px;">
                                    <input
                                        type="text"
                                        class="form-control text-end"
                                        wire:model="tax.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                        wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                    >
                                </td>
                                <td style="min-width:150px;">
                                    <input
                                        type="text"
                                        class="form-control text-end fw-bold bg-light"
                                        wire:model="net_amount.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                        readonly
                                    >
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="12" class="py-3 text-uppercase fw-bold text-muted">
                                No data found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($hasChanges)
        <div class="d-flex justify-content-end mt-5">
            <button class="btn btn-primary px-5 py-3 text-uppercase" wire:loading.attr="disabled" wire:click="save">
                <span wire:loading.remove wire:target="save">Save Changes</span>
                <span wire:loading wire:target="save">
                    Saving <i class="fa-solid fa-spinner fa-spin"></i>
                </span>
            </button>
        </div>
    @endif

    @if(!$isApproved && !$hasChanges)
        <div class="d-flex justify-content-end mt-5">
            <button class="btn btn-primary px-5 py-3 text-uppercase" wire:loading.attr="disabled" wire:click="approve">
                <span wire:loading.remove wire:target="approve">Approve</span>
                <span wire:loading wire:target="approve">
                    Please Wait <i class="fa-solid fa-spinner fa-spin"></i>
                </span>
            </button>
        </div>
    @endif

    @if($confirmingDelete)
    <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
        style="background: rgba(0,0,0,0.5); z-index: 10000;">

        <div class="bg-white p-4 rounded shadow text-center" style="width: 400px;">
            <h5 class="fw-bold mb-3">Delete Employee?</h5>
            <p>This will permanently remove this employee from payroll.</p>

            <div class="d-flex justify-content-center gap-2 mt-3">
                <button
                    class="btn btn-secondary"
                    wire:click="$set('confirmingDelete', false)"
                >
                    Cancel
                </button>

                <button
                    class="btn btn-danger"
                    wire:click="deleteEmployee"
                >
                    Confirm Delete
                </button>
            </div>
        </div>
    </div>
    @endif
    <div 
    wire:loading.delay
        wire:target="deleteEmployee,save,approve,confirmAddEmployee,confirmSave,selectEmployee,searchEmployeeAction,recompute"
        wire:loading.class.remove="d-none"
        class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
        style="background: rgba(0,0,0,0.5); z-index: 9999;"
    >
    <div class="bg-white p-4 rounded shadow text-center">
        <i class="fa-solid fa-spinner fa-spin fa-2x mb-2"></i>
        <div class="fw-bold">Processing payroll...</div>
    </div>
</div>
</div>