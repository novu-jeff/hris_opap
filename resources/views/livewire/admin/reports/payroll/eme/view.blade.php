
@php
$types = explode(',', $records['payroll']['condition_employment_type']);
//dd($types);
@endphp




<div>
    <div class="action mb-4"></div>
    <hr class="mt-0">
    <div class="row mb-3 align-items-end">
    <!-- Filter by Salary Method -->
    <div class="col-md-4">
        <label class="fw-bold">Filter by Salary Method</label>
        <select wire:model.live="filterSalaryMethod" class="form-select">
            <option value="">All</option>
            <option value="cash">Cash</option>
            <option value="land bank atm">Land Bank ATM</option>
            <option value="hold">Hold</option>
            <option value="unhold">UnHold</option>
        </select>
    </div>

    <!-- Search by Name -->
    <div class="col-md-4">
        <label class="fw-bold">Search by Name</label>
        <input type="text" wire:model.live="searchName" class="form-control" placeholder="Enter employee name">
    </div>

    <!-- Export Button -->
    <div class="col-md-4 d-flex justify-content-start justify-content-md-end">
        <button wire:click="exportExcelEme" class="btn btn-success mt-2 mt-md-0">
            Export to Excel
        </button>
    </div>
    
</div>

   
    <hr>

   {{-- SUMMARY --}}
   <div class="row mb-4">

    <div class="col-md-4">
        <div class="info-row">
            <span class="info-label">No. of Employees:</span>
            <span class="info-value">
                {{ $records['payroll']['no_employees'] }}
            </span>
        </div>

        <div class="info-row">
            <span class="info-label">Payroll Date:</span>
            <span class="info-value">
                {{ $records['payroll']['formatted_payroll_date'] }}
            </span>
        </div>

        <div class="info-row">
            <span class="info-label">Employee Type:</span>
            <span class="info-value">
                {{ $records['payroll']['formatted_employment_type'] }}
            </span>
        </div>
    </div>

    <div class="col-md-4">

        <div class="info-row">
            <span class="info-label">EME Total:</span>
            <span class="info-value">
                PHP {{ number_format($records['totals']['eme'], 2) }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Net Amount:</span>
            <span class="info-value fw-bold text-success">
                PHP {{ number_format($records['totals']['net_amount'], 2) }}
            </span>
        </div>
    </div>

    

</div>

<hr class="pt-2">

{{-- TABLE --}}
<div class="table-responsive overflow-auto">

    <table class="table table-bordered table-striped table-hover">

        <thead>
            <tr class="bg-primary text-white sticky-top">
                <th>Name</th>
                <th>Position</th>
                <th class="text-end">EME</th>
                <th class="text-end">Net Amount</th>
            </tr>
        </thead>

        <tbody>

            @forelse($records['payroll_items'] as $sectionGroup)

                {{-- SECTION HEADER --}}
                <tr class="section-row">
                    <td colspan="7" class="fw-bold bg-light">
                        {{ strtoupper($sectionGroup['section_name'] ?? 'UNKNOWN SECTION') }}
                    </td>
                </tr>

                @foreach($sectionGroup['employees'] as $item)

                    <tr>

                        <td class="fw-semibold">
                            {!! $this->highlightSearchTerm($item['name']) !!}
                        </td>

                        <td>
                            {{ $item['position'] }}
                        </td>

                        <td class="text-end">
                            {{ number_format($item['eme'], 2) }}
                        </td>

                        <td class="text-end fw-bold text-success">
                            {{ number_format($item['net_amount'], 2) }}
                        </td>

                    </tr>

                @endforeach

                {{-- SECTION TOTAL --}}
                @if(isset($sectionGroup['section_totals']))
                    <tr class="fw-bold bg-light">

                        <td colspan="2" class="text-end">
                            SECTION TOTAL
                        </td>

                        <td class="text-end">
                            {{ number_format($sectionGroup['section_totals']['eme'], 2) }}
                        </td>

                        <td class="text-end text-success">
                            {{ number_format($sectionGroup['section_totals']['net_amount'], 2) }}
                        </td>

                    </tr>
                @endif

            @empty

                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No EME payroll data found.
                    </td>
                </tr>

            @endforelse

        </tbody>

        {{-- GRAND TOTAL --}}
        <tfoot>
            <tr class="fw-bold bg-dark text-white">

                <td colspan="2" class="text-end">
                    GRAND TOTAL
                </td>

                <td class="text-end">
                    {{ number_format($records['totals']['eme'], 2) }}
                </td>

                <td class="text-end text-success">
                    {{ number_format($records['totals']['net_amount'], 2) }}
                </td>

            </tr>
        </tfoot>

    </table>

</div>

</div>


