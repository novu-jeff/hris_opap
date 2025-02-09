<div>
    <form wire:submit.prevent="save">
        <div class="table-responsive mt-5">
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
                        <tr class="year-header">
                            <td colspan="1">
                                {{$year}} 
                            </td>
                            <td colspan="3" style="font-weight: 500 !important">
                                {{$total_bal[$year]['vl'] != 0 || $total_bal[$year]['sl'] != 0 ? '(Bal. brought forward)' : ''}}
                            </td>
                            <td colspan="4">
                                <strong>
                                    {{ $total_bal[$year]['vl'] != 0 ? $total_bal[$year]['vl'] : '' }}
                                </strong>
                            </td>
                            <td colspan="12">
                                <strong>
                                    {{ $total_bal[$year]['sl'] != 0 ? $total_bal[$year]['sl'] : '' }}
                                </strong>
                            </td>
                        </tr>
                    
                        @foreach($data['items'] as $key => $item)
                        <tr>
                            <td>{{ $item['period'] }}</td>
                            <td>
                                <textarea wire:model="particulars.{{$year}}.{{ $key }}" wire:key="particulars-{{$year}}.{{ $key }}" class="form-control" style="width: 400px; height: 100px;"></textarea>
                            </td>
                            <td>
                                <input type="number" wire:key="vl_earned-{{$year}}.{{ $key }}" wire:change="onChange('vl', {{$year}}, {{$key}})" wire:model="vl_earned.{{$year}}.{{ $key }}" class="form-control" style="width: 100px;" value="{{ $item['vl_earned'] ?? '' }}">
                            </td>
                            <td style="color: red">
                                <input type="number" wire:key="vl_aut_w_pay-{{$year}}.{{ $key }}" wire:change="onChange('vl', {{$year}}, {{$key}})" wire:model="vl_aut_w_pay.{{$year}}.{{ $key }}" class="form-control" style="width: 100px;" value="{{ $item['vl_aut_w_pay'] ?? '' }}">
                            </td>
                            <td style="color: red">
                                {{$vl_bal[$year][$key]}}
                            </td>
                            <td>
                                <input type="number" wire:key="vl_aut_wo_pay-{{$year}}.{{ $key }}" wire:model="vl_aut_wo_pay.{{$year}}.{{ $key }}" class="form-control" style="width: 100px;" value="{{ $item['vl_aut_wo_pay'] ?? '' }}">
                            </td>
                            <td>
                                <input type="number" wire:key="sl_earned-{{$year}}.{{ $key }}" wire:change="onChange('sl', {{$year}}, {{$key}})" wire:model="sl_earned.{{$year}}.{{ $key }}" class="form-control" style="width: 100px;" value="{{ $item['sl_earned'] ?? '' }}">
                            </td>
                            <td style="color: red">
                                <input type="number" wire:key="sl_aut_w_pay-{{$year}}.{{ $key }}" wire:change="onChange('sl', {{$year}}, {{$key}})" wire:model="sl_aut_w_pay.{{$year}}.{{ $key }}" class="form-control" style="width: 100px;" value="{{ $item['sl_aut_w_pay'] ?? '' }}">
                            </td>
                            <td style="color: red">
                                {{$sl_bal[$year][$key]}}
                            </td>
                            <td>
                                <input type="text" wire:key="sl_aut_wo_pay-{{$year}}.{{ $key }}" wire:model="sl_aut_wo_pay.{{$year}}.{{ $key }}" class="form-control" style="width: 100px;" value="{{ $item['sl_aut_wo_pay'] ?? '' }}">
                            </td>
                            <td>
                                <textarea wire:model="remarks.{{$year}}.{{ $key }}" wire:key="remarks-{{$year}}.{{ $key }}" class="form-control" style="width: 200px; height: 80px;"></textarea>
                            </td>                            
                        </tr>  
                                        
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="12" class="text-center">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-5">
            <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
            </button>
        </div>
    </form>

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 20px 8px 20px;
            vertical-align: middle;
        }
        th {
            background-color: #f8f8f8;
            font-weight: bold;
        }
        .year-header {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: left !important;
            color: red;
        }
    </style>
</div>
