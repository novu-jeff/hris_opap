<div> <!-- single root wrapper for Livewire -->

    <!-- Filters -->
    <div class="row mb-3">
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

    <!-- Show entries + Search -->
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
            <input
                type="text"
                wire:model.live="search"
                class="form-control"
                placeholder="Search Payroll ID or Employee"
            >
        </div>
    </div>

    <!-- Payroll Accordion -->
    <div class="accordion" id="payrollAccordion">

        @forelse($groupedPayrolls as $month => $payrolls)
            @php
                $monthKey = \Illuminate\Support\Str::slug($month);

                $total = $payrolls->sum('items_sum_net_amount');
                $count = $payrolls->sum('employee_count');
                $payrollCount = $payrolls->count();
            @endphp

            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">

                <!-- HEADER -->
                <h2 class="accordion-header">
                    <button
                        class="accordion-button collapsed fw-bold fs-5"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#{{ $monthKey }}"
                    >
                        <div class="d-flex justify-content-between w-100 pe-3">
                            <span>{{ $month }}</span>

                            <span class="text-muted small">
                                {{ $payrollCount }} payroll{{ $payrollCount > 1 ? 's' : '' }}
                            </span>
                        </div>
                    </button>
                </h2>

                <!-- BODY -->
                <div
                    id="{{ $monthKey }}"
                    class="accordion-collapse collapse"
                    data-bs-parent="#payrollAccordion"
                >
                    <div class="accordion-body bg-light">

                        <!-- Summary Card -->
                        <div class="row mb-4 g-3">
                            <div class="col-md-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="fw-bold text-primary">
                                            Monthly EME RATA Summary
                                        </div>

                                        <div class="fs-4 fw-bold">
                                            ₱{{ number_format($total, 2) }}
                                        </div>

                                        <div class="text-muted small">
                                            {{ $count }} employees
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payroll Table -->
                        @include('livewire.admin.reports.payroll.partials.eme-rata-payroll-table-modern', [
                            'payrolls' => $payrolls,
                            'type' => 'eme_rata'
                        ])

                    </div>
                </div>
            </div>

        @empty
            <div class="text-center py-5">
                <h5 class="fw-bold text-muted">
                    No approved EME RATA payroll records found
                </h5>
            </div>
        @endforelse

    </div>

</div>