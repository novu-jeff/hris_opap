<div>
    @if ($records)
        <div class="d-flex justify-content-start">
            <div>
                <hr class="mt-0">
                <div class="text-uppercase fw-bold">
                    @if($isApproved)
                        <h2 class="text-success fw-bold text-uppercase text-center">Approved</h2>
                    @else
                        <h2 class="text-danger fw-bold text-uppercase text-center">Pending</h2>
                    @endif
                </div>
                <hr>
                <div class="text-uppercase fw-bold">
                    Payroll Date : <span class="ms-2">{{$records['payroll_date']}}</span>
                </div>
                <div class="text-uppercase fw-bold">
                    Cut-off Period : <span class="ms-2">{{$records['cutoff_period']}}</span>
                </div>
                <div class="text-uppercase fw-bold">
                    Type : <span class="ms-2">{{$records['employment_type']}}</span>
                </div>
                <div class="text-uppercase fw-bold">
                    No. of employees : <span class="ms-2">{{$records['no_employees']}}</span>
                </div>
                <hr class="py-1">
                <div class="text-uppercase fw-bold">
                    Net Amount : <span class="ms-2">{{$records['net_amount']}}</span>
                </div>
                <div class="text-uppercase fw-bold">
                    Salary Amount : <span class="ms-2">{{$records['salary_amount']}}</span>
                </div>
                <hr class="mb-2">
            </div>
        </div>

        @if($records['employment_type_id'] == '1') 
            <div class="table-responsive pb-3">
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2">No.</th>
                            <th rowspan="2" class="text-center sticky-top">Name</th>
                            <th rowspan="2" class="text-center sticky-top">Position</th>
                            <th rowspan="2" class="text-center sticky-top">Basic Salary</th>
                            <th rowspan="2" class="text-center sticky-top">Pera</th>
                            <th rowspan="2" class="text-center sticky-top">Overtime</th>
                            <th rowspan="2" class="text-center sticky-top">Gross Amount Earned</th>
                            <th colspan="30" class="text-center sticky-top">DEDUCTIONS: (GSIS, MPL, PHILHEALTH, AUT, and W/TAX)</th>
                            <th colspan="8" class="text-center sticky-top"></th>
                            <th colspan="4" class="text-center sticky-top"></th>
                        </tr>
                        <tr>
                            <th colspan="2" class="vertical-text sticky-top">RLIP</th>
                            <th colspan="2" class="vertical-text sticky-top">HDMF</th>
                            <th colspan="2" class="vertical-text sticky-top">PHIL HEALTH</th>
                            <th colspan="2" class="vertical-text sticky-top">CONSOLOAN</th>
                            <th colspan="2" class="vertical-text sticky-top">EMERGYLN</th>
                            <th colspan="2" class="vertical-text sticky-top">PLREG</th>
                            <th colspan="2" class="vertical-text sticky-top">MPL</th>
                            <th colspan="2" class="vertical-text sticky-top">CPL</th>
                            <th colspan="2" class="vertical-text sticky-top">MP2</th>
                            <th colspan="2" class="vertical-text sticky-top">MPL STLMS</th>
                            <th colspan="2" class="vertical-text sticky-top">CIR375, CIR449</th>
                            <th colspan="2" class="vertical-text sticky-top">W/TAX</th>
                            <th colspan="2" class="vertical-text sticky-top">UCA</th>
                            <th colspan="2" class="vertical-text sticky-top">Allowance</th>
                            <th colspan="2" class="vertical-text sticky-top">AUT's</th>
                            <th class="text-center sticky-top">TOTAL DED.</th>
                            <th class="text-center sticky-top">NET AMOUNT</th>
                            <th colspan="2" class="vertical-text sticky-top">DBP</th>
                            <th colspan="2" class="vertical-text sticky-top">KAWANI</th>
                            <th colspan="2" class="vertical-text sticky-top">LBP PAYROLL ACCOUNT</th>
                            <th colspan="2" class="text-center sticky-top">Salary <br> (cut-off)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records['payroll'] as $key => $record)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>{{$record['name']}}</td>
                                <td>{{$record['position']}}</td>
                                <td>{{$record['basic_salary']}}</td>
                                <td>{{$record['pera']}}</td>
                                <td>{{$record['overtime']}}</td>
                                <td>{{$record['gross_amount_earned']}}</td>
                                <td colspan="2">{{$record['rlip']}}</td>
                                <td colspan="2">{{$record['hdmf']}}</td>
                                <td colspan="2">{{$record['philhealth']}}</td>
                                <td colspan="2">{{$record['consoloan']}}</td>
                                <td colspan="2">{{$record['emergency_loan']}}</td>
                                <td colspan="2">{{$record['plreg']}}</td>
                                <td colspan="2">{{$record['mpl']}}</td>
                                <td colspan="2">{{$record['cpl']}}</td>
                                <td colspan="2">{{$record['mp2']}}</td>
                                <td colspan="2">{{$record['mplstlms']}}</td>
                                <td colspan="2">{{$record['cir375_cir449']}}</td>
                                <td colspan="2">{{$record['w_tax']}}</td>
                                <td colspan="2">{{$record['uca']}}</td>
                                <td colspan="2">{{$record['allowance']}}</td>
                                <td colspan="2">{{$record['aut']}}</td>
                                <td>{{$record['total_deductions']}}</td>
                                <td>{{$record['net_amount']}}</td>
                                <td colspan="2">{{$record['dbp']}}</td>
                                <td colspan="2">{{$record['kawani']}}</td>
                                <td colspan="2">{{$record['lbp_payroll_account']}}</td>
                                <td colspan="2">{{$record['salary']}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($records['employment_type_id'] == '2')
            <div class="table-responsive pb-3">
                <table class="">
                    <thead>
                        <tr>
                            <th rowspan="2">No.</th>
                            <th rowspan="2" class="text-center sticky-top">Name</th>
                            <th rowspan="2" class="text-center sticky-top">Position</th>
                            <th rowspan="2" class="text-center sticky-top">Basic Salary</th>
                            <th colspan="2" class="vertical-text sticky-top">HDMF</th>
                            <th colspan="2" class="vertical-text sticky-top">PHIL HEALTH</th>
                            <th colspan="2" class="vertical-text sticky-top">MP2</th>
                            <th colspan="2" class="vertical-text sticky-top">MPL STLMS</th>
                            <th colspan="2" class="vertical-text sticky-top">CIR449</th>
                            <th colspan="2" class="vertical-text sticky-top">Unliquidated CA</th>
                            <th colspan="2" class="vertical-text sticky-top">TAX</th>
                            <th class="text-center sticky-top">TOTAL DED.</th>
                            <th class="text-center sticky-top">NET AMOUNT</th>
                            <th colspan="2" class="vertical-text sticky-top">DBP BRANCH</th>
                            <th colspan="2" class="vertical-text sticky-top">KAWANI</th>
                            <th colspan="2" class="vertical-text sticky-top">LBP PAYROLL ACCOUNT</th>
                            <th colspan="2" class="text-center sticky-top">1st HALF</th>
                            <th colspan="2" class="text-center sticky-top">2nd HALF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $key => $record)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>{{$record['name']}}</td>
                                <td>{{$record['position']}}</td>
                                <td>{{$record['basic_salary']}}</td>
                                <td colspan="2">{{$record['hdmf']}}</td>
                                <td colspan="2">{{$record['philhealth']}}</td>
                                <td colspan="2">{{$record['mp2']}}</td>
                                <td colspan="2">{{$record['mplstlms']}}</td>
                                <td colspan="2">{{$record['cir375_cir449']}}</td>
                                <td colspan="2">{{$record['uca']}}</td>
                                <td colspan="2">{{$record['w_tax']}}</td>
                                <td>{{$record['total_deductions']}}</td>
                                <td>{{$record['net_amount']}}</td>
                                <td colspan="2">{{$record['dbp_branch']}}</td>
                                <td colspan="2">{{$record['kawani']}}</td>
                                <td colspan="2">{{$record['lbp_payroll_account']}}</td>
                                <td colspan="2">{{$record['first_half']}}</td>
                                <td colspan="2">{{$record['second_half']}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if(!$isApproved)
            <div class="d-flex justify-content-end mt-5">
                <button wire:click="save" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">Approve <i class="fa-solid fa-arrow-right ms-2"></i></button>
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
            background-color: #f8f8f8;
            font-weight: bold;
            text-align: center;
        }
        .vertical-text {
            background-color: #e0e0e0 !important;
            font-weight: bold;
            text-align: center !important;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: normal !important;
            word-wrap: break-word !important;
            font-size: 12px;
            height: 100px;
        }
    </style>
</div>
