<div> <!-- single root wrapper for Livewire -->
    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-3 mb-2 mb-md-0">
            <label for="cut_off_period" class="form-label mb-0">Cut-off Period</label>
            <select id="cut_off_period" wire:model.change="cutoffPeriod" class="form-select">
                <option value="">All</option>
                @foreach($cutOffPeriods as $period)
                    <option value="{{ $period }}">{{ $period }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 mb-2 mb-md-0">
            <label class="fw-bold">Filter by Employment Type</label>
            <select wire:model.live="filterEmploymentType" class="form-select">
                <option value="">All</option>
                @foreach($employmentTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Second row: Show entries & Search input -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-3 mb-2 mb-md-0 d-flex align-items-center gap-2">
            <label for="entries" class="form-label mb-0">Show entries:</label>
            <select id="entries" wire:model.live="entries" class="form-select w-auto">
                @foreach([5,10,20,30,40,50,60,70,80,90,100] as $num)
                    <option value="{{ $num }}">{{ $num }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 offset-md-5 mt-2 mt-md-0">
            <input type="text" wire:model.live="search" class="form-control" placeholder="Search Payroll ID or Type">
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="accordion" id="payrollAccordion">

        @foreach($groupedPayrolls as $month => $halves)
            @php
                $monthKey = Str::slug($month);
        
                $firstTotal = $halves['first_half']->sum('items_sum_net_amount');
                $secondTotal = $halves['second_half']->sum('items_sum_net_amount');
        
                $firstCount = $halves['first_half']->sum('employee_count');
                $secondCount = $halves['second_half']->sum('employee_count');
            @endphp
        
            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                
                <!-- HEADER -->
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold fs-5"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $monthKey }}">
                        
                        <div class="d-flex justify-content-between w-100 pe-3">
                            <span>{{ $month }}</span>
        
                            <span class="text-muted small">
                                {{ $halves['first_half']->count() + $halves['second_half']->count() }} payrolls
                            </span>
                        </div>
                    </button>
                </h2>
        
                <!-- BODY -->
                <div id="{{ $monthKey }}"
                     class="accordion-collapse collapse"
                     data-bs-parent="#payrollAccordion">
        
                    <div class="accordion-body bg-light">
        
                        <!-- SUMMARY CARDS -->
                        <div class="row mb-4 g-3">
        
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="text-primary fw-bold">1st Half (1–15)</div>
                                        <div class="fs-4 fw-bold">
                                            ₱{{ number_format($firstTotal, 2) }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $firstCount }} employees
                                        </div>
                                    </div>
                                </div>
                            </div>
        
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="text-success fw-bold">2nd Half (16–end)</div>
                                        <div class="fs-4 fw-bold">
                                            ₱{{ number_format($secondTotal, 2) }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $secondCount }} employees
                                        </div>
                                    </div>
                                </div>
                            </div>
        
                        </div>
        
                        <!-- FIRST HALF -->
                        @if($halves['first_half']->count())
                            <h6 class="fw-bold text-primary mb-2">
                                1st Half (1–15)
                            </h6>
        
                            @include('livewire.admin.reports.payroll.partials.payroll-table-modern', [
                                'payrolls' => $halves['first_half'],
                                'type' => 'first'
                            ])
                        @endif
        
                        <!-- SECOND HALF -->
                        @if($halves['second_half']->count())
                            <h6 class="fw-bold text-success mt-4 mb-2">
                                2nd Half (16–end)
                            </h6>
        
                            @include('livewire.admin.reports.payroll.partials.payroll-table-modern', [
                                'payrolls' => $halves['second_half'],
                                'type' => 'second'
                            ])
                        @endif
        
                    </div>
                </div>
            </div>
        @endforeach
        
        </div>
</div>
