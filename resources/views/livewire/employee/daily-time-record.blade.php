@section('style')
<style>
    /* Add any custom styling here */

    td {
        position: relative;
    }

    .underline {
        min-width: 300px;
        width: fit-content;
        border-bottom: 1px solid black;
        padding: 0 10px 0 20px;
        display: inline-flex;
        align-items: end;
    }
    .dtr {
        width: 850px;
        margin: 50px auto;
        padding: 10mm 5mm;
        box-sizing: border-box;
        border: 1px solid rgb(178, 178, 178);
        background-color: #fdffe4;
        border-radius: 12px;
        position: relative;
    }

    .loading-screen {
        position: absolute;
        height: 100%;
        width: 100%;
        z-index: 2;
        left: 8px;
        top: 8px;
    }

    .dtr-header {
        position: relative;
        text-align: center;
        margin-bottom: 20px;
    }

    .dtr-header img {
        position: absolute;
        top: -10px;
        left: 30px;
        height: 70px;
    }

    @media(max-width: 993px ) {
        .dtr-header img {
            left: 0;
        }
    }

    .dtr-header h1 {
        font-size: 14px;
        margin: 5px 0;
    }

    .dtr-info {
        margin-bottom: 20px;
        font-size: 14px;
    }

    .dtr-info div {
        margin-bottom: 5px;
    }

    .dtr-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .dtr-table th, .dtr-table td {
        border: 1px solid black;
        text-align: center;
        padding: 5px;
    }

    .dtr-summary {
        margin-top: 20px;
        font-size: 12px;
    }
    .dtr-summary td {
        text-align: left
    }

    .dtr-summary .signature {
        margin-top: 40px;
        text-align: center;
        font-size: 12px;
    }

    .signature h5 {
        text-transform: uppercase;
        font-weight: bold;
    }

    .shaded-box {
        position: absolute;
        top: 0;
        right: 0;
        padding: 5px;
    }
    .remarks {
        margin-top: 20px;
        font-size: 12px;
    }


    .dtr-summary {
        text-align: center;
        margin-top: 20px;
    }

    .dtr-summary h5 {
        text-transform: uppercase;
        font-weight: bold;
        margin: 30px 0 30px 0;
    }

    .dtr-summary-container {
        width: 90%;
        margin: auto;
        display: grid;
        grid-template-columns: repeat(3, minmax(200px, 1fr));
        text-align: left;
    }
    .dtr-summary-item {
        font-size: 14px;
        font-weight: 400;
        margin-bottom: 0px !important;
    }

    .signature {
        margin-top: 50px;
        text-align: center;
    }

    .sepe {
        width: 90%;
        height: 1px;
        background: #000;
        margin: 10px auto;
    }
    .certify {
        width: 90%;
        margin: auto;
        text-align: center
    }

    .remarks {
        margin-left: 40px;
    }

    .btn-correction {
        position: absolute;
        right: 0px;
        top: 50%;
        transform: translate(160px, -50%);
        display: flex;
        align-items: center;
    }

</style>
@endsection
<div class="container">
    <div class="mt-3">
        @if($dtr)
            <div class="py-3 d-flex justify-content-between gap-3 align-items-center">
                <div class="d-flex gap-3">
                    <div class="d-flex align-items-center">
                        <button 
                            class="btn btn-sm btn-outline-primary" 
                            wire:click="changeMonth('control', '-1')"
                            wire:loading.attr="disabled" 
                            wire:loading.class="btn-secondary">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        
                        <div class="mx-3" id="monthYear">
                            {{ \Carbon\Carbon::parse($dtrDate)->format('F, Y') }}
                        </div>
                        
                        <button 
                            class="btn btn-sm btn-outline-primary" 
                            wire:click="changeMonth('control', '1')"
                            wire:loading.attr="disabled" 
                            wire:loading.class="btn-secondary"
                            @disabled($dtrDate == now()->format('F, Y'))>
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                    <div>
                        <input type="month" wire:change="changeMonth('date')" wire:model="monthDate" class="form-control">
                    </div>
                </div>
                <button class="btn btn-success save-as-pdf"><i class="fa-solid fa-print"></i></button>
            </div>
            <div class="dtr">
                <div wire:loading class="ml-2 loading-screen">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </div>
                <div class="dtr-header ms-5 d-flex justify-content-center gap-4">
                    <div>
                        <img src="{{ asset('img/opapru-logo.png') }}" alt="Logo">
                    </div>
                    <div>
                        <h1>DAILY TIME RECORD</h1>
                        <h1>Office of the Presidential Adviser on the Peace Process</h1>
                        <h1>For the month of <div class="underline" style="min-width: auto !important; padding: 0 15px 0 15px !important; text-transform: uppercase">{{ \Carbon\Carbon::parse($dtrDate)->format('F Y') }} </div>(FY)</h1>
                    </div>
                </div>
                <div class="dtr-info">
                    <div>Employee Name: <div  style="margin-left: 10px;" class="underline">{{ $dtr['employee_account']['firstname'] . ' ' . $dtr['employee_account']['middlename'] . ' ' . $dtr['employee_account']['lastname']}}</div></div>
                    <div>Position: <div  style="margin-left: 10px;" class="underline">{{ $dtr['employee_account']['position'] }}</div></div>
                    <div>Official Time: <div style="margin-left: 10px;" class="underline">{{$officialTime}}</div></div>
                    <div>Office/Department: <div style="margin-left: 10px;" class="underline">{{ $dtr['employee_account']['section'] . ' - (' . $dtr['employee_account']['section_code'] . ')' }}</div></div>
                </div>
                <table class="dtr-table">
                    <thead>
                        <tr>
                            <th>Days</th>
                            <th colspan="2">AM</th>
                            <th colspan="2">PM</th>
                            <th colspan="2">OVERTIME</th>
                            <th colspan="2">AUT</th>
                            <th>Remark</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>In</th>
                            <th>Out</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Hours</th>
                            <th>Mins</th>
                            <th>Hours</th>
                            <th>Mins</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dtr['clock_in_out'] as $day)
                            <tr>
                                <td style="position: relative; ">
                                    {{ \Carbon\Carbon::parse($day['date'])->format('d D') }}
                                    @if ($day['clock_in'] !== null && $day['origin'] === 'web')
                                        <div class="shaded-box">|</div>
                                    @endif
                                </td>
    
                                <!-- AM -->
                                <td>
                                    @isset($day['clock_in'])
                                        {{ \Carbon\Carbon::parse($day['clock_in'])->format('g:i') }}{{ \Carbon\Carbon::parse($day['clock_in'])->format('A') === 'PM' ? ' PM' : '' }}
                                    @else
                                        {{ ' ' }}
                                    @endisset
                                </td>
                                <td>{{ isset($day['break_out']) ? \Carbon\Carbon::parse($day['break_out'])->format('g:i') : ' ' }}</td>
    
                                <!-- PM -->
                                <td>{{ isset($day['break_in']) ? \Carbon\Carbon::parse($day['break_in'])->format('g:i') : ' ' }}</td>
                                <td> {{ isset($day['clock_out']) ? \Carbon\Carbon::parse($day['clock_out'])->format('g:i') : ' ' }}</td>
    
                                <!-- Overtime: Calculate Hours and Mins-->
                                <td>
                                    {{-- hours --}}
                                    @if(isset($day['overtime_approved']))
                                        @php
                                            $hours = floor($day['overtime_approved'] / 60); // Get hours
                                        @endphp
                                        {{ $hours }}
                                    @endif
                                </td>
                                <td>
                                    {{-- minutes --}}
                                    @if(isset($day['overtime_approved']))
                                        @php
                                            $minutes = str_pad($day['overtime_approved'] % 60, 2, '0', STR_PAD_LEFT); // Get remaining minutes
                                        @endphp
                                        {{ $minutes }}
                                    @endif
                                </td>
                                
    
                                <!-- AUT: Calculate Hours and Mins-->
                                <td>
                                    {{-- Hours --}}
                                    @if(isset($day['total_aut']))
                                        @php
                                            $hours = intdiv($day['total_aut'], 60); // Get hours
                                        @endphp
                                        {{ $hours ?: ' ' }}
                                    @endif
                                </td>
                                <td>
                                    {{-- Minutes --}}
                                    @if(isset($day['total_aut']))
                                        @php
                                            $minutes = $day['total_aut'] % 60; // Get remaining minutes
                                        @endphp
                                        {{ ($hours != 0 && $minutes == 0) ? '0' : ($minutes ? str_pad($minutes, 2, '0', STR_PAD_LEFT) : '') }}
                                    @endif
                                </td>
                                
                                <td style="width: 100px; !important">
                                    @if(isset($day['remarks']) && is_array($day['remarks']))
                                        @foreach($day['remarks'] as $index => $remark)
                                            <small>{{ $remark }}</small>
                                            @php 
                                                $nextIndex = $index + 1;
                                                $totalRemarks = count($day['remarks']);
                                            @endphp
                                    
                                            @if ($nextIndex < $totalRemarks)
                                                @if ($nextIndex % 2 == 0)
                                                    <br> 
                                                @else
                                                    <small>, </small>
                                                @endif
                                            @endif
                                        @endforeach
                                    @else
                                        <small> </small>
                                    @endif
                                    {{-- @if(isset($day['remarks']) && in_array('Discrepancy', $day['remarks']))
                                        <a href="{{route('timekeeping.correction-apply', ['bsd_no' => $dtr['employee_account']['bsd_no'], 'date' => \Carbon\Carbon::parse($day['date'])->format('Y-m-d')])}}" class="btn btn-sm btn-danger btn-correction">Correction</a>  
                                    @endif    --}}
                                </td>                      
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="dtr-summary">
                    <h5 class="text-center text-uppercase">Total Summary</h5>
                    <div class="dtr-summary-container">
                        <div class="dtr-summary-item">Days Worked - {{ $dtr['summary']['days_works'] ?? ' ' }}</div>
                        <div class="dtr-summary-item">Tardiness 
                            @isset($dtr['summary']['tota_late'])
                                {{ floor($dtr['summary']['tota_late'] / 60) }} hr - {{ $dtr['summary']['tota_late'] % 60 }} min
                            @else
                                {{ ' ' }}
                            @endisset
                        </div>
                        <div class="dtr-summary-item">Leave - {{ $dtr['summary']['leaves'] ?? ' ' }}</div>
                        <div class="dtr-summary-item">Absences - {{ $dtr['summary']['absences'] ?? ' ' }}</div>
                        <div class="dtr-summary-item">TA Freq. 0</div>
                        <div class="dtr-summary-item">Rest Day - {{ $dtr['summary']['rest_days'] ?? ' ' }}</div>
                        <div class="dtr-summary-item">Overtime - {{ $dtr['summary']['overtime'] ?? ' ' }}</div>
                        <div class="dtr-summary-item">Undertime 
                            @isset($dtr['summary']['total_undertime'])
                                {{ floor($dtr['summary']['total_undertime'] / 60) }} hr - {{ $dtr['summary']['total_undertime'] % 60 }} min
                            @else
                                {{ ' ' }}
                            @endisset
                        </div>
                        <div class="dtr-summary-item">Special Hol. - {{ $dtr['summary']['special_holidays'] ?? ' ' }}</div>
                        <div class="dtr-summary-item">Total Days of Work - {{ $dtr['summary']['total_days_work'] ?? ' ' }}</div>
                        <div class="dtr-summary-item">UT Freq. 0</div>
                        <div class="dtr-summary-item">Legal Hol. - {{ $dtr['summary']['regular_holidays'] ?? ' ' }}</div>
                        <div class="dtr-summary-item">Less TA/UT 0</div>
                    </div>
                </div>
                <div class="sepe" style="margin-top: 40px;"></div>
                <div class="certify">
                    I, CERTIFY on my honor that the above is a true and correct report of the hours of work
                    performed, record of which was made daily at the time of arrival and departure from office.
                </div>
                <div class="signature">
                    <h5>{{ $dtr['employee_account']['firstname'] . ' ' . $dtr['employee_account']['middlename'] . ' ' . $dtr['employee_account']['lastname']}}</h5>
                    <div class="sepe"></div>
                    <h6>(Name and Signature of Employee)</h6>
                    <div class="sepe" style="margin-top: 30px;"></div>
                    <p>Verified as to prescribed office hours (In-Charge)</p>
                </div>
                <div class="remarks">
                    <h6 style="text-transform: uppercase">Remarks:</h6>
                    <p>{{ $dtr['remarks'] ?? '' }}</p>   
                </div>
            </div>
        @else
            <div class="alert alert-danger" role="alert">
                @if (!empty($errors))
                    <ul class="m-0">
                        @foreach ($errors as $error)
                            <li class="text-uppercase">{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
    </div>
</div>
