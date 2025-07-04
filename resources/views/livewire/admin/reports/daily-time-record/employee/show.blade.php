@section('style')
<style>

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

    .print-container {
        display: flex;
        justify-content: center;
        width: 100%;
        gap: 20px;
        padding: 0 80px 0 80px;
    }

    .print-container .dtr:nth-of-type(2) {
        display: none;
    }

    .dtr {
        width: 800px;
        margin: 10px 0 50px 0;
        padding: 10mm 5mm;
        box-sizing: border-box;
        border: 1px solid rgb(178, 178, 178);
        background-color: #fff;
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
        font-size: 12px !important;
    }

    .dtr-table th, .dtr-table td {
        border: 1px solid black;
        text-align: center;
        padding: 5px;
        font-size: 12px; 
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
        font-size: 13px;
        font-weight: 600 ;
        margin-bottom: 0px !important;
        text-transform: uppercase;
        color: #000000c5;
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
<div class="mahcon">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
            <div class="section-title">
                <h1>Daily Time Record <span  class="text-primary">{{ $employee_no }}</span></h1>
            </div>
            <div class="action">
                <div class="d-md-flex gap-3">
                    <a href="{{route('reports.dtr')}}" class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
                </div>
            </div>
        </div>
        <div class="mt-3">
            @if($logs)
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
            @endif
        </div>
    </div>
    <div class="container">
        @if($logs)
            @if(!$hasLeaveCard)
                <div class="warning mt-5 mb-3">
                    <div class="alert alert-info fw-bold text-center" role="alert">
                        <p class="m-0 text-uppercase">No Leave Card Detected</p>
                        <small class="text-uppercase" style="font-size: 12px;">
                            <a href="{{route('leave.show', ['leave' => 1, 'employee' => $employee_no])}}">Click here to add</a>
                        </small>
                    </div>
                </div>
            @endif
            <div class="print-container mt-4">
                <div class="dtr">
                    <div wire:loading class="ml-2 loading-screen">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    <div class="dtr-header ms-5 d-flex justify-content-center gap-4">
                        @if($product == 'government')
                        <div>
                            <img src="{{ asset('/img/' . $provider['client_logo']) }}">            
                        </div>
                        @endif
                        <div>
                            <h1>DAILY TIME RECORD</h1>
                            <h1>{{$company}}</h1>
                            <h1>For the month of <div class="underline" style="min-width: auto !important; padding: 0 15px 0 15px !important; text-transform: uppercase">{{ \Carbon\Carbon::parse($dtrDate)->format('F Y') }} </div>(FY)</h1>
                        </div>
                    </div>

                    <div class="dtr-info">
                        <div>Employee Name: <div  style="margin-left: 10px;" class="underline">{{ $logs['employee_account']['firstname'] . ' ' . $logs['employee_account']['middlename'] . ' ' . $logs['employee_account']['lastname']}}</div></div>
                        <div>Position: <div  style="margin-left: 10px;" class="underline">{{ $logs['employee_account']['position'] }}</div></div>
                        <div>Official Time: <div style="margin-left: 10px;" class="underline">{{$officialTime}}</div></div>
                        <div>Office/Department: <div style="margin-left: 10px;" class="underline">{{ $logs['employee_account']['section'] }}</div></div>
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
                            @foreach ($logs['dtr']['logs'] as $key => $day)
                                <tr>
                                    <td style="position: relative; ">
                                        {{ \Carbon\Carbon::parse($key)->format('d D') }}
                                        @if ($day['clock_in'] !== null && $day['origin'] === 'web')
                                            <div class="shaded-box">|</div>
                                        @endif
                                    </td>
                                    @if(is_array($day['remarks']) && in_array('rest day', array_map('strtolower', $day['remarks'])) && !$day['workOnHoliday'])
                                        <td colspan="4">
                                            Rest Day
                                        </td>
                                    @else
                                        <!-- AM -->
                                        <td>
                                            @isset($day['clock_in'])
                                                {{ \Carbon\Carbon::parse($day['clock_in'])->format('g:i A') }}
                                            @else
                                                {{ ' ' }}
                                            @endisset
                                        </td>
                                        <td>{{ isset($day['lunch_in']) ? \Carbon\Carbon::parse($day['lunch_in'])->format('g:i A') : ' ' }}</td>
            
                                        <!-- PM -->
                                        <td>{{ isset($day['lunch_out']) ? \Carbon\Carbon::parse($day['lunch_out'])->format('g:i A') : ' ' }}</td>
                                        <td> {{ isset($day['clock_out']) ? \Carbon\Carbon::parse($day['clock_out'])->format('g:i A') : ' ' }}</td>
        
                                    @endif
    
                                    @php
                                        // Flag to check if it's a future date
                                        $isFuture = $day['isFuture'] ?? false;
                                    
                                        // Allowed remarks
                                        $allowedRemarks = ['absent', 'rest day', 'special hol', 'legal hol'];
                                    
                                        // Normalize and check remarks
                                        $remarks = $day['remarks'] ?? null;
                                        $isEmpty = false;
                                    
                                        if (is_array($remarks)) {
                                            $normalized = array_map('strtolower', array_map('trim', $remarks));
                                            $isEmpty = count($normalized) === 1 && in_array($normalized[0], $allowedRemarks);
                                        } elseif (is_string($remarks)) {
                                            $normalized = strtolower(trim($remarks));
                                            $isEmpty = in_array($normalized, $allowedRemarks);
                                        }
                                    
                                        // Check if any required clock times are missing or empty
                                        $clockIn = $day['clock_in'] ?? null;
                                        $lunchIn = $day['lunch_in'] ?? null;
                                        $lunchOut = $day['lunch_out'] ?? null;
                                        $clockOut = $day['clock_out'] ?? null;
                                    
                                        // If any clock times are empty or null, mark isEmpty = true
                                        if (empty($clockIn) || empty($lunchIn) || empty($lunchOut) || empty($clockOut)) {
                                            $isEmpty = true;
                                        }
                                    
                                        // Overtime calculation
                                        $totalOvertime = $day['aut']['overtime']['minutes'] ?? 0;
                                        $overtimeHours = intdiv($totalOvertime, 60);
                                        $overtimeMinutes = $totalOvertime % 60;
                                    
                                        // Total AUT calculation
                                        $totalMinutes = $day['total_aut'] ?? 0;
                                        $hours = intdiv($totalMinutes, 60);
                                        $minutes = $totalMinutes % 60;
                                    @endphp
                                
                                    
                                    <!-- Overtime: Calculate Hours -->
                                    <td>
                                        @if(!$isFuture)
                                            @if(!$isEmpty)
                                                {{ $overtimeHours > 0 ? str_pad($overtimeHours, 2, '0', STR_PAD_LEFT) . ':00' : '0' }}
                                            @endif
                                        @endif
                                    </td>
                                    
                                    <!-- Overtime: Calculate Minutes -->
                                    <td>
                                        @if(!$isFuture)
                                            @if(!$isEmpty)
                                                {{ $overtimeMinutes > 0 ? str_pad($overtimeMinutes, 2, '0', STR_PAD_LEFT) . ':00' : '0' }}
                                            @endif
                                        @endif
                                    </td>
                                    
                                    <!-- Total Combined Hours -->
                                    <td>
                                        @if(!$isFuture)
                                            @if(!$isEmpty)
                                                {{ $hours > 0 ? str_pad($hours, 2, '0', STR_PAD_LEFT) . ':00' : '0' }}
                                            @endif
                                        @endif
                                    </td>
                                    
                                    <!-- Remaining Minutes -->
                                    <td>
                                        @if(!$isFuture)
                                            @if(!$isEmpty)
                                                {{ $minutes > 0 ? str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':00' : '0' }}
                                            @endif
                                        @endif
                                    </td>
                                    
                                    <td style="width: 100px; !important">
                                        @if(isset($day['remarks']) && is_array($day['remarks']))
                                            @foreach($day['remarks'] as $index => $remark)
                                                @if(strtolower($remark) != 'rest day')
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
                                                @endif
                                            @endforeach
                                        @else
                                            <small> </small>
                                        @endif
                                        {{-- @if(isset($day['remarks']) && in_array('Discrepancy', $day['remarks'])) --}}
                                            <a href="{{ route('timekeeping.correction-apply', [
                                                'bsd_no' => $logs['employee_account']['bsd_no'] ?? null,
                                                'date' => \Carbon\Carbon::parse($key)->format('Y-m-d'),
                                            ]) }}" class="btn btn-sm btn-danger btn-correction">
                                                Correction
                                            </a>
                                        {{-- @endif    --}}
                                    </td>                      
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="dtr-summary">
                        <h5 class="text-center text-uppercase">Total Summary</h5>
                        <div class="dtr-summary-container">
                            <div class="dtr-summary-item">Days Worked = {{ $logs['dtr']['summary']['worked_days'] ?? '0' }}</div>
                            <div class="dtr-summary-item">Tardiness =
                                {{$logs['dtr']['summary']['tardiness'] ?? '0'}}
                            </div>
                            <div class="dtr-summary-item">Leave = {{ $logs['dtr']['summary']['leaves'] ?? '0' }}</div>
                            <div class="dtr-summary-item">Absences = {{ $logs['dtr']['summary']['absences'] ?? '0' }}</div>
                            <div class="dtr-summary-item">TA Freq. = {{$logs['dtr']['summary']['tardiness_freq']}} </div>
                            <div class="dtr-summary-item">Rest Day = {{ $logs['dtr']['summary']['rest_days'] ?? '0' }}</div>
                            <div class="dtr-summary-item">Overtime = {{ $logs['dtr']['summary']['overtime'] ?? '0' }}</div>
                            <div class="dtr-summary-item">Undertime =
                                {{ $logs['dtr']['summary']['undertime'] ?? '0' }}
                            </div>
                            <div class="dtr-summary-item">Special Hol. = {{ $logs['dtr']['summary']['special_hol'] ?? '0' }}</div>
                            <div class="dtr-summary-item">Total Days of Work = {{ $logs['dtr']['summary']['total_days_of_work'] ?? '0' }}</div>
                            <div class="dtr-summary-item">UT Freq. = {{$logs['dtr']['summary']['undertime_freq']}}</div>
                            <div class="dtr-summary-item">Legal Hol. = {{ $logs['dtr']['summary']['legal_hol'] ?? '0' }}</div>
                            <div class="dtr-summary-item">Less TA/UT  = {{$logs['dtr']['summary']['less_aut'] ?? '0'}} </div>
                        </div>
                    </div>
                    <div class="sepe" style="margin-top: 40px;"></div>
                    <div class="certify">
                        I, CERTIFY on my honor that the above is a true and correct report of the hours of work
                        performed, record of which was made daily at the time of arrival and departure from office.
                    </div>
                    <div class="signature">
                        <h5>{{ $logs['employee_account']['firstname'] . ' ' . $logs['employee_account']['middlename'] . ' ' . $logs['employee_account']['lastname']}}</h5>
                        <div class="sepe"></div>
                        <h6>(Name and Signature of Employee)</h6>
                        <div class="sepe" style="margin-top: 30px;"></div>
                        <p>Verified as to prescribed office hours (In-Charge)</p>
                    </div>
                    <div class="remarks">
                        <h6 style="text-transform: uppercase">Remarks:</h6>
                        <p>{{ $logs['remarks'] ?? '' }}</p>   
                    </div>
                </div>
                <div class="dtr">
                    <div wire:loading class="ml-2 loading-screen">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    <div class="dtr-header ms-5 d-flex justify-content-center gap-4">
                        @if($product == 'government')
                            <div>
                                <img src="{{ asset('/img/' . $provider['client_logo']) }}">            
                            </div>
                        @endif
                        <div>
                            <h1>DAILY TIME RECORD</h1>
                            <h1>{{$company}}</h1>
                            <h1>For the month of <div class="underline" style="min-width: auto !important; padding: 0 15px 0 15px !important; text-transform: uppercase">{{ \Carbon\Carbon::parse($dtrDate)->format('F Y') }} </div>(FY)</h1>
                        </div>
                    </div>

                    <div class="dtr-info">
                        <div>Employee Name: <div  style="margin-left: 10px;" class="underline">{{ $logs['employee_account']['firstname'] . ' ' . $logs['employee_account']['middlename'] . ' ' . $logs['employee_account']['lastname']}}</div></div>
                        <div>Position: <div  style="margin-left: 10px;" class="underline">{{ $logs['employee_account']['position'] }}</div></div>
                        <div>Official Time: <div style="margin-left: 10px;" class="underline">{{$officialTime}}</div></div>
                        <div>Office/Department: <div style="margin-left: 10px;" class="underline">{{ $logs['employee_account']['section'] }}</div></div>
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
                            @foreach ($logs['dtr']['logs'] as $key => $day)
                                <tr>
                                    <td style="position: relative; ">
                                        {{ \Carbon\Carbon::parse($key)->format('d D') }}
                                        @if ($day['clock_in'] !== null && $day['origin'] === 'web')
                                            <div class="shaded-box">|</div>
                                        @endif
                                    </td>
                                    @if(is_array($day['remarks']) && in_array('rest day', array_map('strtolower', $day['remarks'])) && !$day['workOnHoliday'])
                                        <td colspan="4">
                                            Rest Day
                                        </td>
                                    @else
                                        <!-- AM -->
                                        <td>
                                            @isset($day['clock_in'])
                                                {{ \Carbon\Carbon::parse($day['clock_in'])->format('g:i A') }}
                                            @else
                                                {{ ' ' }}
                                            @endisset
                                        </td>
                                        <td>{{ isset($day['lunch_in']) ? \Carbon\Carbon::parse($day['lunch_in'])->format('g:i A') : ' ' }}</td>
            
                                        <!-- PM -->
                                        <td>{{ isset($day['lunch_out']) ? \Carbon\Carbon::parse($day['lunch_out'])->format('g:i A') : ' ' }}</td>
                                        <td> {{ isset($day['clock_out']) ? \Carbon\Carbon::parse($day['clock_out'])->format('g:i A') : ' ' }}</td>
        
                                    @endif
    
                                    @php
                                        // Flag to check if it's a future date
                                        $isFuture = $day['isFuture'] ?? false;
                                    
                                        // Allowed remarks
                                        $allowedRemarks = ['absent', 'rest day', 'special hol', 'legal hol'];
                                    
                                        // Normalize and check remarks
                                        $remarks = $day['remarks'] ?? null;
                                        $isEmpty = false;
                                    
                                        if (is_array($remarks)) {
                                            $normalized = array_map('strtolower', array_map('trim', $remarks));
                                            $isEmpty = count($normalized) === 1 && in_array($normalized[0], $allowedRemarks);
                                        } elseif (is_string($remarks)) {
                                            $normalized = strtolower(trim($remarks));
                                            $isEmpty = in_array($normalized, $allowedRemarks);
                                        }
                                    
                                        // Check if any required clock times are missing or empty
                                        $clockIn = $day['clock_in'] ?? null;
                                        $lunchIn = $day['lunch_in'] ?? null;
                                        $lunchOut = $day['lunch_out'] ?? null;
                                        $clockOut = $day['clock_out'] ?? null;
                                    
                                        // If any clock times are empty or null, mark isEmpty = true
                                        if (empty($clockIn) || empty($lunchIn) || empty($lunchOut) || empty($clockOut)) {
                                            $isEmpty = true;
                                        }
                                    
                                        // Overtime calculation
                                        $totalOvertime = $day['aut']['overtime']['minutes'] ?? 0;
                                        $overtimeHours = intdiv($totalOvertime, 60);
                                        $overtimeMinutes = $totalOvertime % 60;
                                    
                                        // Total AUT calculation
                                        $totalMinutes = $day['total_aut'] ?? 0;
                                        $hours = intdiv($totalMinutes, 60);
                                        $minutes = $totalMinutes % 60;
                                    @endphp
                                
                                    
                                    <!-- Overtime: Calculate Hours -->
                                    <td>
                                        @if(!$isFuture)
                                            @if(!$isEmpty)
                                                {{ $overtimeHours > 0 ? str_pad($overtimeHours, 2, '0', STR_PAD_LEFT) . ':00' : '0' }}
                                            @endif
                                        @endif
                                    </td>
                                    
                                    <!-- Overtime: Calculate Minutes -->
                                    <td>
                                        @if(!$isFuture)
                                            @if(!$isEmpty)
                                                {{ $overtimeMinutes > 0 ? str_pad($overtimeMinutes, 2, '0', STR_PAD_LEFT) . ':00' : '0' }}
                                            @endif
                                        @endif
                                    </td>
                                    
                                    <!-- Total Combined Hours -->
                                    <td>
                                        @if(!$isFuture)
                                            @if(!$isEmpty)
                                                {{ $hours > 0 ? str_pad($hours, 2, '0', STR_PAD_LEFT) . ':00' : '0' }}
                                            @endif
                                        @endif
                                    </td>
                                    
                                    <!-- Remaining Minutes -->
                                    <td>
                                        @if(!$isFuture)
                                            @if(!$isEmpty)
                                                {{ $minutes > 0 ? str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':00' : '0' }}
                                            @endif
                                        @endif
                                    </td>
                                    
                                    <td style="width: 100px; !important">
                                        @if(isset($day['remarks']) && is_array($day['remarks']))
                                            @foreach($day['remarks'] as $index => $remark)
                                                @if(strtolower($remark) != 'rest day')
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
                                                @endif
                                            @endforeach
                                        @else
                                            <small> </small>
                                        @endif
                                        {{-- @if(isset($day['remarks']) && in_array('Discrepancy', $day['remarks'])) --}}
                                            <a href="{{ route('timekeeping.correction-apply', [
                                                'bsd_no' => $bsd_emp_identical ? $employee_no : ($logs['employee_account']['bsd_no'] ?? null),
                                                'date' => \Carbon\Carbon::parse($key)->format('Y-m-d'),
                                            ]) }}" class="btn btn-sm btn-danger btn-correction">
                                                Correction
                                            </a>
                                        {{-- @endif    --}}
                                    </td>                      
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="dtr-summary">
                        <h5 class="text-center text-uppercase">Total Summary</h5>
                        <div class="dtr-summary-container">
                            <div class="dtr-summary-item">Days Worked = {{ $logs['dtr']['summary']['worked_days'] ?? '0' }}</div>
                            <div class="dtr-summary-item">Tardiness =
                                {{$logs['dtr']['summary']['tardiness'] ?? '0'}}
                            </div>
                            <div class="dtr-summary-item">Leave = {{ $logs['dtr']['summary']['leaves'] ?? '0' }}</div>
                            <div class="dtr-summary-item">Absences = {{ $logs['dtr']['summary']['absences'] ?? '0' }}</div>
                            <div class="dtr-summary-item">TA Freq. = {{$logs['dtr']['summary']['tardiness_freq']}} </div>
                            <div class="dtr-summary-item">Rest Day = {{ $logs['dtr']['summary']['rest_days'] ?? '0' }}</div>
                            <div class="dtr-summary-item">Overtime = {{ $logs['dtr']['summary']['overtime'] ?? '0' }}</div>
                            <div class="dtr-summary-item">Undertime =
                                {{ $logs['dtr']['summary']['undertime'] ?? '0' }}
                            </div>
                            <div class="dtr-summary-item">Special Hol. = {{ $logs['dtr']['summary']['special_hol'] ?? '0' }}</div>
                            <div class="dtr-summary-item">Total Days of Work = {{ $logs['dtr']['summary']['total_days_of_work'] ?? '0' }}</div>
                            <div class="dtr-summary-item">UT Freq. = {{$logs['dtr']['summary']['undertime_freq']}}</div>
                            <div class="dtr-summary-item">Legal Hol. = {{ $logs['dtr']['summary']['legal_hol'] ?? '0' }}</div>
                            <div class="dtr-summary-item">Less TA/UT  = {{$logs['dtr']['summary']['less_aut'] ?? '0'}} </div>
                        </div>
                    </div>
                    <div class="sepe" style="margin-top: 40px;"></div>
                    <div class="certify">
                        I, CERTIFY on my honor that the above is a true and correct report of the hours of work
                        performed, record of which was made daily at the time of arrival and departure from office.
                    </div>
                    <div class="signature">
                        <h5>{{ $logs['employee_account']['firstname'] . ' ' . $logs['employee_account']['middlename'] . ' ' . $logs['employee_account']['lastname']}}</h5>
                        <div class="sepe"></div>
                        <h6>(Name and Signature of Employee)</h6>
                        <div class="sepe" style="margin-top: 30px;"></div>
                        <p>Verified as to prescribed office hours (In-Charge)</p>
                    </div>
                    <div class="remarks">
                        <h6 style="text-transform: uppercase">Remarks:</h6>
                        <p>{{ $logs['remarks'] ?? '' }}</p>   
                    </div>
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