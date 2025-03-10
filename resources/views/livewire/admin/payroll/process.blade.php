<div>
    
    @if(!$employment_type) 
        <div class="alert alert-info text-uppercase fw-bold text-center mt-5">No selected employment type for payroll</div>
        <div class="modal fade" id="chooseEmploymentType" tabindex="-1" aria-labelledby="chooseEmploymentTypeLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-uppercase fw-bold" id="chooseEmploymentTypeLabel">Choose Employment Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="mb-3">
                                <label for="employmentType" class="form-label">Employment Type</label>
                                <select class="form-select" wire:model="employment_type" id="employmentType" name="employment_type">
                                    <option value="" selected> - CHOOSE - </option>
                                    <option value="1">Regular Contractual</option>
                                    <option value="2">Contract of Service</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button wire:click="save" class="btn btn-primary text-uppercase fw-bold px-4 py-3">Continue</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(function() {
                $('#chooseEmploymentType').modal('show');
            });
        </script>
    @endif

    @if ($records)
        @if($employment_type == 1) 
            <div class="table-responsive">
                <table class="">
                    <thead>
                        <tr>
                            <th rowspan="2">No.</th>
                            <th rowspan="2" class="text-center sticky-top">Name</th>
                            <th rowspan="2" class="text-center sticky-top">Position</th>
                            <th rowspan="2" class="text-center sticky-top">Basic Salary</th>
                            <th rowspan="2" class="text-center sticky-top">Pera</th>
                            <th rowspan="2" class="text-center sticky-top">Gross Amount Earned</th>
                            <th colspan="30" class="text-center sticky-top">DEDUCTIONS: (GSIS, MPL, PHILHEALTH, AUT, and W/TAX)</th>
                            <th colspan="8" class="text-center sticky-top"></th>
                            <th colspan="4" class="text-center sticky-top">Salary Breakdown</th>
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
                                <td>{{$record['pera']}}</td>
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

        @if($employment_type == 2)
        <div class="table-responsive">
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

    @endif



    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            font-size: 12px;
            padding: 10px;
            border: 1px solid #ddd;
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
