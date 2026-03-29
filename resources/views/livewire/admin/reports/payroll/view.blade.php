
@php
$types = explode(',', $records['payroll']['condition_employment_type']);
//dd($types);
@endphp

@if(!in_array('2', $types) && !in_array('3', $types) && !in_array('4', $types))


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
        </select>
    </div>

    <!-- Search by Name -->
    <div class="col-md-4">
        <label class="fw-bold">Search by Name</label>
        <input type="text" wire:model.live="searchName" class="form-control" placeholder="Enter employee name">
    </div>

    <!-- Export Button -->
    <div class="col-md-4 d-flex justify-content-start justify-content-md-end">
        <button wire:click="exportExcel" class="btn btn-success mt-2 mt-md-0">
            Export to Excel
        </button>
    </div>
    
</div>

   
    <hr>

    <div class="row mb-3">
        <div class="col-12 col-md-4">
    <div class="info-row">
        <span class="info-label">No. of employees:</span>
        <span class="info-value">{{ $records['payroll']['no_employees'] }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Cut-off Period:</span>
        <span class="info-value">{{ $records['payroll']['formatted_cutoff_period'] }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Employee Type:</span>
        <span class="info-value">{{ $records['payroll']['formatted_employment_type'] }}</span>
    </div>
</div>

<div class="col-12 col-md-4">
    <div class="info-row">
        <span class="info-label">Basic Amount:</span>
        <span class="info-value">PHP {{ number_format($records['totals']['basic_salary'], 2) }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Gross Amount:</span>
        <span class="info-value">PHP {{ number_format($records['totals']['gross'], 2) }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Total Deductions:</span>
        <span class="info-value">PHP {{ number_format($records['totals']['total_deductions'], 2) }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Net Amount:</span>
        <span class="info-value">PHP {{ number_format($records['payroll']['overall_net_amount'], 2) }}</span>
    </div>
</div>

<div class="col-12 col-md-4">
    <div class="info-row">
        <span class="info-label-3">Net LBP Payroll Account</span>
        <span class="info-value">PHP {{ number_format($records['totals']['lbp_payroll_account'], 2) }}</span>
    </div>

    <div class="info-row">
        <span class="info-label-3">First Half Amount:</span>
        <span class="info-value">PHP {{ number_format($records['totals']['net_first_half'], 2) }}</span>
    </div>

    <div class="info-row">
        <span class="info-label-3">Second Half Amount:</span>
        <span class="info-value">PHP {{ number_format($records['totals']['net_second_half'], 2) }}</span>
    </div>

   
</div>

    </div>

    <hr class="pt-3">


    <div class="table-responsive overflow-auto" style="max-width: 100%;">
        
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr class="bg-primary text-white sticky-top">
                    <th rowspan="2" class="sticky-col text-start">Name</th>
                    <th rowspan="2" class="sticky-col-2 text-start">Position</th>
                    <th rowspan="2">Method</th>
    
                    <th colspan="3">EARNINGS</th>
                    <th colspan="14">DEDUCTIONS</th>
                    <th colspan="2">NET</th>
                    <th colspan="5">DISTRIBUTION</th>
                </tr>
    
                <tr class="sub-header text-center">
    
                    <!-- Earnings -->
                    <th title="Basic Salary">Basic</th>
                    <th title="Personnel Economic Relief Allowance">PERA</th>
                    <th title="Gross Earnings">Gross</th>
    
                    <!-- Deductions -->
                    <th title="Retirement Life Insurance Premium">RLIP</th>
                    <th title="Pag-IBIG Contribution">HDMF</th>
                    <th title="PhilHealth">PHIC</th>
                    <th>Conso</th>
                    <th>Emerg</th>
                    <th>PLReg</th>
                    <th>MPL</th>
                    <th>MPL Lite</th>
                    <th>CPL</th>
                    <th>MP2</th>
                    <th>MPL STL</th>
                    <th>CIR</th>
                    <th>Tax</th>
                    <th>UCA</th>
                    <th>DISALLOWANCE</th>
                    <th>AUT</th>
    
                    <!-- Net -->
                    <th>Total Ded</th>
                    <th>Net</th>
    
                    <!-- Distribution -->
                    <th>DBP</th>
                    <th>Kawani</th>
                    <th>LBP</th>
                    <th>1st</th>
                    <th>2nd</th>
                </tr>
            </thead>
    
            <tbody>
            @forelse($records['payroll_items'] as $sectionGroup)
    
                <!-- SECTION HEADER -->
                <tr class="section-row">
                    <td colspan="29">
                        {{ strtoupper($sectionGroup['section_name'] ?? 'UNKNOWN SECTION') }}
                    </td>
                </tr>
    
                @foreach($sectionGroup['employees'] as $item)
                <tr>
    
                    <!-- Sticky Columns -->
                    <td class="sticky-col fw-semibold">
                        {!! $this->highlightSearchTerm($item['name']) !!}
                    </td>
    
                    <td class="sticky-col-2">
                        {{ $item['position'] }}
                    </td>
    
                    <td class="text-center small">
                        {{ $item['salary_method'] ?? 'N/A' }}
                    </td>
    
                    <!-- Earnings -->
                    <td class="text-end">{{ number_format($item['basic_salary'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['pera'], 2) }}</td>
                    <td class="text-end fw-semibold">
                        {{ number_format($item['gross_amount_earned'], 2) }}
                    </td>
    
                    <!-- Deductions -->
                    <td class="text-end">{{ number_format($item['rlip'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['hdmf'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['philhealth'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['consoloan'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['emergency_loan'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['plreg'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['mpl'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['mpl_lite'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['cpl'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['mp2'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['mplstlms'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['cir375_cir449'], 2) }}</td>
                    <td class="text-end text-danger fw-semibold">
                        {{ number_format($item['w_tax'], 2) }}
                    </td>
                    <td class="text-end">{{ number_format($item['uca'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['disallowance'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['aut'], 2) }}</td>
    
                    <!-- Net -->
                    <td class="text-end fw-semibold">
                        {{ number_format($item['total_deductions'], 2) }}
                    </td>
    
                    <td class="text-end fw-bold text-success">
                        {{ number_format($item['net_amount'], 2) }}
                    </td>
    
                    <!-- Distribution -->
                    <td class="text-end">{{ number_format($item['dbp'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['kawani'], 2) }}</td>
                    <td class="text-end fw-bold text-primary">
                        {{ number_format($item['lbp_payroll_account'], 2) }}
                    </td>
                    <td class="text-end">{{ number_format($item['net_first_half'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['net_second_half'], 2) }}</td>
    
                </tr>
                @endforeach
    
                <!-- SECTION TOTAL -->
                @if(isset($sectionGroup['section_totals']))
                <tr class="section-total fw-bold bg-light">
                    <td colspan="3" class="text-end">SECTION TOTAL</td>
                
                    <!-- Earnings -->
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['basic_salary'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['pera'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['gross'], 2) }}</td>
                
                    <!-- Deductions -->
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['rlip'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['hdmf'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['philhealth'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['consoloan'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['emergency_loan'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['plreg'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['mpl'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['mpl_lite'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['cpl'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['mp2'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['mplstlms'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['cir375_cir449'], 2) }}</td>
                    <td class="text-end text-danger">{{ number_format($sectionGroup['section_totals']['w_tax'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['uca'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['disallowance'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['aut'], 2) }}</td>
                
                    <!-- Net -->
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['total_deductions'], 2) }}</td>
                    <td class="text-end text-success">{{ number_format($sectionGroup['section_totals']['net_amount'], 2) }}</td>
                
                    <!-- Distribution -->
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['dbp'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['kawani'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['lbp_payroll_account'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['net_first_half'], 2) }}</td>
                    <td class="text-end">{{ number_format($sectionGroup['section_totals']['net_second_half'], 2) }}</td>
                </tr>
                @endif
    
            @empty
                <tr>
                    <td colspan="29" class="text-center text-muted py-4">
                        No payroll data found.
                    </td>
                </tr>
            @endforelse
            </tbody>
    
            <!-- GRAND TOTAL -->
            <tfoot>
                <tr class="grand-total fw-bold bg-dark text-white">
                    <td colspan="3" class="text-end">GRAND TOTAL</td>
                
                    <!-- Earnings -->
                    <td class="text-end">{{ number_format($records['totals']['basic_salary'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['pera'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['gross'], 2) }}</td>
                
                    <!-- Deductions -->
                    <td class="text-end">{{ number_format($records['totals']['rlip'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['hdmf'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['philhealth'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['consoloan'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['emergency_loan'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['plreg'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['mpl'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['mpl_lite'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['cpl'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['mp2'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['mplstlms'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['cir375_cir449'], 2) }}</td>
                    <td class="text-end text-danger">{{ number_format($records['totals']['w_tax'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['uca'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['disallowance'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['aut'], 2) }}</td>
                
                    <!-- Net -->
                    <td class="text-end">{{ number_format($records['totals']['total_deductions'], 2) }}</td>
                    <td class="text-end text-success">{{ number_format($records['totals']['net_amount'], 2) }}</td>
                
                    <!-- Distribution -->
                    <td class="text-end">{{ number_format($records['totals']['dbp'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['kawani'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['lbp_payroll_account'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['net_first_half'], 2) }}</td>
                    <td class="text-end">{{ number_format($records['totals']['net_second_half'], 2) }}</td>
                </tr>
                </tfoot>
    
        </table>
    </div>
</div>


@else


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
        </select>
    </div>

    <!-- Search by Name -->
    <div class="col-md-4">
        <label class="fw-bold">Search by Name</label>
        <input type="text" wire:model.live="searchName" class="form-control" placeholder="Enter employee name">
    </div>

    <!-- Export Button -->
    <div class="col-md-4 d-flex justify-content-start justify-content-md-end">
        <button wire:click="exportExcelCosJo" class="btn btn-success mt-2 mt-md-0">
            Export to Excel
        </button>
    </div>
</div>

   
    <hr>

    <div class="row mb-3">
        <div class="col-12 col-md-4">
    <div class="info-row">
        <span class="info-label">No. of employees:</span>
        <span class="info-value">{{ $records['payroll']['no_employees'] }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Cut-off Period:</span>
        <span class="info-value">{{ $records['payroll']['formatted_cutoff_period'] }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Employee Type:</span>
        <span class="info-value">{{ $records['payroll']['formatted_employment_type'] }}</span>
    </div>
</div>

<div class="col-12 col-md-4">
    <div class="info-row">
        <span class="info-label">Basic Amount:</span>
        <span class="info-value">PHP {{ number_format($records['totals']['basic_salary'], 2) }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Gross Amount:</span>
        <span class="info-value">PHP {{ number_format($records['totals']['gross'], 2) }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Total Deductions:</span>
        <span class="info-value">PHP {{ number_format($records['totals']['total_deductions'], 2) }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Net Amount:</span>
        <span class="info-value">PHP {{ number_format($records['payroll']['overall_net_amount'], 2) }}</span>
    </div>
</div>

<div class="col-12 col-md-4">
    <div class="info-row">
        <span class="info-label-3">Net LBP Payroll Account</span>
        <span class="info-value">PHP {{ number_format($records['totals']['lbp_payroll_account'], 2) }}</span>
    </div>

    <div class="info-row">
        <span class="info-label-3">First Half Amount:</span>
        <span class="info-value">PHP {{ number_format($records['totals']['net_first_half'], 2) }}</span>
    </div>

    <div class="info-row">
        <span class="info-label-3">Second Half Amount:</span>
        <span class="info-value">PHP {{ number_format($records['totals']['net_second_half'], 2) }}</span>
    </div>

   
</div>

    </div>

    <hr class="pt-3">


    <div class="table-responsive overflow-auto" style="max-width: 100%;">
        
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr class="bg-primary text-white sticky-top">
                    <th rowspan="2" class="sticky-col text-start">Name</th>
                    <th rowspan="2" class="sticky-col-2 text-start">Position</th>
                    <th rowspan="2">Method</th>
    
                    <th colspan="1">EARNINGS</th>
                    <th colspan="9">DEDUCTIONS</th>
                    <th colspan="4">TAX BREAKDOWN</th>
                    <th colspan="2">NET</th>
                    <th colspan="5">DISTRIBUTION</th>
                </tr>
    
                <tr class="sub-header text-center">
    
                    <!-- Earnings -->
                    <th>Basic</th>
    
                    <!-- Deductions -->
                    <th>HDMF</th>
                    <th>PHIC</th>
                    <th>MPL</th>
                    <th>MP2</th>
                    <th>MPL STL</th>
                    <th>CIR</th>
                    <th>UCA</th>
                    <th>AUT</th>
                    <th>Overpay</th>
    
                    <!-- Tax -->
                    <th>3%</th>
                    <th>5%</th>
                    <th>8%</th>
                    <th>10%</th>
    
                    <!-- Net -->
                    <th>Total Ded</th>
                    <th>Net</th>
    
                    <!-- Distribution -->
                    <th>DBP</th>
                    <th>Kawani</th>
                    <th>LBP</th>
                    <th>1st</th>
                    <th>2nd</th>
                </tr>
            </thead>
    
            <tbody>
            @forelse($records['payroll_items'] as $sectionGroup)
    
                <!-- SECTION -->
                <tr class="section-row">
                    <td colspan="24">
                        {{ strtoupper($sectionGroup['section_name'] ?? 'UNKNOWN SECTION') }}
                    </td>
                </tr>
    
                @foreach($sectionGroup['employees'] as $item)
                <tr>
    
                    <td class="sticky-col fw-semibold">
                        {!! $this->highlightSearchTerm($item['name']) !!}
                    </td>
    
                    <td class="sticky-col-2">
                        {{ $item['position'] }}
                    </td>
    
                    <td class="text-center small">
                        {{ $item['salary_method'] ?? 'N/A' }}
                    </td>
    
                    <!-- Earnings -->
                    <td class="text-end">
                        {{ number_format($item['basic_salary'], 2) }}
                    </td>
    
                    <!-- Deductions -->
                    <td class="text-end">{{ number_format($item['hdmf'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['philhealth'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['mpl'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['mp2'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['mplstlms'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['cir375_cir449'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['uca'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['aut'], 2) }}</td>
                    <td class="text-end text-warning">
                        {{ number_format($item['overpayment'], 2) }}
                    </td>
    
                    <!-- TAX BREAKDOWN -->
                    <td class="text-end text-danger">{{ number_format($item['tax_3'], 2) }}</td>
                    <td class="text-end text-danger">{{ number_format($item['tax_5'], 2) }}</td>
                    <td class="text-end text-danger">{{ number_format($item['tax_8'], 2) }}</td>
                    <td class="text-end text-danger">{{ number_format($item['tax_10'], 2) }}</td>
                  
    
                    <!-- Net -->
                    <td class="text-end fw-semibold">
                        {{ number_format($item['total_deductions'], 2) }}
                    </td>
    
                    <td class="text-end fw-bold text-success">
                        {{ number_format($item['net_amount'], 2) }}
                    </td>
    
                    <!-- Distribution -->
                    <td class="text-end">{{ number_format($item['dbp'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['kawani'], 2) }}</td>
                    <td class="text-end fw-bold text-primary">
                        {{ number_format($item['lbp_payroll_account'], 2) }}
                    </td>
                    <td class="text-end">{{ number_format($item['net_first_half'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['net_second_half'], 2) }}</td>
    
                </tr>
                @endforeach
    
                <!-- SECTION TOTAL -->
                @if(isset($sectionGroup['section_totals']))
                <tr class="section-total">
                    <td colspan="3" class="text-end">SECTION TOTAL</td>
    
                    <td>{{ number_format($sectionGroup['section_totals']['basic_salary'], 2) }}</td>
    
                    <td colspan="8"></td>
    
                    <td>{{ number_format($sectionGroup['section_totals']['tax_3'], 2) }}</td>
                    <td>{{ number_format($sectionGroup['section_totals']['tax_5'], 2) }}</td>
                    <td>{{ number_format($sectionGroup['section_totals']['tax_8'], 2) }}</td>
                    <td>{{ number_format($sectionGroup['section_totals']['tax_10'], 2) }}</td>
    
                    <td>{{ number_format($sectionGroup['section_totals']['total_deductions'], 2) }}</td>
                    <td class="text-success">
                        {{ number_format($sectionGroup['section_totals']['net_amount'], 2) }}
                    </td>
    
                    <td colspan="5"></td>
                </tr>
                @endif
    
            @empty
                <tr>
                    <td colspan="24" class="text-center text-muted py-4">
                        No payroll data found.
                    </td>
                </tr>
            @endforelse
            </tbody>
    
            <!-- GRAND TOTAL -->
            <tfoot>
                <tr class="grand-total">
                    <td colspan="3" class="text-end">GRAND TOTAL</td>
    
                    <td>{{ number_format($records['totals']['basic_salary'], 2) }}</td>
    
                    <td colspan="8"></td>
                    <td>{{ number_format($records['totals']['tax_3'], 2) }}</td>
                    <td>{{ number_format($records['totals']['tax_5'], 2) }}</td>
                    <td>{{ number_format($records['totals']['tax_8'], 2) }}</td>
                    <td>{{ number_format($records['totals']['tax_10'], 2) }}</td>
    
                    <td>{{ number_format($records['totals']['total_deductions'], 2) }}</td>
                    <td class="text-success">
                        {{ number_format($records['totals']['net_amount'], 2) }}
                    </td>
    
                    <td colspan="5"></td>
                </tr>
            </tfoot>
    
        </table>
    </div>
</div>



@endif


