<div>
    <div class="action mb-4">
        
    </div>
    <hr class="mt-0">
    <div class="text-uppercase fw-bold">
        @if($isApproved)
            <h2 class="text-success fw-bold text-uppercase text-center">Approved</h2>
        @else
            <h2 class="text-danger fw-bold text-uppercase text-center">Pending</h2>
        @endif
    </div>
    <hr>
    @if(!$isApproved)
    <button class="btn btn-success mb-3" wire:click="$set('showAddModal', true)">
        + Add Employee
    </button>
    @endif
    
    @if($showAddModal)
    <div class="card p-4 mb-3">
        
        <input type="text"
        wire:keyup="searchEmployeeAction($event.target.value)"
        class="form-control mb-2"
        placeholder="Search employee name or ID">

        <div>
          
            @foreach($employeeResults ?? [] as $emp)
            <div class="border p-2 mb-1 cursor-pointer"
                wire:click="selectEmployee({{ $emp->id }})">
                {{ $emp->employee_no }} - {{ $emp->name }}
            </div>
        @endforeach
        </div>

        @if($selectedEmployee)
            <div class="mt-3 p-3 border">
                <strong>{{ $selectedEmployee['name'] }}</strong><br>
                {{ $selectedEmployee['position'] ?? 'N/A' }}
            </div>
            @if(!$showDuploicateLabel)
            <button class="btn btn-primary mt-2" wire:click="confirmAddEmployee">
                Confirm Add
            </button>
            @endif
            @if($showDuploicateLabel)
                <div class="border border-danger rounded p-2 mt-2 bg-light">
                    <span class="text-danger fw-semibold">
                        ⚠ Employee already exists in payroll
                    </span>
                </div>
            @endif
        @endif
    
        <button class="btn btn-secondary mt-2" wire:click="$set('showAddModal', false)">
            Cancel
        </button>
    </div>
    @endif
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="text-uppercase fw-bold">
                Type : <span class="ms-2">{{$records['payroll']['type']}}</span>
            </div>
            <div class="text-uppercase fw-bold">
                Date : <span class="ms-2">{{$records['payroll']['formatted_payroll_date']}}</span>
            </div>
            <div class="text-uppercase fw-bold">
                Cut-off Period : <span class="ms-2">{{$records['payroll']['formatted_cutoff_period']}}</span>
            </div>
            <div class="text-uppercase fw-bold">
                Employee Type : <span class="ms-2">{{$records['payroll']['formatted_employment_type']}}</span>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="text-uppercase fw-bold">
                No. of employees : <span class="ms-2">{{$records['payroll']['no_employees']}}</span>
            </div>
            <div class="text-uppercase fw-bold">
                Net Amount : <span class="ms-2">PHP {{number_format($records['payroll']['overall_net_amount'], 2)}}</span>
            </div>
            <div class="text-uppercase fw-bold">
                Salary Amount : <span class="ms-2">PHP {{number_format($records['payroll']['overall_salary'], 2)}}</span>
            </div>
        </div>
    </div>
    <hr class="pt-3">
    @php
        $status = $records['payroll']['status'];
    @endphp
    @if($product == 'government')
        @if($records['payroll']['employment_type']['id'] == '1')
        <div class="payroll-table-wrapper">
            <table class="payroll-table">
                    <thead>
                        <tr>
                            @if(!$isApproved)
                            <th rowspan="2" class="vertical-text text-dark">Action</th>
                            @endif
                            <th rowspan="2" class="vertical-text text-dark">Status</th>
                            <th rowspan="2" class="text-center">Name</th>
                            <th rowspan="2" class="text-center">Position</th>
                            <th rowspan="2" class="text-center">Basic Salary</th>
                            <th rowspan="2" class="text-center">Pera</th>
                            <th rowspan="2" class="text-center">Gross Amount Earned</th>
                            <th colspan="25" class="text-center">DEDUCTIONS: (GSIS, MPL, PHILHEALTH, AUT, and W/TAX)</th>
                  
                            <th colspan="10" class="text-center"></th>
                            <th colspan="10" class="text-center">Salary</th> 
                        </tr>
                        <tr>
                            <th colspan="2" class="vertical-text green">RLIP</th>
                            <th colspan="2" class="vertical-text yellow">HDMF</th>
                            <th colspan="2" class="vertical-text skyblue">PHILHEALTH</th>
                            <th colspan="2" class="vertical-text green">CONSOLOAN</th>
                            <th colspan="2" class="vertical-text green">EMERGYLN</th>
                            <th colspan="2" class="vertical-text green">PLREG</th>
                            <th colspan="2" class="vertical-text green">MPL</th>
                            <th colspan="2" class="vertical-text green">MPL LITE</th>
                            <th colspan="2" class="vertical-text green">CPL</th>
                            <th colspan="2" class="vertical-text green">GSEL</th>
                            <th colspan="2" class="vertical-text green">GBEL</th>
                            <th colspan="2" class="vertical-text yellow">MP2</th>
                            <th colspan="2" class="vertical-text yellow">MPL STLMS</th>
                            <th colspan="2" class="vertical-text yellow">CIR375, CIR449</th>
                            <th colspan="2" class="vertical-text red">W/TAX</th>
                            <th colspan="2" class="vertical-text red">UCA</th>
                            <th colspan="2" class="horizantal-text red">DISALLOWANCE</th>
                            <th class="vertical-text grey">AUT</th>
                            <th class="text-center">TOTAL DED.</th>
                            <th class="text-center">NET AMOUNT</th>
                           <!-- <th colspan="2" class="vertical-text grey">DBP BRANCH</th>
                            <th colspan="2" class="vertical-text grey">KAWANI</th>
                            <th colspan="4" class="vertical-text grey">LBP PAYROLL ACCOUNT</th>-->
                            <th colspan="4" class="text-center">1st Half</th>
                            <th colspan="4" class="text-center">2nd Half</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($records['payroll_items'] as $sectionIndex => $sectionGroup)
                           <tr class="section-header section-c">
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
                                    @if(!$isApproved)
                                    <td>
                                        
                                            <button 
                                                wire:click="confirmDelete({{ $sectionIndex }}, {{ $employeeIndex }})"
                                                class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                       
                                    </td>
                                    @endif
                                    <td>
                                        <div class="marked-changed">
                                            #{{ $employeeIndex + 1 }}
                                            @if(in_array($record['id'], $updatedItems))
                                                <i class="fa-solid fa-triangle-exclamation unsaved" title="Unsaved changes"></i>
                                            @else
                                                <i class="fa-solid fa-check ready" title="No changes made"></i>
                                            @endif
                                        </div>
                                       
                                    </td>
                                    <td>
                                        <a href="{{ route('hris.show', ['employee_no' => $record['employee_no'], 'form' => 'information']) }}"
                                        class="text-dark" target="_blank">
                                            {{ $record['name'] }}
                                        </a>
                                    </td>
                                    <td>{{ $record['position'] }}</td>
                                    <td>
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="basic_salary.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input" {{ $isApproved ? 'readonly' : '' }}>
                                    </td>
                                    <td>
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="pera.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input" {{ $isApproved ? 'readonly' : '' }}>
                                    </td>
                                    <td>
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="gross_amount_earned.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input" {{ $isApproved ? 'readonly' : '' }}>
                                    </td>   
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="rlip.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input" {{ $isApproved ? 'readonly' : '' }}>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="hdmf.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="philhealth.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>    
                                        
                                      </td>
                                    <td colspan="2">
                                             <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="consoloan.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>    
                                    </td>
                                    <td colspan="2">
                                           <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="emergency_loan.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input" {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                    </td>
                                    <td colspan="2">
                                             <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="plreg.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input" {{ $isApproved ? 'readonly' : '' }}>  
                                          
                                    </td>
                                    <td colspan="2">
                                             <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="mpl.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                          
                                        </td>
                                    <td colspan="2">
                                             <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="mpl_lite.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                          
                                        </td>    
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="cpl.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                          
                                        </td>

                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="gsel.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                    </td>

                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="gbel.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                    </td>
                                    
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="mp2.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                    </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="mplstlms.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }}" style="width: 120px;" {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                        </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="cir375_cir449.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input" {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                        </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="w_tax.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                        </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="uca.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="disallowance.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>
                                    </td>
                                    <td>
                                         <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="aut.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input" {{ $isApproved ? 'readonly' : '' }}>
                                        
                                        </td>
                                    
                                    <td>
                                         <input type="text"
                                            wire:model.lazy="total_deductions.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            wire:keydown="manualEdit({{ $sectionIndex }}, {{ $employeeIndex }}, 'total_deductions')"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>
                                        
                                       </td>
                                    <td>
                                        <input type="text" class="form-control wide-input {{ $isApproved ? 'restricted' : '' }}" " 
                                            wire:model.lazy="net_amount.{{ $sectionIndex }}.{{ $employeeIndex }}" 
                                            wire:keydown="manualEdit({{ $sectionIndex }}, {{ $employeeIndex }}, 'net_amount')"
                                        {{ $isApproved ? 'readonly' : '' }}>
                                        </td>
                                 <!--   <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="dbp.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input" style="width: 120px;" {{ $isApproved ? 'readonly' : '' }}>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="kawani.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>
                                    </td>
                                    <td colspan="4">
                                          <input type="text" class="form-control wide-input {{ $isApproved ? 'restricted' : '' }}" 
                                         wire:model.lazy="lbp_payroll_account.{{ $sectionIndex }}.{{ $employeeIndex }}" 
                                            wire:keydown="manualEdit({{ $sectionIndex }}, {{ $employeeIndex }}, 'lbp_payroll_account')"
                                        {{ $isApproved ? 'readonly' : '' }}> 
                                        </td>-->
                                    <td colspan="4">

                                        <input type="text"
                                        class="form-control wide-input
                                            {{ ($isApproved || $isSecondCutoff) ? 'restricted' : '' }}"
                                        wire:model.lazy="net_first_half.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                        wire:keydown="manualEdit({{ $sectionIndex }}, {{ $employeeIndex }}, 'net_first_half')"
                                        {{ ($isApproved || $isSecondCutoff) ? 'readonly' : '' }}
                                    >

                                   
                                    </td>
                                    <td colspan="4">
                                          <input type="text"
                                            class="form-control wide-input
                                                {{ ($isApproved) ? 'restricted' : '' }}"
                                            wire:model.lazy="net_second_half.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            wire:keydown="manualEdit({{ $sectionIndex }}, {{ $employeeIndex }}, 'net_second_half')"
                                            {{ ($isApproved ) ? 'readonly' : '' }}
                                        >

                                       
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="12" class="py-3 text-uppercase fw-bold text-muted">
                                    No data found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        @if($records['payroll']['employment_type']['id'] == '2' || $records['payroll']['employment_type']['id'] == '4')
        <div class="payroll-table-wrapper">
            <table class="payroll-table">
                    <thead>
                        <tr>
                            @if(!$isApproved)
                            <th rowspan="2" class="vertical-text text-dark">Action</th> 
                            @endif
                            <th rowspan="2" class="vertical-text text-dark">Status</th>
                            <th rowspan="2" class="text-center">Name</th>
                            <th rowspan="2" class="text-center">Position</th>
                            <th rowspan="2" class="text-center">Basic Salary</th>
                            <th colspan="13" class="text-center">DEDUCTIONS: (GSIS, MPL, PHILHEALTH, AUT, and W/TAX)</th>
                  
                            <th colspan="10" class="text-center"></th>
                            <th colspan="10" class="text-center">Salary</th> 
                        </tr>
                        <tr>
                            <th colspan="2" class="vertical-text yellow">HDMF</th>
                            <th colspan="2" class="vertical-text skyblue">PHIL HEALTH</th>
                            <th colspan="2" class="vertical-text yellow">MP2</th>
                            <th colspan="2" class="vertical-text yellow">MPL</th>
                           <!-- <th colspan="2" class="vertical-text green">MPL LITE</th>-->
                            
                            <!--<th colspan="2" class="vertical-text yellow">MPL STLMS</th>-->
                            <th colspan="2" class="vertical-text yellow">CIR375, CIR449</th>
                            <th colspan="2" class="vertical-text red">UCA</th>
                            @if($use_realtime_aut)
                            <th class="vertical-text grey">AUT</th>
                            @else
                            <!--<th class="vertical-text grey">AUT</th>-->
                                @foreach($autMonths as $month)
                                <th class="vertical-text grey">
                                    {{ strtoupper($month->format('M')) }} AUT
                                </th>
                                @endforeach
                           

                        <th class="vertical-text grey">TOTAL AUT</th>
                        @endif
                            <th colspan="2" class="vertical-text red">Over payment</th>
                            <th class="vertical-text red">TAX (3%)</th>
                            <th class="vertical-text red">TAX (5%)</th>
                           <!-- <th class="vertical-text red">TAX (8%)</th>-->
                            <th class="vertical-text red">TAX (10%)</th>
                            <th class="text-center">TOTAL DED.</th>
                            <th class="text-center">NET AMOUNT</th>
                         <!--   <th colspan="2" class="vertical-text grey">DBP</th>
                            <th colspan="2" class="vertical-text grey">KAWANI</th>
                            <th colspan="4" class="vertical-text grey">LBP PAYROLL ACCOUNT</th>-->
                            <th colspan="4" class="text-center">1st Half</th>
                            <th colspan="4" class="text-center">2nd Half</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($records['payroll_items'] as $sectionIndex => $sectionGroup)
                        <tr class="section-header section-c">
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
                                    @if(!$isApproved)
                                    <td>
                                      
                                            <button 
                                                wire:click="confirmDelete({{ $sectionIndex }}, {{ $employeeIndex }})"
                                                class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                       
                                    </td>
                                    @endif
                                    <td>
                                        <div class="marked-changed">
                                            #{{ $employeeIndex + 1 }}
                                            @if(in_array($record['id'], $updatedItems))
                                                <i class="fa-solid fa-triangle-exclamation unsaved" title="Unsaved changes"></i>
                                            @else
                                                <i class="fa-solid fa-check ready" title="No changes made"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('hris.show', ['employee_no' => $record['employee_no'], 'form' => 'information']) }}"
                                        class="text-dark" target="_blank">
                                            {{ $record['name'] }}
                                        </a>
                                    </td>
                                    <td>{{ $record['position'] }}</td>
                                    <td>
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="basic_salary.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input" {{ $isApproved ? 'readonly' : '' }}>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="hdmf.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="philhealth.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>    
                                        
                                      </td>
                                      <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="mp2.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                    </td>
                                    
                                    <td colspan="2">
                                             <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="mpl.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                          
                                        </td>
                                  
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="cir375_cir449.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input" {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                        </td>
                                        <td colspan="2">
                                            <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                                wire:model="uca.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                                class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>
                                        </td>
                                        @if($use_realtime_aut)
                                        <td>
                                            <input type="text"
                                                wire:model.live="aut.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                                wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                                class="form-control wide-input">
                                        </td>
                                        @else
                                        <td>
                                            <input type="text"
                                                wire:model.live="aut_month1.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                                wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                                class="form-control wide-input">
                                        </td>
                                        
                                        <td>
                                            <input type="text"
                                                wire:model.live="aut_month2.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                                wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                                class="form-control wide-input">
                                        </td>
                                        
                                        <td>
                                            <input type="text"
                                                wire:model.live="aut_month3.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                                wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                                class="form-control wide-input">
                                        </td>
                                        
                                        <td>
                                            <input type="text"
                                                wire:model="aut_total.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                                readonly
                                                class="form-control wide-input">
                                        </td>
                                        @endif
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="overpayment.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                        </td>
                                        <td>
                                            <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                                wire:model="tax_3.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                                class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                        </td>
                                        
                                        <td>
                                            <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                                wire:model="tax_5.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                                class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                        </td>
                                        
                                      <!--  <td>
                                            <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                                wire:model="tax_8.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                                class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                        </td>-->
                                        
                                        <td>
                                            <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                                wire:model="tax_10.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                                class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>  
                                        
                                        </td>
                                    
                                    
                                    <td>
                                         <input type="text"
                                            wire:model.lazy="total_deductions.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            wire:keydown="manualEdit({{ $sectionIndex }}, {{ $employeeIndex }}, 'total_deductions')"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>
                                        
                                       </td>
                                    <td>
                                        <input type="text" class="form-control wide-input {{ $isApproved ? 'restricted' : '' }}" " 
                                            wire:model.lazy="net_amount.{{ $sectionIndex }}.{{ $employeeIndex }}" 
                                            wire:keydown="manualEdit({{ $sectionIndex }}, {{ $employeeIndex }}, 'net_amount')"
                                        {{ $isApproved ? 'readonly' : '' }}>
                                        </td>
                                   <!-- <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="dbp.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input" style="width: 120px;" {{ $isApproved ? 'readonly' : '' }}>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                            wire:model="kawani.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            class="form-control {{ $isApproved ? 'restricted' : '' }} wide-input"  {{ $isApproved ? 'readonly' : '' }}>
                                    </td>
                                    <td colspan="4">
                                          <input type="text" class="form-control wide-input {{ $isApproved ? 'restricted' : '' }}" 
                                         wire:model.lazy="lbp_payroll_account.{{ $sectionIndex }}.{{ $employeeIndex }}" 
                                            wire:keydown="manualEdit({{ $sectionIndex }}, {{ $employeeIndex }}, 'lbp_payroll_account')"
                                        {{ $isApproved ? 'readonly' : '' }}> 
                                        </td>-->
                                    <td colspan="4">

                                        <input type="text"
                                        class="form-control wide-input
                                            {{ ($isApproved || $isSecondCutoff) ? 'restricted' : '' }}"
                                        wire:model.lazy="net_first_half.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                        wire:keydown="manualEdit({{ $sectionIndex }}, {{ $employeeIndex }}, 'net_first_half')"
                                        {{ ($isApproved || $isSecondCutoff) ? 'readonly' : '' }}
                                    >

                                    </td>
                                    <td colspan="4">
                                          <input type="text"
                                            class="form-control wide-input
                                                {{ ($isApproved || $isFirstCutoff) ? 'restricted' : '' }}"
                                            wire:model.lazy="net_second_half.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                            wire:keydown="manualEdit({{ $sectionIndex }}, {{ $employeeIndex }}, 'net_second_half')"
                                            {{ ($isApproved || $isFirstCutoff) ? 'readonly' : '' }}
                                        >

                                       
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="12" class="py-3 text-uppercase fw-bold text-muted">
                                    No data found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


        @endif
    @endif
    
    @if($product == 'private')
    <div class="table-responsive pb-3">
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Basic Salary</th>
                    <th>Overtime</th>
                    <th>Holiday Pay</th>
                    <th>Allowances</th>
                    <th>Gross Amount</th>
                    <th>SSS</th>
                    <th>PhilHealth</th>
                    <th>Pagibig</th>
                    <th>W/Tax</th>
                    <th>AUT</th>
                    <th>Other Loans</th>
                    <th>Total Deductions</th>
                    <th>Net Amount</th>
                    <th>Bank Name</th>
                    <th>Bank Account</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records['payroll_items'] as $sectionIndex => $sectionGroup)
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
                                    @if(in_array($record['id'] ?? null, $updatedItems ?? []))
                                        <i class="fa-solid fa-triangle-exclamation unsaved" title="Unsaved changes"></i>
                                    @else
                                        <i class="fa-solid fa-check ready" title="No changes made"></i>
                                    @endif
                                </div>
                            </td>
                            <td>#{{ $employeeIndex + 1 }}</td>
                            <td>
                                <a href="{{ route('hris.show', ['employee_no' => $record['employee_no'], 'form' => 'information']) }}"
                                class="text-dark" target="_blank">
                                    {{ $record['name'] }}
                                </a>
                            </td>
                            <td>{{ $record['position'] }}</td>
                            <td>{{ number_format($record['basic_salary'], 2) }}</td>
                            <td>{{ number_format($record['overtime_pay'], 2) }}</td>
                            <td>{{ number_format($record['holiday_pay'], 2) }}</td>
                            <td>{{ number_format($record['allowances'], 2) }}</td>
                            <td>{{ number_format($record['gross_amount_earned'], 2) }}</td>
                            <td>{{ number_format($record['sss'], 2) }}</td>
                            <td>{{ number_format($record['philhealth'], 2) }}</td>
                            <td>{{ number_format($record['pagibig'], 2) }}</td>
                            <td>{{ number_format($record['w_tax'], decimals: 2) }}</td>
                            <td>{{ number_format($record['aut'], decimals: 2) }}</td>
                            <td>{{ number_format($record['other_loans'], 2) }}</td>
                            <td>{{ number_format($record['total_deductions'], 2) }}</td>
                            <td>{{ number_format($record['net_amount'], 2) }}</td>
                            <td>
                                {{ $record['bank_name'] }}
                            </td>
                            <td>
                                {{ $record['bank_account'] }}
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="20" class="py-3 text-uppercase fw-bold text-muted">
                            No payroll data found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    @if($hasChanges || !empty($newItems))
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

     @if($confirmingDelete)
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
            style="background: rgba(0,0,0,0.5); z-index: 10000;">

            <div class="bg-white p-4 rounded shadow text-center" style="width: 400px;">
                <h5 class="fw-bold mb-3">Delete Employee?</h5>
                <p>This will permanently remove this employee from payroll.</p>

                <div class="d-flex justify-content-center gap-2 mt-3">
                    <button class="btn btn-secondary"
                            wire:click="$set('confirmingDelete', false)">
                        Cancel
                    </button>

                    <button 
                        class="btn btn-danger"
                        wire:click="deleteEmployee"
                        wire:loading.attr="disabled"
                        wire:target="deleteEmployee">

                        <span wire:loading.remove wire:target="deleteEmployee">
                            Confirm Delete
                        </span>

                        <span wire:loading wire:target="deleteEmployee">
                            Deleting...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div 
        wire:loading.delay
            wire:target="deleteEmployee,save,approve,confirmAddEmployee,confirmSave,selectEmployee,searchEmployeeAction,recompute"
            wire:loading.class.remove="d-none"
            class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
            style="background: rgba(0,0,0,0.5); z-index: 9999;"
        >
        <div class="bg-white p-4 rounded shadow text-center">
            <i class="fa-solid fa-spinner fa-spin fa-2x mb-2"></i>
            <div class="fw-bold">Processing payroll...</div>
        </div>
    </div>
</div>



