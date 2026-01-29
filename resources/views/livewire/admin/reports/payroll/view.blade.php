<div>
    <div class="action mb-4"></div>
    <hr class="mt-0">
    <div class="row mb-3">
    <div class="col-md-4">
    <label class="fw-bold">Filter by Salary Method</label>
        <select wire:model.live="filterSalaryMethod" class="form-select">
            <option value="">All</option>
            <option value="cash">Cash</option>
            <option value="land bank atm">Land Bank ATM</option>
        </select>
    </div>

    <div class="col-md-4">
        <button wire:click="exportExcel" class="btn btn-success mt-4">
            Export to Excel
        </button>
    </div>
</div>

   
    <hr>

    <div class="row mb-3">
        <div class="col-12 col-md-6">
             <div class="fw-bold">No. of employees: <span class="ms-2">{{ $records['payroll']['no_employees'] }}</span></div>
            <div class="fw-bold">Payroll Period: <span class="ms-2">{{ $records['payroll']['formatted_payroll_date'] }}</span></div>
            <div class="fw-bold">Cut-off Period: <span class="ms-2">{{ $records['payroll']['formatted_cutoff_period'] }}</span></div>
            <div class="fw-bold">Employee Type: <span class="ms-2">{{ $records['payroll']['formatted_employment_type'] }}</span></div>
        </div>
        <div class="col-12 col-md-6">
           <div class="fw-bold">Basic Amount: <span class="ms-2">PHP {{ number_format($records['totals']['basic_salary'], 2) }}</span></div>
           <div class="fw-bold">Gross Amount: <span class="ms-2">PHP {{ number_format($records['totals']['gross'], 2) }}</span></div>
           <div class="fw-bold">Total Deductions: <span class="ms-2">PHP {{ number_format($records['totals']['total_deductions'], 2) }}</span></div>
            <div class="fw-bold">Net Amount: <span class="ms-2">PHP {{ number_format($records['payroll']['overall_net_amount'], 2) }}</span></div>
            <div class="fw-bold">Net lbp_payroll_account: <span class="ms-2">PHP {{ number_format($records['totals']['lbp_payroll_account'], 2) }}</span></div>
          
        @php
            $cutoff = $records['payroll']['formatted_cutoff_period'];
            $startDate = \Carbon\Carbon::parse(explode(' to ', $cutoff)[0]);
            $dayStart = $startDate->day;
            $isFirstHalf = $dayStart <= 15;
        @endphp
        @if($isFirstHalf)
            <div class="fw-bold">FirstHalf Amount: <span class="ms-2">PHP {{ number_format($records['totals']['net_first_half'], 2) }}</span></div>
        @else
            <div class="fw-bold">SecondHalf Amount: <span class="ms-2">PHP {{ number_format($records['totals']['net_second_half'], 2) }}</span></div>
        @endif
            
            
        </div>
    </div>

    <hr class="pt-3">

    <div class="table-responsive overflow-auto" style="max-width: 100%;">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr class="bg-primary text-white sticky-top">
                    <th>No.</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Salary Method</th> 
                    <th>Basic Salary</th>
                    <th>Pera</th>
                    <th>Gross</th>
                    <th>Rlip</th>
                    <th>HDMF</th>
                    <th>Philhealth</th>
                    <th>Consoloan</th>
                    <th>mergency Loan</th>
                    <th>Plreg</th>
                    <th>MPL</th>
                    <th>MPL lite</th>
                    <th>CPL</th>
                    <th>MP2</th>
                    <th>MPLSTLMS</th>
                    <th>Cir375, Cir449</th>
                    <th>Tax</th>
                    <th>UCA</th>
                    <th>AUT</th>
                    <th>Total Deductions</th>
                    <th>Net Amount</th>
                    <th>DBP</th>
                    <th>KAWANI</th>
                    <th>LBP Payroll Account</th>
                    <th>First Half</th>
                    <th>Second Half</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records['payroll_items'] as $sectionIndex => $sectionGroup)
                    <tr class="fw-bold bg-secondary text-white sticky-top">
                        <td colspan="29">{{ $sectionGroup['section_name'] ?? 'Unknown Section' }}</td>
                    </tr>

                    @foreach($sectionGroup['employees'] as $employeeIndex => $item)
                        @php
                            // Determine which half is considered first/second based on payroll cutoff
                            $cutoff = $records['payroll']['formatted_cutoff_period'];
                            $dayStart = intval(explode(' to ', $cutoff)[0]);
                            $isFirstHalf = $dayStart <= 15;
                        @endphp
                        <tr>
                            <td>{{ $employeeIndex + 1 }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['position'] }}</td>
                            <td>{{ $item['salary_method'] ?? 'N/A' }}</td> <!-- NEW -->
                            <td>{{ number_format($item['basic_salary'], 2) }}</td>
                            <td>{{ number_format($item['pera'], 2) }}</td>
                            <td>{{ number_format($item['gross_amount_earned'], 2) }}</td>
                            <td>{{ number_format($item['rlip'], 2) }}</td>
                            <td>{{ number_format($item['hdmf'], 2) }}</td>
                            <td>{{ number_format($item['philhealth'], 2) }}</td>
                            <td>{{ number_format($item['consoloan'], 2) }}</td>
                            <td>{{ number_format($item['emergency_loan'], 2) }}</td>
                            <td>{{ number_format($item['plreg'], 2) }}</td>
                            <td>{{ number_format($item['mpl'], 2) }}</td>
                            <td>{{ number_format($item['mpl_lite'], 2) }}</td>
                            <td>{{ number_format($item['cpl'], 2) }}</td>
                            <td>{{ number_format($item['mp2'], 2) }}</td>
                            <td>{{ number_format($item['mplstlms'], 2) }}</td>
                            <td>{{ number_format($item['cir375_cir449'], 2) }}</td>
                            <td>{{ number_format($item['w_tax'], 2) }}</td>
                            <td>{{ number_format($item['uca'], 2) }}</td>
                            <td>{{ number_format($item['aut'], 2) }}</td>
                            <td>{{ number_format($item['total_deductions'], 2) }}</td>
                            <td class="fw-bold text-success">{{ number_format($item['net_amount'], 2) }}</td>
                            <td>{{ number_format($item['dbp'], 2) }}</td>
                            <td>{{ number_format($item['kawani'], 2) }}</td>
                            <td class="fw-bold text-success">{{ number_format($item['lbp_payroll_account'], 2) }}</td>
                            <td>{{ number_format($item['net_first_half'], 2) }}</td>
                            <td>{{ number_format($item['net_second_half'], 2) }}</td>
                        </tr>
                    @endforeach
                    <tr >
                        <td colspan="29">{{ $sectionGroup['section_name'] ?? 'Unknown Section' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-muted text-center py-3">No payroll data found.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
            <tr class="table-total-row">
                <td colspan="3" class="text-end">TOTAL</td>
                <td></td> <!-- blank for salary method -->
                <td>{{ number_format($records['totals']['basic_salary'], 2) }}</td>
                <td>{{ number_format($records['totals']['pera'], 2) }}</td>
                <td>{{ number_format($records['totals']['gross'], 2) }}</td>
                <td>{{ number_format($records['totals']['rlip'], 2) }}</td>
                <td>{{ number_format($records['totals']['hdmf'], 2) }}</td>
                <td>{{ number_format($records['totals']['philhealth'], 2) }}</td>
                <td>{{ number_format($records['totals']['consoloan'], 2) }}</td>
                <td>{{ number_format($records['totals']['emergency_loan'], 2) }}</td>
                <td>{{ number_format($records['totals']['plreg'], 2) }}</td>
                <td>{{ number_format($records['totals']['mpl'], 2) }}</td>
                <td>{{ number_format($records['totals']['mpl_lite'], 2) }}</td>
                <td>{{ number_format($records['totals']['cpl'], 2) }}</td>
                <td>{{ number_format($records['totals']['mp2'], 2) }}</td>
                <td>{{ number_format($records['totals']['mplstlms'], 2) }}</td>
                <td>{{ number_format($records['totals']['cir375_cir449'], 2) }}</td>
                <td>{{ number_format($records['totals']['w_tax'], 2) }}</td>
                <td>{{ number_format($records['totals']['uca'], 2) }}</td>
                <td>{{ number_format($records['totals']['aut'], 2) }}</td>
                <td>{{ number_format($records['totals']['total_deductions'], 2) }}</td>
                <td class="text-success">
                    {{ number_format($records['totals']['net_amount'], 2) }}
                </td>
                <td>{{ number_format($records['totals']['dbp'], 2) }}</td>
                <td>{{ number_format($records['totals']['kawani'], 2) }}</td>
                <td class="text-success">{{ number_format($records['totals']['lbp_payroll_account'], 2) }}</td>
                <td>{{ number_format($records['totals']['net_first_half'], 2) }}</td>
                <td>{{ number_format($records['totals']['net_second_half'], 2) }}</td>
            </tr>
        </tfoot>

        </table>
    </div>
</div>
