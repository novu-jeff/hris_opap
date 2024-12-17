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
    @if($dtr)
        <div class="py-3 d-flex justify-content-between gap-3 align-items-center">
            <div class="d-flex align-items-center">
                <button 
                    class="btn btn-sm btn-outline-primary" 
                    wire:click="changeMonth(-1, '{{ Auth::user()->employee_no }}')"
                    wire:loading.attr="disabled" 
                    wire:loading.class="btn-secondary">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                
                <div class="mx-3" id="monthYear">
                    {{ $dtrDate }}
                </div>
                
                <button 
                    class="btn btn-sm btn-outline-primary" 
                    wire:click="changeMonth(1, '{{ Auth::user()->employee_no }}')"
                    wire:loading.attr="disabled" 
                    wire:loading.class="btn-secondary"
                    @disabled($dtrDate == now()->subMonth()->format('F, Y'))>
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
            <button class="btn btn-success save-as-pdf"><i class="fa-solid fa-print"></i></button>
        </div>
        <div class="dtr">
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
        <div class="alert alert-info" role="alert">
            Please contact HR regarding this issue.
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
