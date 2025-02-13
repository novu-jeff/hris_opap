@section('style')
<style>
    /* Add any custom styling here */
    .underline {
        text-decoration: underline;
    }
    .dtr {
        width: 100%;
        margin: 0 auto;
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
        margin-top: 20px;
        text-align: center;
        font-size: 12px;
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
</style>
@endsection
<div class="container">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Daily Time Record <span  class="text-primary">{{ $employee_no }}</span></h1>
        </div>
    </div>
    <div class="mt-3">
        @if($dtr)
            <div class="py-3 d-flex justify-content-between gap-3 align-items-center">
                <div class="d-flex align-items-center">
                    <button 
                        class="btn btn-sm btn-outline-primary" 
                        wire:click="changeMonth(-1)"
                        wire:loading.attr="disabled" 
                        wire:loading.class="btn-secondary">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    
                    <div class="mx-3" id="monthYear">
                        {{ $dtrDate }}
                    </div>
                    
                    <button 
                        class="btn btn-sm btn-outline-primary" 
                        wire:click="changeMonth(1)"
                        wire:loading.attr="disabled" 
                        wire:loading.class="btn-secondary"
                        @disabled($dtrDate == now()->format('F, Y'))>
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
                <button class="btn btn-success save-as-pdf"><i class="fa-solid fa-print"></i></button>
            </div>
            <div class="d-flex gap-5">
                <div class="dtr">
                    <div wire:loading class="ml-2 loading-screen">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    <div class="dtr-header ms-5 d-flex justify-content-center">
                        <div>
                            <img src="{{ asset('img/opapru-logo.png') }}" alt="Logo">
                        </div>
                        <div>
                            <h1>DAILY TIME RECORD</h1>
                            <h1>Office of the Presidential Adviser on the Peace Process</h1>
                            <h1>For the month of <span class="underline">{{ $dtrDate }} </span>(FY)</h1>
                        </div>
                    </div>
                    <div class="dtr-info">
                        <div>Employee Name: <span  class="underline">{{ $dtr['employee_account']['firstname'] . ' ' . $dtr['employee_account']['middlename'] . ' ' . $dtr['employee_account']['lastname']}}</span></div>
                        <div>Position: <span  class="underline">{{ $dtr['employee_account']['position'] }}</span></div>
                        <div>Official Time: __________________________</div>
                        <div>Office/Department: <span class="underline">{{ $dtr['employee_account']['department'] }}</span></div>
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
                                    <td style="position: relative;">
                                        {{ \Carbon\Carbon::parse($day['date'])->format('d D') }}
                                        @if ($day['origin'] === 'web')
                                            <div class="shaded-box">|</div>
                                        @endif
                                    </td>
        
                                    <!-- AM -->
                                    <td>{{ isset($day['clock_in']) ? \Carbon\Carbon::parse($day['clock_in'])->format('g:i') : ' ' }}</td>
                                    <td>{{ isset($day['break_out']) ? \Carbon\Carbon::parse($day['break_out'])->format('g:i') : ' ' }}</td>
        
                                    <!-- PM -->
                                    <td>{{ isset($day['break_in']) ? \Carbon\Carbon::parse($day['break_in'])->format('g:i') : ' ' }}</td>
                                    <td> {{ isset($day['clock_out']) ? \Carbon\Carbon::parse($day['clock_out'])->format('g:i') : ' ' }}</td>
        
                                    <!-- Overtime: Calculate Hours and Mins-->
                                    <td>
                                        {{-- hours --}}
                                        @if(isset($day['overtime_approved']))
                                            @php
                                                $hours = str_pad(floor($day['overtime_approved'] / 60), 2, '0', STR_PAD_LEFT);
                                            @endphp
                                            {{ $hours }}
                                        @endif
                                    </td>
                                    <td>
                                        {{-- minutes --}}
                                        @if(isset($day['overtime_approved']))
                                            @php
                                                $minutes = str_pad($day['overtime_approved'] % 60, 2, '0', STR_PAD_LEFT);
                                            @endphp
                                            {{ $minutes }}
                                        @endif
                                    </td>
        
                                    <!-- AUT: Calculate Hours and Mins-->
                                    <td>
                                        {{-- hours --}}
                                        {{-- @if(isset($day['total_mins_consumed']))
                                            @php
                                                $hours = str_pad(floor($day['total_mins_consumed'] / 60), 2, '0', STR_PAD_LEFT);
                                            @endphp
                                            {{ $hours }}
                                        @endif --}}
                                    </td>
                                    <td>
                                        {{-- minutes --}}
                                        {{-- @if(isset($day['total_mins_consumed']))
                                            @php
                                                $minutes = str_pad($day['total_mins_consumed'] % 60, 2, '0', STR_PAD_LEFT);
                                            @endphp
                                            {{ $minutes }}
                                        @endif --}}
                                    </td>
                                    <td>
                                        @if(isset($day['remarks']) && is_array($day['remarks']))
                                            @foreach($day['remarks'] as $index => $remark)
                                                <small>{{ $remark }}</small>
                                                @if ($index < count($day['remarks']) - 1)
                                                    <small>,</small>
                                                @endif
                                            @endforeach
                                        @else
                                            <span> </span>
                                        @endif
                                    </td>                                                               
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="dtr-summary">
                        <table class="dtr-table">
                            <tr>
                                <td>Days Worked</td>
                                <td>{{ isset($dtr['summary']['days_works']) ? $dtr['summary']['days_works'] : ' ' }}</td>
                                <td>Tardiness</td>
                                <td>-</td>
                                <td>Leave</td>
                                <td>{{ isset($dtr['summary']['leaves']) ? $dtr['summary']['leaves'] : ' ' }}</td>
                            </tr>
                            <tr>
                                <td>Absences</td>
                                <td>{{ isset($dtr['summary']['absences']) ? $dtr['summary']['absences'] : ' ' }}</td>
                                <td>TA Freq.</td>
                                <td>0</td>
                                <td>Rest Day</td>
                                <td>{{ isset($dtr['summary']['rest_days']) ? $dtr['summary']['rest_days'] : ' ' }}</td>
                            </tr>
                            <tr>
                                <td>Overtime</td>
                                <td>-</td>
                                <td>Undertime</td>
                                <td>-</td>
                                <td>Special Hol.</td>
                                <td>{{ isset($dtr['summary']['special_holidays']) ? $dtr['summary']['special_holidays'] : ' ' }}</td>
                            </tr>
                            <tr>
                                <td>Total Days of Work</td>
                                <td>{{ isset($dtr['summary']['total_days_work']) ? $dtr['summary']['total_days_work'] : ' ' }}</td>
                                <td>UT Freq.</td>
                                <td>0</td>
                                <td>Legal Hol.</td>
                                <td>{{ isset($dtr['summary']['regular_holidays']) ? $dtr['summary']['regular_holidays'] : ' ' }}</td>
                            </tr>
                            <tr>
                                <td>Less TA/UT</td>
                                <td>0</td>
                            </tr>
                        </table>
                        <div class="signature" style="margin-top: 30px; text-align: center;">
                            <p style="margin: 0"><span  class="underline">{{ $dtr['employee_account']['firstname'] . ' ' . $dtr['employee_account']['middlename'] . ' ' . $dtr['employee_account']['lastname']}}</span></p>
                            <p>(Name and Signature of Employee)</p>
                            <p>Verified as to prescribed office hours (In-Charge)</p>
                        </div>
                    </div>
                </div>
                <div class="dtr">
                    <div wire:loading class="ml-2 loading-screen">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    <div class="dtr-header ms-5 d-flex justify-content-center">
                        <div>
                            <img src="{{ asset('img/opapru-logo.png') }}" alt="Logo">
                        </div>
                        <div>
                            <h1>DAILY TIME RECORD</h1>
                            <h1>Office of the Presidential Adviser on the Peace Process</h1>
                            <h1>For the month of <span class="underline">{{ $dtrDate }} </span>(FY)</h1>
                        </div>
                    </div>
                    <div class="dtr-info">
                        <div>Employee Name: <span  class="underline">{{ $dtr['employee_account']['firstname'] . ' ' . $dtr['employee_account']['middlename'] . ' ' . $dtr['employee_account']['lastname']}}</span></div>
                        <div>Position: <span  class="underline">{{ $dtr['employee_account']['position'] }}</span></div>
                        <div>Official Time: __________________________</div>
                        <div>Office/Department: <span class="underline">{{ $dtr['employee_account']['department'] }}</span></div>
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
                                    <td style="position: relative;">
                                        {{ \Carbon\Carbon::parse($day['date'])->format('d D') }}
                                        @if ($day['origin'] === 'web')
                                            <div class="shaded-box">|</div>
                                        @endif
                                    </td>
        
                                    <!-- AM -->
                                    <td>{{ isset($day['clock_in']) ? \Carbon\Carbon::parse($day['clock_in'])->format('g:i') : ' ' }}</td>
                                    <td>{{ isset($day['break_out']) ? \Carbon\Carbon::parse($day['break_out'])->format('g:i') : ' ' }}</td>
        
                                    <!-- PM -->
                                    <td>{{ isset($day['break_in']) ? \Carbon\Carbon::parse($day['break_in'])->format('g:i') : ' ' }}</td>
                                    <td> {{ isset($day['clock_out']) ? \Carbon\Carbon::parse($day['clock_out'])->format('g:i') : ' ' }}</td>
        
                                    <!-- Overtime: Calculate Hours and Mins-->
                                    <td>
                                        {{-- hours --}}
                                        @if(isset($day['overtime_approved']))
                                            @php
                                                $hours = str_pad(floor($day['overtime_approved'] / 60), 2, '0', STR_PAD_LEFT);
                                            @endphp
                                            {{ $hours }}
                                        @endif
                                    </td>
                                    <td>
                                        {{-- minutes --}}
                                        @if(isset($day['overtime_approved']))
                                            @php
                                                $minutes = str_pad($day['overtime_approved'] % 60, 2, '0', STR_PAD_LEFT);
                                            @endphp
                                            {{ $minutes }}
                                        @endif
                                    </td>
        
                                    <!-- AUT: Calculate Hours and Mins-->
                                    <td>
                                        {{-- hours --}}
                                        {{-- @if(isset($day['total_mins_consumed']))
                                            @php
                                                $hours = str_pad(floor($day['total_mins_consumed'] / 60), 2, '0', STR_PAD_LEFT);
                                            @endphp
                                            {{ $hours }}
                                        @endif --}}
                                    </td>
                                    <td>
                                        {{-- minutes --}}
                                        {{-- @if(isset($day['total_mins_consumed']))
                                            @php
                                                $minutes = str_pad($day['total_mins_consumed'] % 60, 2, '0', STR_PAD_LEFT);
                                            @endphp
                                            {{ $minutes }}
                                        @endif --}}
                                    </td>
                                    <td>
                                        @if(isset($day['remarks']) && is_array($day['remarks']))
                                            @foreach($day['remarks'] as $index => $remark)
                                                <small>{{ $remark }}</small>
                                                @if ($index < count($day['remarks']) - 1)
                                                    <small>,</small>
                                                @endif
                                            @endforeach
                                        @else
                                            <span> </span>
                                        @endif
                                    </td>                                                               
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="dtr-summary">
                        <table class="dtr-table">
                            <tr>
                                <td>Days Worked</td>
                                <td>{{ isset($dtr['summary']['days_works']) ? $dtr['summary']['days_works'] : ' ' }}</td>
                                <td>Tardiness</td>
                                <td>-</td>
                                <td>Leave</td>
                                <td>{{ isset($dtr['summary']['leaves']) ? $dtr['summary']['leaves'] : ' ' }}</td>
                            </tr>
                            <tr>
                                <td>Absences</td>
                                <td>{{ isset($dtr['summary']['absences']) ? $dtr['summary']['absences'] : ' ' }}</td>
                                <td>TA Freq.</td>
                                <td>0</td>
                                <td>Rest Day</td>
                                <td>{{ isset($dtr['summary']['rest_days']) ? $dtr['summary']['rest_days'] : ' ' }}</td>
                            </tr>
                            <tr>
                                <td>Overtime</td>
                                <td>-</td>
                                <td>Undertime</td>
                                <td>-</td>
                                <td>Special Hol.</td>
                                <td>{{ isset($dtr['summary']['special_holidays']) ? $dtr['summary']['special_holidays'] : ' ' }}</td>
                            </tr>
                            <tr>
                                <td>Total Days of Work</td>
                                <td>{{ isset($dtr['summary']['total_days_work']) ? $dtr['summary']['total_days_work'] : ' ' }}</td>
                                <td>UT Freq.</td>
                                <td>0</td>
                                <td>Legal Hol.</td>
                                <td>{{ isset($dtr['summary']['regular_holidays']) ? $dtr['summary']['regular_holidays'] : ' ' }}</td>
                            </tr>
                            <tr>
                                <td>Less TA/UT</td>
                                <td>0</td>
                            </tr>
                        </table>
                        <div class="signature" style="margin-top: 30px; text-align: center;">
                            <p style="margin: 0"><span  class="underline">{{ $dtr['employee_account']['firstname'] . ' ' . $dtr['employee_account']['middlename'] . ' ' . $dtr['employee_account']['lastname']}}</span></p>
                            <p>(Name and Signature of Employee)</p>
                            <p>Verified as to prescribed office hours (In-Charge)</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info" role="alert">
                @if (!empty($errors))
                    <ul class="m-0">
                        @foreach ($errors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
    </div>
</div>
