<div>
    <div class="mt-5">

        @if($error)
            <div class="alert alert-danger text-center text-uppercase fw-bold my-4">{{$error}}</div>
        @else

            <div class="py-3 d-flex justify-content-between gap-3 align-items-center">
                <div class="d-flex gap-3">
                    <div class="d-flex align-items-center">
                        <button 
                            class="btn btn-sm btn-outline-primary" 
                            wire:click="changePeriod('control', '-1')"
                            wire:loading.attr="disabled" 
                            wire:loading.class="btn-secondary">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        
                        <div class="mx-3" id="cuttOffPeriod">
                            {{ \Carbon\Carbon::parse($payroll->payroll_date)->format('F d, Y') }}
                        </div>
                        
                        <button 
                            class="btn btn-sm btn-outline-primary" 
                            wire:click="changePeriod('control', '1')"
                            wire:loading.attr="disabled" 
                            wire:loading.class="btn-secondary">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>                
                    </div>
                </div>
                <button class="btn btn-success save-as-pdf"><i class="fa-solid fa-print"></i></button>
            </div>
            <div>
                <div class="payslip-container">
                    <div class="inner-content">
                        <div class="header d-flex justify-content-center gap-3 align-items-center text-center px-5">
                            <div class="logo">
                                <img src="{{asset('img/opapru-logo.png')}}" alt="" srcset="">    
                            </div>    
                            <div class="header-text">
                                Office of the Presidential Adviser on Peace, Reconciliation and Unity PAYROLL PAYMENT SLIP
                            </div>
                        </div>  
                        <div class="info">
                            <div class="d-flex align-items-start">
                                <div class="label">
                                    Pay Period:
                                </div>
                                <div class="value">
                                    {{$payslip['payroll_date']}}
                                </div>
                            </div>  
                            <div class="d-flex align-items-start">
                                <div class="label">
                                    Employee's Name:
                                </div>
                                <div class="value">
                                    {{$payslip['employee']['name']}}
                                </div>
                            </div>  
                            <div class="d-flex align-items-start">
                                <div class="label">
                                    Position:
                                </div>
                                <div class="value">
                                    {{$payslip['employee']['position']}}
                                </div>
                            </div>  
                            <div class="d-flex align-items-start">
                                <div class="label">
                                    Unit:
                                </div>
                                <div class="value">
                                    {{$payslip['employee']['unit']}}
                                </div>
                            </div>  
                        </div>
                        <div class="info">
                            <div class="tle px-2">*** Earnings ***</div>
                            <div class="d-flex align-items-start">
                                <div class="label">Monthly Basic Salary:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['basic_salary']}}</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">Personnel Economic Relief Allowance:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['pera']}}</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">Overtime:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['overtime']}}</div>
                            </div>
                            
                            <div class="tle px-2">*** Deductions ***</div>
                            <div class="d-flex align-items-start">
                                <div class="label">GSIS Contribution:</div>
                                <div class="value">PHP 0.00 </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">PAG-IBIG Contribution:</div>
                                <div class="value">PHP200.00</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">Phil Health Contribution:</div>
                                <div class="value">PHP400.00</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">GSIS Emergency Loan:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['emergency_loan']}}</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">GSIS Conso Loan:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['consoloan']}}</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">GSIS Education Assistance Loan:</div>
                                <div class="value">PHP 0.00</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">GSIS Policy Loan:</div>
                                <div class="value">PHP 0.00</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">GSIS MPL:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['mpl']}}</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">GSIS MPL Lite:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['mplstlms']}}</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">GSIS CPL:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['cpl']}}</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">HDMF Calamity Loan:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['hdmf']}}</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">HDMF MP2:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['mp2']}}</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">HDMF MP3:</div>
                                <div class="value">PHP 0.00</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">Cir375-ECQ:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['cir375_cir449']}}</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">BIR Withholding TAX:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['w_tax']}}</div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="label">Lates / Undertime / Absences:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['aut']}}</div>
                            </div>    
                            <div class="d-flex align-items-start">
                                <div class="label">Total Deductions</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['total_deductions']}}</div>
                            </div>    
                            <div class="tle px-2">*** Net Pay ***</div>
                            <div class="d-flex align-items-start">
                                <div class="label">Net Amount:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['net_amount']}}</div>
                            </div>  
                            <div class="d-flex align-items-start">
                                <div class="label">DBP:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['dbp']}}</div>
                            </div>          
                            <div class="d-flex align-items-start">
                                <div class="label">Unlad Kawani:</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['kawani']}}</div>
                            </div>    
                            <div class="d-flex align-items-start">
                                <div class="label">Amount Due (15):</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['salary']}}</div>
                            </div>  
                            <div class="d-flex align-items-start">
                                <div class="label">Amount Due (28):</div>
                                <div class="value">PHP {{$payslip['payroll'][0]['salary']}}</div>
                            </div>  
                        </div>
                        <div class="info">
                            <div class="text-center pt-2">
                                <div>Issued by : <span class="text-decoration-underline">____________________</span></div>
                                <div>___________________________</div>
                            </div>
                        </div>
                    </div>    
                </div>
            </div>
        @endif
    </div>
</div>