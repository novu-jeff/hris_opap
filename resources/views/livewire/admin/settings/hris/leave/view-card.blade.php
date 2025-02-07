<div>
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
                        {{$data['previous_bal']['vl'] != 0 || $data['previous_bal']['sl'] != 0 ? '(Bal. brought forward)' : ''}}
                    </td>
                    <td colspan="4">
                        <strong>
                            {{ $data['previous_bal']['vl'] != 0 ? $data['previous_bal']['vl'] : '' }}
                        </strong>
                    </td>
                    <td colspan="12">
                        <strong>
                            {{ $data['previous_bal']['sl'] != 0 ? $data['previous_bal']['sl'] : '' }}
                        </strong>
                    </td>
                </tr>
            
                @foreach($data['items'] as $item)
                    <tr>
                        <td>{{ $item['period'] }}</td>
                        <td>{{ implode(', ', array_filter([$item['vl_particulars'], $item['sl_particulars']])) }}</td>
                        <td>{{ $item['vl_earned'] ?? '-' }}</td>
                        <td style="color: red">{{ $item['vl_aut_w_pay'] ?? '-' }}</td>
                        <td style="color: red">{{ $item['vl_bal'] ?? '-' }}</td>
                        <td>{{ $item['vl_aut_wo_pay'] ?? '-' }}</td>
                        <td>{{ $item['sl_earned'] ?? '-' }}</td>
                        <td style="color: red">{{ $item['sl_aut_w_pay'] ?? '-' }}</td>
                        <td style="color: red">{{ $item['sl_bal'] ?? '-' }}</td>
                        <td>{{ $item['sl_aut_wo_pay'] ?? '-' }}</td>
                        <td>{{ $item['remarks'] ?? '-' }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="12" class="text-center">No records found</td>
                </tr>
            @endforelse
        
        </tbody>
    </table>

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
