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
                wire:click="exportExcelPremium"
                class="btn btn-success mt-2 mt-md-0"
            >
                EXPORT TO EXCEL
            </button>

        </div>

    </div>

    <hr>

    <!-- SUMMARY -->
    <div class="row mb-4 g-4">

        <!-- LEFT -->
        <div class="col-lg-7">
    
            <div class="card shadow-sm border-0 h-100">
    
                <div class="card-body">
    
                    <h6 class="fw-bold text-uppercase mb-4">
                        Payroll Information
                    </h6>
    
                    <div class="row g-3">
    
                        <!-- EMPLOYEES -->
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block">
                                No. of Employees
                            </small>
    
                            <div class="fw-bold fs-6">
                                {{ $records['payroll']['no_employees'] }}
                            </div>
                        </div>
    
                        <!-- PAYROLL DATE -->
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block">
                                Payroll Date
                            </small>
    
                            <div class="fw-bold fs-6">
                                {{ strtoupper($records['payroll']['formatted_payroll_date']) }}
                            </div>
                        </div>
    
                        <!-- EMPLOYMENT TYPE -->
                        <div class="col-12">
                            <small class="text-muted text-uppercase d-block">
                                Employment Type
                            </small>
    
                            <div
                                class="fw-bold fs-6 text-uppercase"
                                style="
                                    word-break: break-word;
                                    white-space: normal;
                                    line-height: 1.4;
                                "
                            >
                                {{ $records['payroll']['formatted_employment_type'] }}
                            </div>
                        </div>
    
                        <!-- BONUS TYPE -->
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block">
                                Premium Type
                            </small>
    
                            <div class="fw-bold fs-6 text-uppercase">
                                {{ str_replace('_', ' ', $records['payroll']['bonus_type']) }}
                            </div>
                        </div>
    
                        <!-- SEMESTER -->
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block">
                                Semester
                            </small>
    
                            <div class="fw-bold fs-6 text-uppercase">
                                {{ str_replace('_', ' ', $records['payroll']['semester']) }}
                            </div>
                        </div>
    
                        <!-- COVERAGE -->
                        <div class="col-12">
                            <small class="text-muted text-uppercase d-block">
                                Coverage Period
                            </small>
    
                            <div class="fw-bold fs-6">
    
                                {{ strtoupper(
                                    \Carbon\Carbon::parse(
                                        $records['payroll']['coverage_from']
                                    )->format('M d, Y')
                                ) }}
    
                                -
    
                                {{ strtoupper(
                                    \Carbon\Carbon::parse(
                                        $records['payroll']['coverage_to']
                                    )->format('M d, Y')
                                ) }}
    
                            </div>
                        </div>
    
                    </div>
    
                </div>
    
            </div>
    
        </div>
    
        <!-- RIGHT -->
        <div class="col-lg-5">
    
            <div class="card shadow-sm border-0 h-100">
    
                <div class="card-body">
    
                    <h6 class="fw-bold text-uppercase mb-4">
                        Premium Summary
                    </h6>
    
                    <!-- TOTAL PREMIUM -->
                    <div class="mb-4">
    
                        <small class="text-muted text-uppercase d-block">
                            Total Net Premium
                        </small>
    
                        <div class="fw-bold text-primary fs-3">
                            ₱{{ number_format($records['totals']['net_amount'], 2) }}
                        </div>
    
                    </div>
    
                    <!-- BONUS -->
                    <div class="d-flex justify-content-between mb-3">
    
                        <span class="text-muted text-uppercase small">
                            Gross Amount
                        </span>
    
                        <span class="fw-bold">
                            ₱{{ number_format($records['totals']['bonus'], 2) }}
                        </span>
    
                    </div>
    
                    <!-- TAX -->
                    <div class="d-flex justify-content-between mb-3">
    
                        <span class="text-muted text-uppercase small">
                            Total Tax
                        </span>
    
                        <span class="fw-bold text-danger">
                            ₱{{ number_format($records['totals']['tax'], 2) }}
                        </span>
    
                    </div>
    
                    <!-- NET -->
                    <div class="d-flex justify-content-between mb-3">
    
                        <span class="text-muted text-uppercase small">
                            Net Amount
                        </span>
    
                        <span class="fw-bold text-success">
                            ₱{{ number_format($records['totals']['net_amount'], 2) }}
                        </span>
    
                    </div>
    
                    <!-- PERCENTAGE -->
                    <div class="d-flex justify-content-between">
    
                        <span class="text-muted text-uppercase small">
                            Percentage
                        </span>
    
                        <span class="fw-bold">
                            {{ number_format($records['payroll']['percentage'], 2) }}%
                        </span>
    
                    </div>
    
                </div>
    
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
                
                    <th>BASIC SALARY</th>
                
                    @if($records['payroll']['semester'] == 'first_semester')
                
                        <th class="text-end">JAN</th>
                        <th class="text-end">FEB</th>
                        <th class="text-end">MAR</th>
                        <th class="text-end">APR</th>
                        <th class="text-end">MAY</th>
                        <th class="text-end">JUN</th>
                
                    @endif
                
                    @if($records['payroll']['semester'] == 'second_semester')
                
                        <th class="text-end">JUL</th>
                        <th class="text-end">AUG</th>
                        <th class="text-end">SEP</th>
                        <th class="text-end">OCT</th>
                        <th class="text-end">NOV</th>
                        <th class="text-end">DEC</th>
                
                    @endif
                
                    <th class="text-end">TOTAL</th>
                
                    <th class="text-end">TAX</th>
                
                    <th class="text-end">NET</th>
                
                </tr>

            </thead>

            <tbody>

                @forelse($records['payroll_items'] as $sectionGroup)

                    <!-- SECTION -->
                    <tr class="section-row">

                        <td colspan="12" class="fw-bold bg-light">

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
                                {{ number_format($item['basic_salary'], 2) }}
                            </td>

                            {{-- FIRST SEM --}}
                            @if($records['payroll']['semester'] == 'first_semester')

                            <td class="text-end">
                                {{ number_format($item['january_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($item['february_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($item['march_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($item['april_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($item['may_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($item['june_amount'], 2) }}
                            </td>

                        @endif

                        {{-- SECOND SEM --}}
                        @if($records['payroll']['semester'] == 'second_semester')

                            <td class="text-end">
                                {{ number_format($item['july_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($item['august_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($item['september_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($item['october_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($item['november_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($item['december_amount'], 2) }}
                            </td>

                        @endif

                            <!-- TOTAL -->
                        <td class="text-end fw-bold">
                            {{ number_format($item['total_amount'], 2) }}
                        </td>

                        <!-- TAX -->
                        <td class="text-end text-danger">
                            {{ number_format($item['tax'], 2) }}
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

                        {{-- FIRST SEM --}}
                        @if($records['payroll']['semester'] == 'first_semester')

                            <td class="text-end">
                                {{ number_format($sectionGroup['section_totals']['january_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($sectionGroup['section_totals']['february_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($sectionGroup['section_totals']['march_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($sectionGroup['section_totals']['april_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($sectionGroup['section_totals']['may_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($sectionGroup['section_totals']['june_amount'], 2) }}
                            </td>

                        @endif

                        {{-- SECOND SEM --}}
                        @if($records['payroll']['semester'] == 'second_semester')

                            <td class="text-end">
                                {{ number_format($sectionGroup['section_totals']['july_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($sectionGroup['section_totals']['august_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($sectionGroup['section_totals']['september_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($sectionGroup['section_totals']['october_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($sectionGroup['section_totals']['november_amount'], 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($sectionGroup['section_totals']['december_amount'], 2) }}
                            </td>

                        @endif

                        <td class="text-end">
                            {{ number_format($sectionGroup['section_totals']['total_amount'], 2) }}
                        </td>

                        <td class="text-end text-danger">
                            {{ number_format($sectionGroup['section_totals']['tax'], 2) }}
                        </td>

                        <td class="text-end text-success">
                            {{ number_format($sectionGroup['section_totals']['net_amount'], 2) }}
                        </td>

                    </tr>

                    @endif

                @empty

                    <tr>

                        <td colspan="7" class="text-center text-muted py-4">

                            NO PREMIUM BONUS PAYROLL DATA FOUND.

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
                
                    {{-- FIRST SEM --}}
                    @if($records['payroll']['semester'] == 'first_semester')
                
                        <td class="text-end">
                            {{ number_format($records['totals']['january_amount'], 2) }}
                        </td>
                
                        <td class="text-end">
                            {{ number_format($records['totals']['february_amount'], 2) }}
                        </td>
                
                        <td class="text-end">
                            {{ number_format($records['totals']['march_amount'], 2) }}
                        </td>
                
                        <td class="text-end">
                            {{ number_format($records['totals']['april_amount'], 2) }}
                        </td>
                
                        <td class="text-end">
                            {{ number_format($records['totals']['may_amount'], 2) }}
                        </td>
                
                        <td class="text-end">
                            {{ number_format($records['totals']['june_amount'], 2) }}
                        </td>
                
                    @endif
                
                    {{-- SECOND SEM --}}
                    @if($records['payroll']['semester'] == 'second_semester')
                
                        <td class="text-end">
                            {{ number_format($records['totals']['july_amount'], 2) }}
                        </td>
                
                        <td class="text-end">
                            {{ number_format($records['totals']['august_amount'], 2) }}
                        </td>
                
                        <td class="text-end">
                            {{ number_format($records['totals']['september_amount'], 2) }}
                        </td>
                
                        <td class="text-end">
                            {{ number_format($records['totals']['october_amount'], 2) }}
                        </td>
                
                        <td class="text-end">
                            {{ number_format($records['totals']['november_amount'], 2) }}
                        </td>
                
                        <td class="text-end">
                            {{ number_format($records['totals']['december_amount'], 2) }}
                        </td>
                
                    @endif
                
                    <td class="text-end">
                        {{ number_format($records['totals']['total_amount'], 2) }}
                    </td>
                
                    <td class="text-end text-warning">
                        {{ number_format($records['totals']['tax'], 2) }}
                    </td>
                
                    <td class="text-end text-info">
                        {{ number_format($records['totals']['net_amount'], 2) }}
                    </td>
                
                </tr>

            </tfoot>

        </table>

    </div>

</div>