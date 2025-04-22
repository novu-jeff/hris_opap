@extends('layouts.admin', [
    'title' => 'HRIS | Slip'
])

@section('content')
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
                        Feruary 2025
                    </div>
                </div>  
                <div class="d-flex align-items-start">
                    <div class="label">
                        Employee's Name:
                    </div>
                    <div class="value">
                        Kim Mariano
                    </div>
                </div>  
                <div class="d-flex align-items-start">
                    <div class="label">
                        Position:
                    </div>
                    <div class="value">
                        SAO (SG22)
                    </div>
                </div>  
                <div class="d-flex align-items-start">
                    <div class="label">
                        Unit:
                    </div>
                    <div class="value">
                        Human Resource Management Service
                    </div>
                </div>  
            </div>
            <div class="info">
                <div class="tle px-2">*** Earnings ***</div>
                <div class="d-flex align-items-start">
                    <div class="label">Monthly Basic Salary:</div>
                    <div class="value">PHP30,000.00</div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="label">Personnel Economic Relief Allowance:</div>
                    <div class="value">PHP2,000.00</div>
                </div>
                
                <div class="tle px-2">*** Deductions ***</div>
                <div class="d-flex align-items-start">
                    <div class="label">GSIS Contribution:</div>
                    <div class="value">PHP3,000.00</div>
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
                    <div class="value">PHP1,000.00</div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="label">GSIS Conso Loan:</div>
                    <div class="value">PHP1,500.00</div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="label">GSIS Education Assistance Loan:</div>
                    <div class="value">PHP800.00</div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="label">GSIS Policy Loan:</div>
                    <div class="value">PHP600.00</div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="label">GSIS MPL:</div>
                    <div class="value">PHP1,200.00</div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="label">GSIS MPL Lite:</div>
                    <div class="value">PHP500.00</div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="label">GSIS CPL:</div>
                    <div class="value">PHP700.00</div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="label">HDMF Calamity Loan:</div>
                    <div class="value">PHP300.00</div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="label">HDMF MP2:</div>
                    <div class="value">PHP100.00</div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="label">HDMF MP3:</div>
                    <div class="value">PHP150.00</div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="label">Cir375-ECQ:</div>
                    <div class="value">PHP50.00</div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="label">BIR Withholding TAX:</div>
                    <div class="value">PHP2,500.00</div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="label">Lates / Undertime / Absences:</div>
                    <div class="value">PHP0.00</div>
                </div>    
                <div class="d-flex align-items-start">
                    <div class="label">Total Deductions</div>
                    <div class="value">PHP0.00</div>
                </div>    
                <div class="tle px-2">*** Net Pay ***</div>
                <div class="d-flex align-items-start">
                    <div class="label">Net Amount:</div>
                    <div class="value">PHP3,000.00</div>
                </div>  
                <div class="d-flex align-items-start">
                    <div class="label">DBP:</div>
                    <div class="value">PHP3,000.00</div>
                </div>          
                <div class="d-flex align-items-start">
                    <div class="label">Unlad Kawani:</div>
                    <div class="value">PHP3,000.00</div>
                </div>    
                <div class="d-flex align-items-start">
                    <div class="label">Amount Due (15):</div>
                    <div class="value">PHP3,000.00</div>
                </div>  
                <div class="d-flex align-items-start">
                    <div class="label">Amount Due (28):</div>
                    <div class="value">PHP3,000.00</div>
                </div>  
            </div>
            <div class="info">
                <div class="text-center pt-2">
                    <div>Issued by : <span class="text-decoration-underline">Louise Caberto</span></div>
                    <div>2025-04-23 10:37am</div>
                </div>
            </div>
        </div>    
    </div>
</div>
@endsection