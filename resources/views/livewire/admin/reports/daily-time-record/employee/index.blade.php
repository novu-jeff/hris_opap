@section('style')
<style>
    /* Add any custom styling here */
    .underline {
        text-decoration: underline;
    }
    .dtr {
        width: 100%;
        max-width: 600px;
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
<div>
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="dtrModal" data-bs-backdrop="static" data-bs-keyboard="false"  tabindex="-1" aria-labelledby="dtrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dtrModalLabel">Daily Time Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="py-3 d-flex justify-content-center flex-column gap-3 align-items-center">
                        <div class="d-flex align-items-center">
                            <button 
                                class="btn btn-sm btn-outline-primary" 
                                wire:click="changeMonth(-1, '{{ $employee_id }}')"
                                wire:loading.attr="disabled" 
                                wire:loading.class="btn-secondary">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            
                            <div class="mx-3" id="monthYear">
                                {{ $dtrDate }}
                            </div>
                            
                            <button 
                                class="btn btn-sm btn-outline-primary" 
                                wire:click="changeMonth(1, '{{ $employee_id }}')"
                                wire:loading.attr="disabled" 
                                wire:loading.class="btn-secondary">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    @if($dtr)
                        <div class="dtr">
                            <!-- Loading spinner when the changeMonth action is processing -->
                            <div wire:loading class="ml-2 loading-screen">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>
                            <div class="dtr-header">
                                <img src="{{ asset('img/opapru-logo.png') }}" alt="Logo">
                                <h1>DAILY TIME RECORD</h1>
                                <h1>Office of the Presidential Adviser on the Peace Process</h1>
                                <h1>For the month of <span class="underline">{{ $dtrDate }} </span>(FY)</h1>
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
                                            <td>{{ isset($day['clock_in_am']) ? \Carbon\Carbon::parse($day['clock_in_am'])->format('g:i') : ' ' }}</td>
                                            <td>{{ isset($day['clock_out_am']) ? \Carbon\Carbon::parse($day['clock_out_am'])->format('g:i') : ' ' }}</td>

                                            <!-- PM -->
                                            <td>{{ isset($day['clock_in_pm']) ? \Carbon\Carbon::parse($day['clock_in_pm'])->format('g:i') : ' ' }}</td>
                                            <td> {{ isset($day['clock_out_pm']) ? \Carbon\Carbon::parse($day['clock_out_pm'])->format('g:i') : ' ' }}</td>

                                            <!-- AUT: Calculate Hours and Mins-->
                                            <td>
                                                {{-- hours --}}
                                                @if(isset($day['total_mins_consumed']))
                                                    @php
                                                        $hours = str_pad(floor($day['total_mins_consumed'] / 60), 2, '0', STR_PAD_LEFT);
                                                    @endphp
                                                    {{ $hours }}
                                                @endif
                                            </td>
                                            <td>
                                                {{-- minutes --}}
                                                @if(isset($day['total_mins_consumed']))
                                                    @php
                                                        $minutes = str_pad($day['total_mins_consumed'] % 60, 2, '0', STR_PAD_LEFT);
                                                    @endphp
                                                    {{ $minutes }}
                                                @endif
                                            </td>

                                            <!-- Remark column -->
                                            <td>{{ '  ' }}</td> 
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="dtr-summary">
                                <table class="dtr-table">
                                    <tr>
                                        <td>Days Worked</td>
                                        <td></td>
                                        <td>Tardinesss</td>
                                        <td>0</td>
                                        <td>Leave</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Absences</td>
                                        <td>0</td>
                                        <td>TA Freq.</td>
                                        <td>0</td>
                                        <td>Rest Day</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Overtime</td>
                                        <td>0</td>
                                        <td>Undertime</td>
                                        <td>0</td>
                                        <td>Special Hol.</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Total Days Worked</td>
                                        <td>0</td>
                                        <td>UT Freq.</td>
                                        <td>0</td>
                                        <td>Legal Hol.</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Less TA/UT</td>
                                        <td>0</td>
                                        <td>No lunch</td>
                                        <td>0</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </table>
                                <div class="signature" style="margin-top: 30px; text-align: center;">
                                    <p style="margin: 0"><span  class="underline">{{ $dtr['employee_account']['firstname'] . ' ' . $dtr['employee_account']['middlename'] . ' ' . $dtr['employee_account']['lastname']}}</span></p>
                                    <p>(Name and Signature of Employee)</p>
                                    <p>Verified as to prescribed office hours (In-Charge)</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <p>No DTR available for this employee for the selected date.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <table class="table data-tables w-100">
                <thead>
                    <tr>
                        <th>Employee #</th>
                        <th>Name</th>
                        <th style="max-width: 200px;">Action</th>
                    </tr>
                </thead>                
                <tbody>
                    @foreach($records as $record)
                        <tr>
                            <td>{{$record->employee_no}}</td>
                            <td>
                                {{ $record->personal->firstname }}
                                {{ $record->personal->middlename ? substr($record->personal->middlename, 0, 1) . '.' : '' }}
                                {{ $record->personal->lastname }}
                            </td>                        
                            <td>
                                <a href="#" class="btn btn-primary" 
                                    wire:click="showDtr('{{ $record->employee_no }}')" >
                                <i class="fa-solid fa-eye"></i> Show DTR
                             </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
