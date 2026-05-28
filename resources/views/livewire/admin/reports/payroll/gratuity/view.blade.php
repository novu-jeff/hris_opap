<div>

    <div class="action mb-4"></div>

    <hr class="mt-0">

    <!-- FILTERS -->
    <div class="row mb-3 align-items-end">

        <!-- Filter by Salary Method -->
        <div class="col-md-4">

            <label class="fw-bold">
                FILTER BY SALARY METHOD
            </label>

            <select
                wire:model.live="filterSalaryMethod"
                class="form-select"
            >

                <option value="">ALL</option>
                <option value="cash">CASH</option>
                <option value="land bank atm">
                    LAND BANK ATM
                </option>
                <option value="hold">
                    HOLD (with ATM)
                </option>
                <option value="unhold">
                    HOLD (No ATM)
                </option>

            </select>

        </div>

        <!-- Search -->
        <div class="col-md-4">

            <label class="fw-bold">
                SEARCH BY NAME
            </label>

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
                wire:click="exportExcelGratuity"
                class="btn btn-success mt-2 mt-md-0"
            >

                EXPORT TO EXCEL

            </button>

        </div>

    </div>

    <hr>

    <!-- SUMMARY -->
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

                    <!-- PAYROLL TYPE -->
                    <div class="col-md-6">
                        <small class="text-muted text-uppercase d-block">
                            Payroll Type
                        </small>

                        <div class="fw-bold fs-6 text-uppercase">
                            GRATUITY
                        </div>
                    </div>

                    <!-- STATUS -->
                    <div class="col-md-6">
                        <small class="text-muted text-uppercase d-block">
                            Status
                        </small>

                        <div class="fw-bold fs-6 text-uppercase">
                            {{ $records['payroll']['status'] ?? 'PENDING' }}
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
                    Gratuity Summary
                </h6>

                <div class="mb-4">

                    <small class="text-muted text-uppercase d-block">
                        Total Gratuity
                    </small>

                    <div class="fw-bold text-primary fs-3">
                        ₱{{ number_format($records['totals']['gratuity_pay'] ?? 0, 2) }}
                    </div>

                </div>

                <div class="d-flex justify-content-between mb-3">

                    <span class="text-muted text-uppercase small">
                        Total Tax
                    </span>

                    <span class="fw-bold text-danger">
                        ₱{{ number_format($records['totals']['tax'] ?? 0, 2) }}
                    </span>

                </div>

                <div class="d-flex justify-content-between mb-3">

                    <span class="text-muted text-uppercase small">
                        Net Amount
                    </span>

                    <span class="fw-bold text-success">
                        ₱{{ number_format($records['totals']['net_amount'] ?? 0, 2) }}
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

                    <th class="text-center">
                        DATE HIRED
                    </th>

                    <th class="text-end">
                        GRATUITY PAY
                    </th>

                    <th class="text-end">
                        TAX (5%)
                    </th>

                    <th class="text-end">
                        NET AMOUNT
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($records['payroll_items'] as $sectionGroup)

                    <!-- SECTION -->
                    <tr class="section-row">

                        <td
                            colspan="6"
                            class="fw-bold bg-light"
                        >

                            {{ strtoupper(
                                $sectionGroup['section_name']
                                ?? 'UNKNOWN SECTION'
                            ) }}

                        </td>

                    </tr>

                    @foreach($sectionGroup['employees'] as $item)

                        <tr>

                            <!-- NAME -->
                            <td class="fw-semibold">

                                {!! $this->highlightSearchTerm(
                                    strtoupper($item['name'])
                                ) !!}

                            </td>

                            <!-- POSITION -->
                            <td>

                                {{ strtoupper(
                                    $item['position']
                                ) }}

                            </td>

                            <!-- DATE HIRED -->
                            <td class="text-center">

                                {{ !empty($item['date_hired'])

                                    ? \Carbon\Carbon::parse(
                                        $item['date_hired']
                                    )->format('M d, Y')

                                    : 'N/A'
                                }}

                            </td>

                            <!-- GRATUITY -->
                            <td class="text-end">

                                {{ number_format(
                                    $item['gratuity_pay'] ?? 0,
                                    2
                                ) }}

                            </td>

                            <!-- TAX -->
                            <td class="text-end text-danger">

                                {{ number_format(
                                    $item['tax'] ?? 0,
                                    2
                                ) }}

                            </td>

                            <!-- NET -->
                            <td class="text-end fw-bold text-success">

                                {{ number_format(
                                    $item['net_amount'] ?? 0,
                                    2
                                ) }}

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

                                {{ number_format(
                                    $sectionGroup['section_totals']['gratuity_pay'] ?? 0,
                                    2
                                ) }}

                            </td>

                            <td class="text-end text-danger">

                                {{ number_format(
                                    $sectionGroup['section_totals']['tax'] ?? 0,
                                    2
                                ) }}

                            </td>

                            <td class="text-end text-success">

                                {{ number_format(
                                    $sectionGroup['section_totals']['net_amount'] ?? 0,
                                    2
                                ) }}

                            </td>

                        </tr>

                    @endif

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="text-center text-muted py-4"
                        >

                            NO GRATUITY PAYROLL DATA FOUND.

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

                        {{ number_format(
                            $records['totals']['gratuity_pay'] ?? 0,
                            2
                        ) }}

                    </td>

                    <td class="text-end text-danger">

                        {{ number_format(
                            $records['totals']['tax'] ?? 0,
                            2
                        ) }}

                    </td>

                    <td class="text-end text-success">

                        {{ number_format(
                            $records['totals']['net_amount'] ?? 0,
                            2
                        ) }}

                    </td>

                </tr>

            </tfoot>

        </table>

    </div>

</div>