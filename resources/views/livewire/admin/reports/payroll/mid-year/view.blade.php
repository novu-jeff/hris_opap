@php
    $types = explode(',', $records['payroll']['condition_employment_type']);
@endphp

<div>

    <div class="action mb-4"></div>

    <hr class="mt-0">

    <!-- FILTERS -->
    <div class="row mb-3 align-items-end">

        <!-- Filter by Salary Method -->
        <div class="col-md-4">
            <label class="fw-bold">FILTER BY SALARY METHOD</label>

            <select wire:model.live="filterSalaryMethod" class="form-select">

                <option value="">ALL</option>
                <option value="cash">CASH</option>
                <option value="land bank atm">LAND BANK ATM</option>
                <option value="hold">HOLD (with ATM)</option>
                <option value="unhold">HOLD (No ATM)</option>

            </select>
        </div>

        <!-- Search -->
        <div class="col-md-4">
            <label class="fw-bold">SEARCH BY NAME</label>

            <input
                type="text"
                wire:model.live="searchName"
                class="form-control"
                placeholder="ENTER EMPLOYEE NAME"
            >
        </div>

        <!-- Export -->
        <div class="col-md-4 d-flex justify-content-start justify-content-md-end">

            <button
                wire:click="exportExcelMidYear"
                class="btn btn-success mt-2 mt-md-0"
            >
                EXPORT TO EXCEL
            </button>

        </div>

    </div>

    <hr>

    <!-- SUMMARY -->
    <div class="row mb-4">

        <!-- LEFT -->
        <div class="col-md-4">

            <div class="info-row">
                <span class="info-label">NO. OF EMPLOYEES:</span>

                <span class="info-value">
                    {{ $records['payroll']['no_employees'] }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">PAYROLL DATE:</span>

                <span class="info-value">
                    {{ strtoupper($records['payroll']['formatted_payroll_date']) }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">EMPLOYMENT TYPE:</span>

                <span class="info-value">
                    {{ strtoupper($records['payroll']['formatted_employment_type']) }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">BONUS TYPE:</span>

                <span class="info-value">
                    {{ strtoupper(str_replace('_', ' ', $records['payroll']['bonus_type'])) }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">COVERAGE PERIOD:</span>

                <span class="info-value">

                    {{ strtoupper(\Carbon\Carbon::parse($records['payroll']['coverage_from'])->format('M d, Y')) }}

                    -

                    {{ strtoupper(\Carbon\Carbon::parse($records['payroll']['coverage_to'])->format('M d, Y')) }}

                </span>
            </div>

            @if(!empty($records['payroll']['semester']))
                <div class="info-row">
                    <span class="info-label">SEMESTER:</span>

                    <span class="info-value">
                        {{ strtoupper(str_replace('_', ' ', $records['payroll']['semester'])) }}
                    </span>
                </div>
            @endif

        </div>

        <!-- RIGHT -->
        <div class="col-md-4">

            <div class="info-row">
                <span class="info-label">TOTAL BONUS:</span>

                <span class="info-value">
                    PHP {{ number_format($records['totals']['bonus'], 2) }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">TOTAL TAX:</span>

                <span class="info-value text-danger">
                    PHP {{ number_format($records['totals']['tax'], 2) }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">NET AMOUNT:</span>

                <span class="info-value fw-bold text-success">
                    PHP {{ number_format($records['totals']['net_amount'], 2) }}
                </span>
            </div>

        </div>

    </div>

    <hr class="pt-2">

    <!-- TABLE -->
    <div class="table-responsive overflow-auto">

        <table class="table table-bordered table-striped table-hover">

            <thead>

                <tr class="bg-primary text-white sticky-top">

                    <th>EMPLOYEE NAME</th>
                    <th>POSITION</th>
                    <th class="text-end">DATE HIRED</th>
                    <th class="text-end">AMOUNT</th>
                    <th class="text-end">PERCENTAGE</th>
                    <th class="text-end">MID YEAR BONUS</th>

                </tr>

            </thead>

            <tbody>

                @forelse($records['payroll_items'] as $sectionGroup)

                    <!-- SECTION -->
                    <tr class="section-row">

                        <td colspan="7" class="fw-bold bg-light">

                            {{ strtoupper($sectionGroup['section_name'] ?? 'UNKNOWN SECTION') }}

                        </td>

                    </tr>

                    @foreach($sectionGroup['employees'] as $item)

                        <tr>

                            <!-- NAME -->
                            <td class="fw-semibold">

                                {!! $this->highlightSearchTerm(strtoupper($item['name'])) !!}

                            </td>

                            <!-- POSITION -->
                            <td>
                                {{ strtoupper($item['position']) }}
                            </td>

                            <!-- BASIC -->
                            <td class="text-end">
                                {{ $item['date_hired'] }}
                            </td>

                            <!-- BONUS -->
                            <td class="text-end">
                                {{ number_format($item['bonus'], 2) }}
                            </td>

                            <!-- TAX -->
                            <td class="text-end text-danger">
                                {{ $item['percentage'] }}
                            </td>

                            <!-- NET -->
                            <td class="text-end fw-bold text-success">
                                {{ number_format($item['net_amount'], 2) }}
                            </td>

                        </tr>

                    @endforeach

                    <!-- SECTION TOTAL -->
                    @if(isset($sectionGroup['section_totals']))

                        <tr class="fw-bold bg-light">

                            <td colspan="3" class="text-end">
                                SECTION TOTAL
                            </td>

                            <td class="text-end">
                                {{ number_format($sectionGroup['section_totals']['bonus'], 2) }}
                            </td>

                            <td class="text-end text-danger">
                                
                            </td>

                            <td class="text-end text-success">
                                {{ number_format($sectionGroup['section_totals']['net_amount'], 2) }}
                            </td>

                        </tr>

                    @endif

                @empty

                    <tr>

                        <td colspan="7" class="text-center text-muted py-4">

                            NO MID-YEAR BONUS PAYROLL DATA FOUND.

                        </td>

                    </tr>

                @endforelse

            </tbody>

            <!-- GRAND TOTAL -->
            <tfoot>

                <tr class="fw-bold bg-dark text-white">

                    <td colspan="3" class="text-end">
                        GRAND TOTAL
                    </td>

                    <td class="text-end">
                        {{ number_format($records['totals']['bonus'], 2) }}
                    </td>

                    <td class="text-end text-danger">
                        
                    </td>

                    <td class="text-end text-success">
                        {{ number_format($records['totals']['net_amount'], 2) }}
                    </td>

                </tr>

            </tfoot>

        </table>

    </div>

</div>