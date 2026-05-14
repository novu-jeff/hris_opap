<div>
    <!-- <div class="action mb-4">
        <div class="d-md-flex justify-content-end gap-3">
            <a href="{{route('payroll.index', [
                'type' => $type,
                'employment_type' => $employment_type
            ])}}" 
            class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
        </div>
    </div> -->
    <hr class="mt-0">
    <div class="text-uppercase fw-bold">
        @if($isApproved)
            <h2 class="text-success fw-bold text-uppercase text-center">Approved</h2>
        @else
            <h2 class="text-danger fw-bold text-uppercase text-center">Pending</h2>
        @endif
    </div>
    <hr>
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="text-uppercase fw-bold">
                Type : <span class="ms-2">{{$records['payroll']['type']}}</span>
            </div>
            <div class="text-uppercase fw-bold">
                Date : <span class="ms-2">{{$records['payroll']['formatted_payroll_date']}}</span>
            </div>
            
            <div class="text-uppercase fw-bold">
                Employee Type : <span class="ms-2">{{$records['payroll']['formatted_employment_type']}}</span>
            </div>
            <div class="text-uppercase fw-bold">
                No. of employees : <span class="ms-2">{{$records['payroll']['no_employees']}}</span>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="text-uppercase fw-bold">
                RA Amount : <span class="ms-2 ">PHP {{number_format($records['payroll']['overall_ra_amount'], 2)}}</span>
            </div>
            <div class="text-uppercase fw-bold">
                TA Amount : <span class="ms-2">PHP {{number_format($records['payroll']['overall_ta_amount'], 2)}}</span>
            </div>
            <div class="text-uppercase fw-bold">
                Net Amount : <span class="ms-2">PHP {{number_format($records['payroll']['overall_net_amount'], 2)}}</span>
            </div>
            
        </div>
    </div>
    <hr class="pt-3">

    @if($product == 'government')
        {{-- ADD EMPLOYEE BUTTON --}}
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
            Add Employee to Rata Payroll
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
                    Employee already exists in this payroll
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
<div class="table-responsive pb-3">
<table class="table table-bordered table-striped align-middle">

    <thead>
        <tr>
            @if(!$isApproved)
                <th width="120">Action</th>
            @endif

            <th>No.</th>
            <th>Name</th>
            <th>Position</th>
            <th>RA</th>
            <th>TA</th>
            <th>Net Amount</th>
        </tr>
    </thead>

    <tbody>

        @forelse($records['payroll_items'] as $sectionIndex => $sectionGroup)

            {{-- SECTION HEADER --}}
            <tr class="table-primary fw-bold">
                <td colspan="100%" class="text-center">
                    {{ $sectionGroup['section_name'] ?? 'Unknown Section' }}
                </td>
            </tr>


            @foreach($sectionGroup['employees'] as $employeeIndex => $record)

                <tr>

                    {{-- DELETE BUTTON --}}
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
                        #{{ $employeeIndex + 1 }}
                    </td>

                    <td>
                        <a
                            href="{{ route('hris.show', [
                                'employee_no' => $record['employee_no'],
                                'form' => 'information'
                            ]) }}"
                            class="text-dark"
                            target="_blank"
                        >
                            {{ $record['name'] }}
                        </a>
                    </td>

                    <td>
                        {{ $record['position'] }}
                    </td>

                    <td>
                        {{ number_format($record['ra'], 2) }}
                    </td>

                    <td>
                        {{ number_format($record['ta'], 2) }}
                    </td>

                    <td>
                        {{ number_format($record['net_amount'], 2) }}
                    </td>

                </tr>

            @endforeach

        @empty

            <tr>
                <td colspan="12" class="text-center fw-bold py-4">
                    No data found
                </td>
            </tr>

        @endforelse

    </tbody>

</table>
</div>
    @endif

   

    @if($hasChanges || !empty($newItems))
 <!--   <div class="d-flex justify-content-end mt-5">
        <button class="btn btn-primary px-5 py-3 text-uppercase" wire:loading.attr="disabled" wire:click="save">
            <span wire:loading.remove wire:target="save">Save Changes</span>
            <span wire:loading wire:target="save">
                Saving <i class="fa-solid fa-spinner fa-spin"></i>
            </span>
        </button>
    </div>-->
@endif

@if(!$isApproved)
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
