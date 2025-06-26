<div>
    @if ($records && !$error)
        <hr class="mt-0">
        <div class="text-uppercase fw-bold">
            @if($isApproved)
                <h2 class="text-success fw-bold text-uppercase text-center">Approved</h2>
            @else
                <h2 class="text-danger fw-bold text-uppercase text-center">Pending</h2>
            @endif
        </div>
        <hr>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="text-uppercase fw-bold">
                    Payroll Date : <span class="ms-2">{{$records['payroll']['formatted_payroll_date']}}</span>
                </div>
                <div class="text-uppercase fw-bold">
                    Cut-off Period : <span class="ms-2">{{$records['payroll']['formatted_cutoff_period']}}</span>
                </div>
                <div class="text-uppercase fw-bold">
                    Type : <span class="ms-2">{{$records['payroll']['formatted_employment_type']}}</span>
                </div>
                <div class="text-uppercase fw-bold">
                    No. of employees : <span class="ms-2">{{$records['payroll']['no_employees']}}</span>
                </div>
                <hr class="py-1">
                <div class="text-uppercase fw-bold">
                    Net Amount : <span class="ms-2">PHP {{number_format($records['payroll']['overall_net_amount'], 2)}}</span>
                </div>
                <div class="text-uppercase fw-bold">
                    Salary Amount : <span class="ms-2">PHP {{number_format($records['payroll']['overall_salary_amount'], 2)}}</span>
                </div>
                <hr class="mb-2">
            </div>
            <div class="col-12 col-md-6">
                <h4 class="text-uppercase fw-bold">Other Earnings</h4>
                <ul class="list-unstyled">
                    <li class="d-flex align-items-center gap-2 text-uppercase">
                        <input type="checkbox" name="clothingAllowance" id="clothingAllowance" wire:model="isClothingAllowance">
                        Clothing Allowance
                    </li>
                </ul>
            </div>
        </div>

        @if($records['payroll']['employment_type'] == '1')
            <div class="table-responsive pb-3">
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2" class="vertical-text text-dark">Status</th>
                            <th rowspan="2">No.</th>
                            <th rowspan="2" class="text-center">Name</th>
                            <th rowspan="2" class="text-center">Position</th>
                            <th rowspan="2" class="text-center">Basic Salary</th>
                            <th rowspan="2" class="text-center">Pera</th>
                            <th rowspan="2" class="text-center">Gross Amount Earned</th>
                            <th colspan="26" class="text-center">DEDUCTIONS: (GSIS, MPL, PHILHEALTH, AUT, and W/TAX)</th>
                            <th colspan="2" class="text-center">AUT</th>
                            <th colspan="8" class="text-center"></th>
                            <th colspan="10" class="text-center">Salary</th> 
                        </tr>
                        <tr>
                            <th colspan="2" class="vertical-text green">RLIP</th>
                            <th colspan="2" class="vertical-text yellow">HDMF</th>
                            <th colspan="2" class="vertical-text skyblue">PHIL HEALTH</th>
                            <th colspan="2" class="vertical-text green">CONSOLOAN</th>
                            <th colspan="2" class="vertical-text green">EMERGYLN</th>
                            <th colspan="2" class="vertical-text green">PLREG</th>
                            <th colspan="2" class="vertical-text green">MPL</th>
                            <th colspan="2" class="vertical-text green">CPL</th>
                            <th colspan="2" class="vertical-text yellow">MP2</th>
                            <th colspan="2" class="vertical-text yellow">MPL STLMS</th>
                            <th colspan="2" class="vertical-text yellow">CIR375, CIR449</th>
                            <th colspan="2" class="vertical-text red">W/TAX</th>
                            <th colspan="2" class="vertical-text red">UCA</th>
                            <th class="vertical-text grey">1st Half</th>
                            <th class="vertical-text grey">2nd Half</th>

                            <th class="text-center">TOTAL DED.</th>
                            <th class="text-center">NET AMOUNT</th>
                            <th colspan="2" class="vertical-text grey">DBP</th>
                            <th colspan="2" class="vertical-text grey">KAWANI</th>
                            <th colspan="2" class="vertical-text grey">LBP PAYROLL ACCOUNT</th>
                            <th colspan="2" class="text-center">First Half</th>
                            <th colspan="2" class="text-center">Second Half</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($records['payroll_items'] as $sectionIndex => $sectionGroup)
                            <tr class="fw-bold bg-primary text-white sticky-top" style="top: 55px; z-index: 9;">
                                <td colspan="100%">
                                    <div class="d-flex justify-content-between w-100 px-5">
                                        <span>{{ $sectionGroup['section_name'] ?? 'Unknown Section' }}</span>
                                        <span class="text-center flex-grow-1">{{ $sectionGroup['section_name'] ?? 'Unknown Section' }}</span>
                                        <span>{{ $sectionGroup['section_name'] ?? 'Unknown Section' }}</span>
                                    </div>
                                </td>
                            </tr>

                            @foreach($sectionGroup['employees'] as $employeeIndex => $record)
                                <tr>
                                    <td>
                                        <div class="marked-changed">
                                            @if(in_array($record['id'], $updatedItems))
                                                <i class="fa-solid fa-triangle-exclamation unsaved" title="Unsaved changes"></i>
                                            @else
                                                <i class="fa-solid fa-check ready" title="No changes made"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        #{{ $employeeIndex + 1 }}
                                    </td>
                                    <td>
                                        <a href="{{ route('hris.show', ['employee_no' => $record['employee_no']]) }}"
                                        class="text-dark" target="_blank">
                                            {{ $record['name'] }}
                                        </a>
                                    </td>
                                    <td>{{ $record['position'] }}</td>
                                    <td>{{ number_format($record['basic_salary'], 2) }}</td>
                                    <td>{{ number_format($record['pera'], 2) }}</td>
                                    <td>{{ number_format($record['gross_amount_earned'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['rlip'], 2) }}</td>

                                    {{-- HDMF --}}
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="hdmf.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control" style="width: 120px;">
                                    </td>

                                    <td colspan="2">{{ number_format($record['philhealth'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['consoloan'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['emergency_loan'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['plreg'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['mpl'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['cpl'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['mp2'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['mplstlms'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['cir375_cir449'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['w_tax'], 2) }}</td>

                                    {{-- UCA --}}
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="uca.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control" style="width: 120px;">
                                    </td>

                                    <td>{{ $record['aut'] }}</td>
                                    <td>{{ $record['aut'] }}</td>

                                    <td>{{ number_format($record['total_deductions'], 2) }}</td>
                                    <td>{{ number_format($record['net_amount'], 2) }}</td>

                                    {{-- DBP --}}
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="dbp.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control" style="width: 120px;">
                                    </td>

                                    {{-- Kawani --}}
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="kawani.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control" style="width: 120px;">
                                    </td>

                                    <td colspan="2">{{ number_format($record['lbp_payroll_account'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['salary'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['salary'], 2) }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($records['payroll']['employment_type'] == '2')
            <div class="table-responsive pb-3">
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2">No.</th>
                            <th rowspan="2" class="text-center">Name</th>
                            <th rowspan="2" class="text-center">Position</th>
                            <th rowspan="2" class="text-center">Basic Salary</th>
                            <th rowspan="2" class="text-center">Pera</th>
                            <th rowspan="2" class="text-center">Gross Amount Earned</th>
                            <th colspan="26" class="text-center">DEDUCTIONS: (GSIS, MPL, PHILHEALTH, AUT, and W/TAX)</th>
                            <th colspan="2" class="text-center">AUT</th>
                            <th colspan="8" class="text-center"></th>
                            <th colspan="10" class="text-center">Salary</th> 
                        </tr>
                        <tr>
                            <th colspan="2" class="vertical-text green">RLIP</th>
                            <th colspan="2" class="vertical-text yellow">HDMF</th>
                            <th colspan="2" class="vertical-text skyblue">PHIL HEALTH</th>
                            <th colspan="2" class="vertical-text green">CONSOLOAN</th>
                            <th colspan="2" class="vertical-text green">EMERGYLN</th>
                            <th colspan="2" class="vertical-text green">PLREG</th>
                            <th colspan="2" class="vertical-text green">MPL</th>
                            <th colspan="2" class="vertical-text green">CPL</th>
                            <th colspan="2" class="vertical-text yellow">MP2</th>
                            <th colspan="2" class="vertical-text yellow">MPL STLMS</th>
                            <th colspan="2" class="vertical-text yellow">CIR375, CIR449</th>
                            <th colspan="2" class="vertical-text red">W/TAX</th>
                            <th colspan="2" class="vertical-text red">UCA</th>
                            <th class="vertical-text grey">1st Half</th>
                            <th class="vertical-text grey">2nd Half</th>

                            <th class="text-center">TOTAL DED.</th>
                            <th class="text-center">NET AMOUNT</th>
                            <th colspan="2" class="vertical-text grey">DBP</th>
                            <th colspan="2" class="vertical-text grey">KAWANI</th>
                            <th colspan="2" class="vertical-text grey">LBP PAYROLL ACCOUNT</th>
                            <th colspan="2" class="text-center">First Half</th>
                            <th colspan="2" class="text-center">Second Half</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($records['payroll_items'] as $sectionIndex => $sectionGroup)
                            <tr class="fw-bold bg-primary text-white sticky-top" style="top: 55px; z-index: 9;">
                                <td colspan="100%">{{ $sectionGroup['section_name'] ?? 'Unknown Section' }}</td>
                            </tr>

                            @foreach($sectionGroup['employees'] as $employeeIndex => $record)
                                <tr>
                                    <td>#{{ $employeeIndex + 1 }}</td>
                                    <td>
                                        <a href="{{ route('hris.show', ['employee_no' => $record['employee_no']]) }}"
                                        class="text-dark" target="_blank">
                                            {{ $record['name'] }}
                                        </a>
                                    </td>
                                    <td>{{ $record['position'] }}</td>
                                    <td>{{ number_format($record['basic_salary'], 2) }}</td>
                                    <td>{{ number_format($record['pera'], 2) }}</td>
                                    <td>{{ number_format($record['gross_amount_earned'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['rlip'], 2) }}</td>

                                    {{-- HDMF --}}
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="hdmf.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control" style="width: 120px;">
                                    </td>

                                    <td colspan="2">{{ number_format($record['philhealth'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['consoloan'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['emergency_loan'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['plreg'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['mpl'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['cpl'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['mp2'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['mplstlms'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['cir375_cir449'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['w_tax'], 2) }}</td>

                                    {{-- UCA --}}
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="uca.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control" style="width: 120px;">
                                    </td>

                                    <td>{{ $record['aut'] }}</td>
                                    <td>{{ $record['aut'] }}</td>

                                    <td>{{ number_format($record['total_deductions'], 2) }}</td>
                                    <td>{{ number_format($record['net_amount'], 2) }}</td>

                                    {{-- DBP --}}
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="dbp.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control" style="width: 120px;">
                                    </td>

                                    {{-- Kawani --}}
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="kawani.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control" style="width: 120px;">
                                    </td>

                                    <td colspan="2">{{ number_format($record['lbp_payroll_account'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['salary'], 2) }}</td>
                                    <td colspan="2">{{ number_format($record['salary'], 2) }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($hasChanges)
            <div class="d-flex justify-content-end mt-5">
                <button class="btn btn-primary px-5 py-3 text-uppercase" wire:loading.attr="disabled" wire:click="save">
                    <span wire:loading.remove wire:target="save">Save Changes</span>
                    <span wire:loading wire:target="save">
                        Saving <i class="fa-solid fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        @endif

        @if(!$isApproved && !$hasChanges)
            <div class="d-flex justify-content-end mt-5">
                <button class="btn btn-primary px-5 py-3 text-uppercase" wire:loading.attr="disabled" wire:click="approve">
                    <span wire:loading.remove wire:target="approve">Approve</span>
                    <span wire:loading wire:target="approve">
                        Please Wait <i class="fa-solid fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        @endif

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

    @else 
        <div class="alert alert-danger text-center text-uppercase fw-bold my-4">{{$error}}</div>
    @endif

    <style>
        
        table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
        }

        th, td {
            font-size: 12px;
            padding: 10px;
            border: 3px solid #ddd;
            padding: 8px 20px 8px 20px;
            vertical-align: middle;
            width: 300px !important;
        }

        th {
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
        }

        .vertical-text {
            color: #fff;
            font-weight: bold;
            text-align: center !important;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: normal !important;
            word-wrap: break-word !important;
            font-size: 12px;
            height: 120px;
            letter-spacing: 1px;
        }

        .marked-changed {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;

            i {
                font-size: 20px;
            }

            i.unsaved {
                color: #feb406;
            }

            i.ready {
                color:rgb(33, 179, 4);
            }
        }

    </style>
</div>
