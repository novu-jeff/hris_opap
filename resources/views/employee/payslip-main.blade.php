<div class="inner-content">
    <div class="header d-flex justify-content-center gap-3 align-items-center text-center px-5">
        <div class="logo">
                <img style="width: 100px !important;" src="{{ asset('/img/' . $provider['client_logo']) }}">            
        </div>    
        <div class="header-text">
            Office of the Presidential Adviser on Peace, Reconciliation and Unity PAYROLL PAYMENT SLIP
        </div>
    </div>  
    <div class="info">
        <div class="d-flex align-items-start">
            <div class="label">
                Cutt Off Period: 
            </div>
            <div class="value">
                {{ collect(explode(' to ', $payslip['payroll']['cut_off_period']))
                    ->map(fn($date, $i) => \Carbon\Carbon::parse($date)->format($i === 0 ? 'F j' : 'F j, Y'))
                    ->implode(' to ') }}
            </div>
        </div>  

        <div class="d-flex align-items-start">
            <div class="label">
                Payroll Date: 
            </div>
            <div class="value">
                {{ \Carbon\Carbon::parse($payslip['payroll']['payroll_date'])->format('F d, Y') }}
            </div>
        </div>  
        <div class="d-flex align-items-start">
            <div class="label">
                Employee's Name:
            </div>
            <div class="value">
                {{$payslip['name']}}
            </div>
        </div>  
        <div class="d-flex align-items-start">
            <div class="label">
                Position:
            </div>
            <div class="value">
                {{$payslip['position']}}
            </div>
        </div>  
        <div class="d-flex align-items-start">
            <div class="label">
                Unit:
            </div>
            <div class="value">
                {{$payslip['information']['section']['name']}}
            </div>
        </div>  
    </div>
    <div class="info">
        <div class="tle px-2">*** Earnings ***</div>
        <div class="d-flex align-items-start">
            <div class="label">Monthly Basic Salary:</div>
            <div class="value">PHP {{ number_format($payslip['basic_salary'], 2) }}</div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">Personnel Economic Relief Allowance:</div>
            <div class="value">PHP {{ number_Format($payslip['pera'], 2)}}</div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">Overtime:</div>
            <div class="value">PHP 0.00</div>
        </div>
        
        <div class="tle px-2">*** Deductions ***</div>
        <div class="d-flex align-items-start">
            <div class="label">GSIS Contribution:</div>
            <div class="value">PHP {{  number_format($payslip['rlip'] , 2) }} </div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">PAG-IBIG Contribution:</div>
            <div class="value">PHP {{  number_format($payslip['hdmf'] , 2) }}</div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">Phil Health Contribution:</div>
            <div class="value">PHP {{  number_format($payslip['philhealth'] , 2) }}</div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">GSIS Emergency Loan:</div>
            <div class="value">PHP {{ number_format($payslip['emergency_loan'] , 2)}}</div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">GSIS Conso Loan:</div>
            <div class="value">PHP {{ number_format($payslip['consoloan'] , 2)}}</div>
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
            <div class="value">PHP {{ number_format($payslip['mpl'] , 2)}}</div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">GSIS MPL Lite:</div>
            <div class="value">PHP {{ number_format($payslip['mplstlms'] , 2)}}</div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">GSIS CPL:</div>
            <div class="value">PHP {{ number_format($payslip['cpl'] , 2)}}</div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">HDMF Calamity Loan:</div>
            <div class="value">PHP {{ number_format($payslip['hdmf'] , 2)}}</div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">HDMF MP2:</div>
            <div class="value">PHP {{ number_format($payslip['mp2'] , 2)}}</div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">HDMF MP3:</div>
            <div class="value">PHP 0.00</div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">Cir375-ECQ:</div>
            <div class="value">PHP {{ number_format($payslip['cir375_cir449'] , 2)}}</div>
        </div>
         <div class="d-flex align-items-start">
            <div class="label">SSS:</div>
            <div class="value">PHP {{ number_format($payslip['sss'] , 2)}}</div>
        </div>
         <div class="d-flex align-items-start">
            <div class="label">PAGIBIG:</div>
            <div class="value">PHP {{ number_format($payslip['pagibig'] , 2)}}</div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">BIR Withholding TAX:</div>
            <div class="value">PHP {{ number_format($payslip['w_tax'] , 2)}}</div>
        </div>
        <div class="d-flex align-items-start">
            <div class="label">Lates / Undertime / Absences:</div>
            <div class="value">PHP {{ number_format($payslip['aut'] , 2)}}</div>
        </div>    
        <div class="d-flex align-items-start">
            <div class="label">Total Deductions</div>
            <div class="value">PHP {{ number_format($payslip['total_deductions'] , 2)}}</div>
        </div>    
        <div class="tle px-2">*** Net Pay ***</div>
        <div class="d-flex align-items-start">
            <div class="label">Net Amount:</div>
            <div class="value">PHP {{ number_format($payslip['net_amount'] , 2)}}</div>
        </div>  
        <div class="d-flex align-items-start">
            <div class="label">DBP:</div>
            <div class="value">PHP {{ number_format($payslip['dbp'] , 2)}}</div>
        </div>          
        <div class="d-flex align-items-start">
            <div class="label">Unlad Kawani:</div>
            <div class="value">PHP {{ number_format($payslip['kawani'] , 2)}}</div>
        </div>    
        <div class="d-flex align-items-start">
            <div class="label">Amount Due (15):</div>
            <div class="value">PHP {{ number_format($payslip['salary'] , 2)}}</div>
        </div>  
        <div class="d-flex align-items-start">
            <div class="label">Amount Due (28):</div>
            <div class="value">PHP {{ number_format($payslip['salary'] , 2)}}</div>
        </div>  
    </div>
    <div class="info">
        <div class="text-center pt-2">
            <div>Issued by : <span class="text-decoration-underline">____________________</span></div>
            <div>___________________________</div>
        </div>
    </div>
</div>   