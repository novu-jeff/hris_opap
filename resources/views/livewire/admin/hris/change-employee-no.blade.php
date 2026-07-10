<div @if($isMigrating) wire:poll.3s="checkMigrationStatus" @endif>
     <div class="modal fade" wire:ignore.self id="change_employee_no" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Changing Employee Number</h1>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <ul wire:ignore class="nav nav-pills mb-3 pointer-events-none" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold text-uppercase" id="notice-tab"
                                data-bs-toggle="pill" data-bs-target="#notice" type="button" role="tab"
                                aria-controls="notice" aria-selected="true" disabled>
                                Step 1: Read Me
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-uppercase" id="change-tab"
                                data-bs-toggle="pill" data-bs-target="#change" type="button" role="tab"
                                aria-controls="change" aria-selected="false" disabled>
                                Step 2: Apply Changes
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <!-- Step 1 -->
                        <div wire:ignore.self class="tab-pane pb-3 fade show active" id="notice" role="tabpanel" aria-labelledby="notice-tab">
                            <ol class="mt-3 text-uppercase fw-bold text-muted ms-0">
                                <li class="mb-1">This will change the current Employee Number of the selected employee.</li>
                                <li class="mb-1">Any data associated with the current Employee No. will now be linked to the new one.</li>
                                <li class="mb-1">Ensure the new Employee No. is unique and unused.</li>
                                <li class="mb-1">This action may affect payroll, attendance, or linked records.</li>
                            </ol>
                            <div class="mt-5 text-end">
                                <button class="btn btn-primary px-5 py-3 text-uppercase fw-bold" type="button" onclick="goToTab('change')">
                                    Next  <i class="fa-solid fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div wire:ignore.self class="tab-pane pb-3 fade" id="change" role="tabpanel" aria-labelledby="change-tab">
                            <!-- Progress bar goes here -->
                            @if($isMigrating)
                            <div class="mb-4">
                                <label class="fw-bold text-uppercase">Migration Progress</label>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                        role="progressbar"
                                        style="width: {{ $progress }}%"
                                        aria-valuenow="{{ $progress }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                        {{ $progress }}%
                                    </div>
                                </div>
                            </div>
                            @endif

                            
                            <form wire:submit.prevent="save">
                                @if(!$isBulk)

                                <div class="mb-3 mt-3 d-flex align-items-center gap-1 justify-content-between">
                                
                                    <div class="w-100">
                                        <label class="form-label">
                                            From
                                        </label>
                                
                                        <input
                                            class="form-control"
                                            value="{{ $current_employee_no }}"
                                            readonly>
                                    </div>
                                
                                    <div class="pt-4 px-3">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </div>
                                
                                    <div class="w-100">
                                        <label class="form-label">
                                            To
                                        </label>
                                
                                        <input
                                            wire:model.defer="new_employee_no"
                                            class="form-control">
                                    </div>
                                
                                </div>
                                
                                @else
                                
                                <div class="table-responsive">
                                
                                <table class="table table-bordered align-middle">
                                
                                    <thead>
                                
                                        <tr>
                                
                                            <th width="60">
                                                Select
                                            </th>
                                
                                            <th>
                                                Current Employee No.
                                            </th>
                                
                                            <th>
                                                New Employee No.
                                            </th>
                                
                                        </tr>
                                
                                    </thead>
                                
                                    <tbody>
                                
                                    @foreach($employees as $index => $employee)
                                
                                        <tr>
                                
                                            <td>
                                
                                                <input
                                                    type="checkbox"
                                                    wire:model="employees.{{ $index }}.selected">
                                
                                            </td>
                                
                                            <td>
                                
                                                <input
                                                    class="form-control"
                                                    value="{{ $employee['current_employee_no'] }}"
                                                    readonly>
                                
                                            </td>
                                
                                            <td>
                                
                                                <input
                                                    class="form-control"
                                                    placeholder="New Employee No."
                                                    wire:model.defer="employees.{{ $index }}.new_employee_no">
                                
                                            </td>
                                
                                        </tr>
                                
                                    @endforeach
                                
                                    </tbody>
                                
                                </table>
                                
                                </div>
                                
                                @endif


                               
                                <div class="d-flex justify-content-between mt-5">
                                    <button class="btn btn-outline-primary px-4 py-3 text-uppercase fw-bold" type="button" onclick="goToTab('notice')">
                                        <i class="fa-solid fa-arrow-left me-2"></i> Previous 
                                    </button>
                                    <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                                        <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                                        <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                                    </button>
                                </div>
                                <div class="mt-3 pb-5">
                                    @if ($errors->any())
                                        <small class="text-danger">There's an error upon submitting, please review your form.</small>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function goToTab(targetId) {
        const $triggerEl = $(`[data-bs-target="#${targetId}"]`);
        const tab = new bootstrap.Tab($triggerEl[0]);
        tab.show();
    }
</script>
