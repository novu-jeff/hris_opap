
<div class="container-fluid px-4">

    {{-- STATUS HEADER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <h3 class="fw-bold mb-1 text-uppercase">
                        Gratuity Payroll Details
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
                            <small class="text-muted text-uppercase">Payroll Date</small>
                            <div class="fw-bold">
                                {{ $records['payroll']['formatted_payroll_date'] }}
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
                                {{ $records['payroll']['no_employees'] }}
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">

                       

                        <div class="mb-3">
                            <small class="text-muted text-uppercase">
                                Total Gratuity
                            </small>
                            <div class="fw-bold text-primary">
                                PHP {{ number_format($records['payroll']['total_gratuity_pay'] ?? 0, 2) }}
                            </div>
                        </div>

                        
                            <div class="mb-3">
                                <small class="text-muted text-uppercase">Tax</small>
                                <div class="fw-bold text-danger">
                                    PHP {{ number_format($records['payroll']['total_tax'] ?? 0, 2) }}
                                </div>
                            </div>
                    

                        <div>
                            <small class="text-muted text-uppercase">Net Amount</small>
                            <div class="fw-bold fs-5 text-dark">
                                PHP {{ number_format($records['payroll']['total_net_amount'] ?? 0, 2) }}
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>


    {{-- ACTION BAR --}}
    @if(!$isApproved)
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

        <button
            class="btn btn-success px-4 py-2 shadow-sm"
            wire:click="$set('showAddModal', true)"
        >
            <i class="fa fa-plus me-2"></i>
            Add Employee
        </button>

    </div>
    @endif

{{-- ADD EMPLOYEE MODAL --}}
@if($showAddModal)
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">

        <h5 class="fw-bold mb-3">
            Add Employee to Gratuity Payroll
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
    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle table-bordered premium-table">
            
                    <thead class="table-light">

                        <tr>
                    
                            @if(!$isApproved)
                                <th width="120">ACTION</th>
                            @endif
                    
                            <th>NO.</th>
                            <th>EMPLOYEE</th>
                            <th>POSITION</th>
                            <th>DATE HIRED</th>
                            <th>PAYROLL DATE</th>
                            <th class="text-end">GRATUITY PAY</th>
                            <th class="text-end">TAX (5%)</th>
                            <th class="text-end">NET AMOUNT</th>
                    
                        </tr>
                    
                    </thead>
            
                    <tbody>

    @forelse($records['payroll_items'] as $sectionIndex => $sectionGroup)

        {{-- SECTION --}}
        <tr>

            <td colspan="100%"
                class="bg-primary text-white fw-bold text-center py-3">

                {{ strtoupper($sectionGroup['section_name'] ?? 'UNKNOWN SECTION') }}

            </td>

        </tr>

        @foreach($sectionGroup['employees'] as $employeeIndex => $record)

            <tr>

                {{-- ACTION --}}
                @if(!$isApproved)
                <td>

                    <button
                        class="btn btn-sm btn-outline-danger"
                        wire:click="confirmDelete({{ $sectionIndex }}, {{ $employeeIndex }})"
                    >
                        <i class="fa fa-trash"></i>
                    </button>

                </td>
                @endif

                {{-- NO --}}
                <td>

                    <div class="d-flex align-items-center gap-2">

                        @if(in_array($record['id'], $updatedItems))
                            <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                        @else
                            <i class="fa-solid fa-check text-success"></i>
                        @endif

                        {{ $employeeIndex + 1 }}

                    </div>

                </td>

                {{-- EMPLOYEE --}}
                <td>

                    <a
                        href="{{ route('hris.show', [
                            'employee_no' => $record['employee_no'],
                            'form' => 'information'
                        ]) }}"
                        target="_blank"
                        class="text-decoration-none fw-semibold text-dark"
                    >

                        {{ strtoupper($record['name']) }}

                    </a>

                </td>

                {{-- POSITION --}}
                <td>
                    {{ strtoupper($record['position']) }}
                </td>

                {{-- BASIC SALARY --}}
                <td class="text-end">
                    {{ !empty($record['date_hired'])
                        ? \Carbon\Carbon::parse($record['date_hired'])->format('M d, Y')
                        : 'N/A' }}
                    
                </td>

                {{-- DATE HIRED --}}
                <td>
                    {{ $records['payroll']['formatted_payroll_date'] }}
                </td>

                {{-- GRATUITY PAY --}}
                <td class="text-end" style="min-width: 120px;">

                    <input
                        type="text"
                        wire:model="gratuity_pay.{{ $sectionIndex }}.{{ $employeeIndex }}"
                        wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                        class="form-control text-end {{ $isApproved ? 'restricted' : '' }}"
                        {{ $isApproved ? 'readonly' : '' }}
                    >

                </td>

                {{-- TAX --}}
                <td class="text-end" style="min-width: 120px;">

                    <input
                        type="text"
                        wire:model="tax.{{ $sectionIndex }}.{{ $employeeIndex }}"
                        class="form-control text-end"
                        readonly
                    >

                </td>

                {{-- NET --}}
                <td class="text-end" style="min-width: 120px;">

                    <input
                        type="text"
                        wire:model="net_amount.{{ $sectionIndex }}.{{ $employeeIndex }}"
                        class="form-control text-end fw-bold"
                        readonly
                    >

                </td>

            </tr>

        @endforeach

    @empty

        <tr>

            <td colspan="100%" class="text-center py-5 text-muted fw-bold">

                NO GRATUITY PAYROLL RECORDS FOUND

            </td>

        </tr>

    @endforelse

</tbody>
            
                    
            
                </table>
            
            </div>

        </div>
    </div>


    {{-- FOOTER ACTIONS --}}
    <div class="d-flex justify-content-end mt-4">

        @if($hasChanges)
            <button
                class="btn btn-primary px-5 py-3 shadow-sm"
                wire:click="save"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="save">
                    Save Changes
                </span>

                <span wire:loading wire:target="save">
                    Saving...
                    <i class="fa fa-spinner fa-spin"></i>
                </span>
            </button>
        @endif

        @if(!$isApproved && !$hasChanges)
            <button
                class="btn btn-success px-5 py-3 shadow-sm"
                wire:click="approve"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="approve">
                    Approve Payroll
                </span>

                <span wire:loading wire:target="approve">
                    Processing...
                    <i class="fa fa-spinner fa-spin"></i>
                </span>
            </button>
        @endif

    </div>

    @if($confirmingDelete)
    <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
        style="background: rgba(0,0,0,0.5); z-index: 10000;">

        <div class="bg-white p-4 rounded shadow text-center" style="width: 400px;">
            <h5 class="fw-bold mb-3">Delete Employee?</h5>
            <p>This will permanently remove this employee from payroll.</p>

            <div class="d-flex justify-content-center gap-2 mt-3">
                <button class="btn btn-secondary"
                        wire:click="$set('confirmingDelete', false)">
                    Cancel
                </button>

                <button 
                    class="btn btn-danger"
                    wire:click="deleteEmployee"
                    wire:loading.attr="disabled"
                    wire:target="deleteEmployee">

                    <span wire:loading.remove wire:target="deleteEmployee">
                        Confirm Delete
                    </span>

                    <span wire:loading wire:target="deleteEmployee">
                        Deleting...
                    </span>
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




