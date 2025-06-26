<div>
    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <div class="row mb-3 mt-5">
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <label for="entries" class="form-label mb-0">Show entries:</label>
                    <select id="entries" wire:model.change="entries" class="form-select w-auto">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                        <option value="60">60</option>
                        <option value="70">70</option>
                        <option value="80">80</option>
                        <option value="90">90</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
                    <label for="search" class="form-label mb-0">Filter Status:</label>
                    <select wire:model.change="status" id="status" class="form-select w-50">
                        <option value=""> - ALL - </option>
                        <option value="pending"> Pending </option>
                        <option value="approved"> Approved </option>
                        <option value="disapproved"> Disapproved </option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cut Off Period</th>
                            <th>Payroll Date</th>
                            <th>Status</th>
                            <th style="max-width: 200px;">Action</th>
                        </tr>
                    </thead>                
                    <tbody>
                        @forelse($records as $record)
                            <tr data-id="{{$record->id}}">
                                <td>#{{format_id($record->id, 6)}}</td>
                                <td>
                                    @php
                                        $dates = explode(' to ', $record->cut_off_period);
                                        $startDate = \Carbon\Carbon::parse($dates[0])->format('F d, Y');
                                        $endDate = \Carbon\Carbon::parse($dates[1])->format('F d, Y');
                                    @endphp

                                    {{ $startDate }} - {{ $endDate }}
                                </td>
                                <td>{{\Carbon\Carbon::parse($record->payroll_date)->format('F d, Y')}}</td>
                                <td>{{$record->status}}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{route('payroll.process', ['payroll_id' => $record->id])}}" class="btn btn-primary">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <button class="btn btn-info" title="Regenerate Payroll" wire:click="regeneratePayroll('{{$record->id}}')">
                                            <i class="fa-solid fa-arrows-rotate fa-spin"></i>
                                        </button>
                                        <button class="btn btn-danger" wire:click="removePayroll('true', '{{$record->id}}')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                            </tr> 
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $records->links(data: ['scrollTo' => false]) }}
                </div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" data-bs-backdrop="static" id="newSalaryPayroll" tabindex="-1" aria-labelledby="newSalaryPayrollLabel" aria-hidden="true">
        <div class="modal-dialog {{ $isToCreate ? 'modal-lg' : '' }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase fw-medium" id="newSalaryPayrollLabel">New Payroll</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <form wire:submit.prevent="createPayroll">
                        @if(!$isToCreate) 
                            <div class="mb-3">
                                <label for="cutOffPeriod" class="form-label">Cut Off Period</label>
                                <input type="text" class="form-control" id="daterangepicker" wire:model='cut_off_period' id="cut_off_period" wire:model="cutOffPeriod">
                                <div class="error-field">
                                    @error('cut_off_period') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="payrollDate" class="form-label">Payroll Date</label>
                                <input type="date" class="form-control"  wire:model='payroll_date' id="payroll_date" wire:model="payrollDate">
                                <div class="error-field">
                                    @error('payroll_date') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="employmentType" clsass="form-label">Employment Type</label>
                                <select name="employment_type_id" id="employment_type_id" wire:model="employment_type_id" class="form-select mt-2">
                                    <option value=""> - CHOOSE - </option>
                                    @foreach($employmentTypes as $type)
                                        <option value="{{$type->id}}">{{$type->name}}</option>
                                    @endforeach
                                </select>
                                <div class="error-field">
                                    @error('employment_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-5 pb-2">
                                <button class="btn btn-primary px-5 py-3 text-uppercase" wire:loading.attr="disabled" type="submit">
                                    <span wire:loading.remove wire:target="createPayroll">Next</span>
                                    <span wire:loading wire:target="createPayroll">
                                        Please Wait <i class="fa-solid fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            </div>
                        @else
                            <h5 class="mt-3 mb-4 text-uppercase fw-bold">Below are the eligible and ineligible for payroll processing</h5>
                            <ul class="nav nav-pills mb-3" id="employeeTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link text-uppercase fw-bold active" id="eligible-tab" data-bs-toggle="tab" data-bs-target="#eligible" type="button" role="tab" aria-controls="eligible" aria-selected="true">
                                        Eligible
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link text-uppercase fw-bold" id="ineligible-tab" data-bs-toggle="tab" data-bs-target="#ineligible" type="button" role="tab" aria-controls="ineligible" aria-selected="false">
                                        Ineligible
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="employeeTabsContent">
                                {{-- Eligible Tab --}}
                                <div class="tab-pane fade show active" id="eligible" role="tabpanel" aria-labelledby="eligible-tab">
                                    <div style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-striped table-bordered w-100 mt-4">
                                            <thead>
                                                <tr>
                                                    <th>Employee No</th>
                                                    <th>Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($employeesChecked['eligible'] as $employee)
                                                    <tr>
                                                        <td>{{ $employee['employee_no'] ?? 'N/A' }}</td>
                                                        <td>{{ $employee['name'] }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                                                    </tr> 
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Ineligible Tab --}}
                                <div class="tab-pane fade" id="ineligible" role="tabpanel" aria-labelledby="ineligible-tab">
                                    <div style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-striped table-bordered w-100 mt-4">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Employee No</th>
                                                    <th>Name</th>
                                                    <th>Reason</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($employeesChecked['ineligible'] as $employee)
                                                    <tr>
                                                        <td>{{ $employee['employee_no'] ?? 'N/A' }}</td>
                                                        <td>{{ $employee['name'] }}</td>
                                                        <td>{{ is_array($employee['reason']) ? implode(', ', $employee['reason']) : $employee['reason'] }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                                                    </tr> 
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4 pb-3">
                                <button class="btn btn-outline-primary px-5 py-3 text-uppercase" type="button" wire:click="go_back">
                                    Go Back
                                </button>
                                <button class="btn btn-primary px-5 py-3 text-uppercase" wire:loading.attr="disabled" type="submit">
                                    <span wire:loading.remove wire:target="createPayroll">Create</span>
                                    <span wire:loading wire:target="createPayroll">
                                        Creating <i class="fa-solid fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($isBatchProcessing) 
        <div 
            class="modal fade d-block show"
            data-bs-backdrop="static"
            data-bs-keyboard="false"
            tabindex="-1"
            aria-modal="true"
            role="dialog"
            style="background-color: rgba(0, 0, 0, 0.5);"
        >
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content text-center py-4 shadow">
                    <div class="modal-body">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        
                        <p class="fw-bold mb-0 text-uppercase text-muted">
                            {{ $batchStatusMessage }}
                        </p>

                        <div class="px-4">
                            <div class="progress mb-2 mt-3" style="height: 30px;">
                                <div 
                                    class="progress-bar progress-bar-striped progress-bar-animated bg-primary fw-bold" 
                                    role="progressbar" 
                                    style="width: {{ $batchProgress }}%; font-size: 12px;" 
                                    aria-valuenow="{{ $batchProgress }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    {{ $batchProgress }}%
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <button class="btn btn-danger text-uppercase fw-bold px-4 py-2" wire:click="cancelPayroll" wire:loading.attr="disabled">
                                <i class="fa-solid fa-circle-xmark me-1"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div wire:poll.3000ms="checkBatchStatus"></div>
        </div>    
    @endif
</div>

@section('script')
    <script>
        $(function() {

            $('#daterangepicker').daterangepicker({
                locale: { format: 'YYYY-MM-DD' },
                autoUpdateInput: false
            });

            $('#daterangepicker').on('apply.daterangepicker', function(ev, picker) {
                @this.set('cut_off_period', picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
            });

            Livewire.on('start-job-dispatch', (event) => {
                const payroll_id = event[0].payroll_id;
                const employment_type = event[0].employment_type

                setTimeout(() => {
                    Livewire.dispatch('dispatchPayrollJobs', [payroll_id, employment_type]);
                }, 100);
            });

        });
    </script>
@endsection