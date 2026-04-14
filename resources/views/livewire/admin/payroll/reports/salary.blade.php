<div>
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
    <div class="accordion" id="salaryAccordion">

        @foreach($groupedSalary as $month => $records)
        
            @php
                $monthKey = \Illuminate\Support\Str::slug($month);
        
                $approvedCount = $records->where('status', 'approved')->count();
                $pendingCount = $records->where('status', 'pending')->count();
            @endphp
        
            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3">
        
                <!-- HEADER -->
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $monthKey }}">
        
                        <div class="d-flex justify-content-between w-100 pe-3">
                            <span>{{ $month }}</span>
        
                            <div class="d-flex gap-2">
                                <span class="badge bg-success">
                                    {{ $approvedCount }} Approved
                                </span>
                                <span class="badge bg-warning text-dark">
                                    {{ $pendingCount }} Pending
                                </span>
                            </div>
                        </div>
                    </button>
                </h2>
        
                <!-- BODY -->
                <div id="{{ $monthKey }}"
                     class="accordion-collapse collapse"
                     data-bs-parent="#salaryAccordion">
        
                    <div class="accordion-body bg-light">
        
                        <div class="table-responsive bg-white rounded-3 border">
                            <table class="table table-hover align-middle mb-0">
        
                                <thead class="table-light position-sticky top-0">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cut Off</th>
                                        <th>Date</th>
                                        <th>Deductions</th>
                                        <th>Status</th>
                                        <th width="160">Action</th>
                                    </tr>
                                </thead>
        
                                <tbody>
                                    @foreach($records as $record)
        
                                        @php
                                            $dates = explode(' to ', $record->cut_off_period);
                                            $startDate = \Carbon\Carbon::parse($dates[0])->format('M d');
                                            $endDate = \Carbon\Carbon::parse($dates[1])->format('M d');
        
                                            $isApproved = $record->status === 'approved';
                                        @endphp
        
                                        <tr>
        
                                            <td class="fw-bold">
                                                #{{ format_id($record->id, 6) }}
                                            </td>
        
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">
                                                    {{ $startDate }} - {{ $endDate }}
                                                </span>
                                            </td>
        
                                            <td>
                                                {{ \Carbon\Carbon::parse($record->payroll_date)->format('M d, Y') }}
                                            </td>
        
                                            <td>
                                                <span class="badge {{ $record->hasDeductions ? 'bg-danger' : 'bg-info' }}">
                                                    {{ $record->hasDeductions ? 'YES' : 'NO' }}
                                                </span>
                                            </td>
        
                                            <td>
                                                <span class="badge 
                                                    {{ $isApproved ? 'bg-success' : ($record->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                    {{ strtoupper($record->status) }}
                                                </span>
                                            </td>
        
                                            <!-- ✅ ACTIONS PRESERVED -->
                                            <td>
                                                <div class="d-flex gap-1">
        
                                                    <a href="{{route('payroll.process', ['type' => $type, 'payroll_id' => $record->id])}}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
        
                                                    @if(!$isApproved)
                                                        <button class="btn btn-sm btn-outline-info"
                                                                wire:click="regeneratePayroll('{{ $record->id }}')">
                                                            <i class="fa-solid fa-rotate"></i>
                                                        </button>
                                                    @endif
        
                                                    <button class="btn btn-sm btn-outline-danger"
                                                            wire:click="removePayroll('true', '{{ $record->id }}')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
        
                                                </div>
                                            </td>
        
                                        </tr>
        
                                    @endforeach
                                </tbody>
        
                            </table>
                        </div>
        
                    </div>
                </div>
            </div>
        
        @endforeach
        
        </div>
    <div class="mt-4">
        {{ $salary->links(data: ['scrollTo' => false]) }}
    </div>
</div>
