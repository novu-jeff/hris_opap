<div>
    <div class="d-flex justify-content-end mb-3">
        @if(!$isEditing)
            <button type="button" wire:click="enableEdit" class="btn btn-primary">✏️ Edit</button>
        @else
            <button type="button" wire:click="save" class="btn btn-success me-2">💾 Save</button>
            <button type="button" wire:click="cancelEdit" class="btn btn-secondary">❌ Cancel</button>
        @endif

        <button type="button" class="btn btn-secondary" onclick="printLeaveCard()">
        🖨️ Print Leave Card
        </button>
    </div>

    <form wire:submit.prevent="save">
        <div class="table-responsive mt-3">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">Period</th>
                        <th rowspan="2">Particulars</th>
                        <th colspan="4">Vacation Leave</th>
                        <th colspan="4">Sick Leave</th>
                        <th rowspan="2">Remarks</th>
                    </tr>
                    <tr>
                        <th>EARNED</th>
                        <th>AUT w/ pay</th>
                        <th>BAL.</th>
                        <th>AUT w/o pay</th>
                        <th>EARNED</th>
                        <th>AUT w/ pay</th>
                        <th>BAL.</th>
                        <th>AUT w/o pay</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $year => $data)
                        <tr class="year-header" data-year="{{ $year }}" style="cursor:pointer;background-color:#f8f9fa;">
                            <td colspan="1">{{ $year }}</td>
                            <td colspan="3" style="font-weight:500;">
                                {!! $total_bal[$year]['vl'] != 0 || $total_bal[$year]['sl'] != 0 ? '(Bal. brought forward from '.($year - 1).')' : '' !!}
                            </td>
                            <td colspan="4">
                                <strong>{{ $total_bal[$year]['vl'] != 0 ? $total_bal[$year]['vl'] : '' }}</strong>
                            </td>
                            <td colspan="12">
                                <strong>{{ $total_bal[$year]['sl'] != 0 ? $total_bal[$year]['sl'] : '' }}</strong>
                            </td>
                        </tr>
                        @php $latestYear = collect($records)->keys()->max(); @endphp
                        @foreach($data['items'] as $key => $item)
                            <tr class="year-content" wire:ignore.self data-year="{{ $year }}" style="{{ $year == $latestYear ? '' : 'display:none;' }}">
                                <td>{{ $item['period'] }}</td>
                                <td>
                                    <textarea wire:model.defer="particulars.{{$year}}.{{$key}}" class="form-control" style="width:400px;height:100px;" @readonly(!$isEditing)></textarea>
                                </td>

                                {{-- Vacation Leave --}}
                                <td><input type="number" step="0.001" wire:model.defer="vl_earned.{{$year}}.{{$key}}" wire:change="onChange('vl', {{$year}}, {{$key}})" class="form-control" style="width:100px;" @readonly(!$isEditing)></td>
                                <td><input type="number" step="0.001" wire:model.defer="vl_aut_w_pay.{{$year}}.{{$key}}" wire:change="onChange('vl', {{$year}}, {{$key}})" class="form-control" style="width:100px;" @readonly(!$isEditing)></td>
                                <td style="color:red">{{ $vl_bal[$year][$key] ?? 0 }}</td>
                                <td><input type="text" wire:model.defer="vl_aut_wo_pay.{{$year}}.{{$key}}" class="form-control" style="width:100px;" @readonly(!$isEditing)></td>

                                {{-- Sick Leave --}}
                                <td><input type="number" step="0.001" wire:model.defer="sl_earned.{{$year}}.{{$key}}" wire:change="onChange('sl', {{$year}}, {{$key}})" class="form-control" style="width:100px;" @readonly(!$isEditing)></td>
                                <td><input type="number" step="0.001" wire:model.defer="sl_aut_w_pay.{{$year}}.{{$key}}" wire:change="onChange('sl', {{$year}}, {{$key}})" class="form-control" style="width:100px;" @readonly(!$isEditing)></td>
                                <td style="color:red">{{ $sl_bal[$year][$key] }}</td>
                                <td><input type="text" wire:model.defer="sl_aut_wo_pay.{{$year}}.{{$key}}" class="form-control" style="width:100px;" @readonly(!$isEditing)></td>

                                <td><textarea wire:model.defer="remarks.{{$year}}.{{$key}}" class="form-control" style="width:200px;height:80px;" @readonly(!$isEditing)></textarea></td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="12" class="text-center">No records found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <!-- PRINT TABLE (hidden by default) -->
<div id="print-section" style="display: none;">
        <div style="margin-bottom: 15px; font-size: 12px;">
            <h2>Employee Leave Card</h2>
            <table style="width: 100%; border: none; margin-bottom: 10px; font-size: 12px;">
                <tr>
                    <td><strong>Name:</strong> {{ $employee->fullname ?? '' }}</td>
                    <td><strong>Employee No:</strong> {{ $employee->employee_no }}</td>
                </tr>
                <tr>
                    <td><strong>Birthday:</strong> {{ $employee->birthday ?? '' }}</td>
                    <td><strong>Sex:</strong> {{ $employee->sex ?? '' }}</td>
                </tr>
                <tr>
                    <td><strong>Civil Status:</strong> {{ $employee->civil_status ?? '' }}</td>
                    <td><strong>Position:</strong> {{ $employee->position ?? '' }}</td>
                </tr>
                <tr>
                    <td><strong>Date Hired:</strong> {{ $employee->date_hired ?? '' }}</td>
                     <td><strong>Employment Type:</strong>  {{ $employee->employment_type ?? '' }}</td>
                </tr>
            </table>

            <!-- Leave Card Table -->
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <thead>
                    <thead>
                    <tr>
                        <th rowspan="2" style="border: 1px solid #000; padding: 5px;">Period</th>
                        <th rowspan="2" style="border: 1px solid #000; padding: 5px;">Particulars</th>
                        <th colspan="4" style="border: 1px solid #000; padding: 5px;">Vacation Leave</th>
                        <th colspan="4" style="border: 1px solid #000; padding: 5px;">Sick Leave</th>
                        <th rowspan="2" style="border: 1px solid #000; padding: 5px;">Remarks</th>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 5px;">EARNED</th>
                        <th style="border: 1px solid #000; padding: 5px;">AUT w/ pay</th>
                        <th style="border: 1px solid #000; padding: 5px;">BAL.</th>
                        <th style="border: 1px solid #000; padding: 5px;">AUT w/o pay</th>
                        <th style="border: 1px solid #000; padding: 5px;">EARNED</th>
                        <th style="border: 1px solid #000; padding: 5px;">AUT w/ pay</th>
                        <th style="border: 1px solid #000; padding: 5px;">BAL.</th>
                        <th style="border: 1px solid #000; padding: 5px;"> AUT w/o pay</th>
                    </tr>
                </thead>
                   
                </thead>
                <tbody>
                    @foreach($records as $year => $data)
                        <tr style="background: #f1f1f1; font-weight: bold;">
                            <td colspan="2">Year: {{ $year }} (Bal. brought forward)</td>
                            <td colspan="3">{{ $total_bal[$year]['vl'] ?? 0 }}</td>
                            <td colspan="3">{{ $total_bal[$year]['sl'] ?? 0 }}</td>
                            <td colspan="2"></td>
                        </tr>
                        @foreach($data['items'] as $key => $item)
                            <tr>
                                <td style="border: 1px solid #000; padding: 3px;">{{ $item['period'] }}</td>
                                <td style="border: 1px solid #000; padding: 3px;">{{ $item['particulars'] }}</td>
                                <td style="border: 1px solid #000; padding: 3px;">{{ $item['vl_earned'] }}</td>
                                <td style="border: 1px solid #000; padding: 3px;">{{ $item['vl_aut_w_pay'] }}</td>
                                <td style="border: 1px solid #000; padding: 3px;">{{ $item['vl_bal'] }}</td>
                                <td style="border: 1px solid #000; padding: 3px;">{{ $item['vl_aut_wo_pay'] }}</td>
                                <td style="border: 1px solid #000; padding: 3px;">{{ $item['sl_earned'] }}</td>
                                <td style="border: 1px solid #000; padding: 3px;">{{ $item['sl_aut_w_pay'] }}</td>
                                <td style="border: 1px solid #000; padding: 3px;">{{ $item['sl_bal'] }}</td>
                                <td style="border: 1px solid #000; padding: 3px;">{{ $item['sl_aut_wo_pay'] }}</td>
                                <td style="border: 1px solid #000; padding: 3px;">{{ $item['remarks'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

 <!-- Print Button -->
    <div class="d-flex justify-content-end mt-3">
        <button type="button" class="btn btn-secondary" onclick="printLeaveCard()">
            🖨️ Print Leave Card
        </button>
    </div>

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 20px;
            vertical-align: middle;
        }
        th { background-color: #f8f8f8; font-weight: bold; }
        .year-header { background-color: #e0e0e0; font-weight: bold; color: red; }


        #print-leave-card table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 10px; /* body font */
        }

        #print-leave-card th, #print-leave-card td {
            border: 1px solid #000; /* lines between cells */
            padding: 4px 6px;
            text-align: left;
            vertical-align: middle;
        }

        #print-leave-card thead th {
            background-color: #f0f0f0;
            font-size: 9px; /* smaller header font to fit labels */
            font-weight: bold;
        }
    </style>

    <script>
        $(function () {
            $(".year-header").click(function () {
                let year = $(this).data("year");
                $(".year-content").not("[data-year='" + year + "']").hide();
                $(".year-content[data-year='" + year + "']").toggle();
            });
        });
    </script>
</div>

<script>
function printLeaveCard() {
    var printContents = document.getElementById('print-section').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
    location.reload();
}
</script>

