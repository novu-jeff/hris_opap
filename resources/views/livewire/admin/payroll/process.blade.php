<div>
    
    @php
        $componentMap = [
            'salary' => 'admin.payroll.process.salary',
            'clothing_allowance' => 'admin.payroll.process.clothing-allowance',
            'mid_year' => 'admin.payroll.process.mid-year',
            'year_end' => 'admin.payroll.process.year-end',
            'ot_pay' => 'admin.payroll.process.ot-pay',
        ];
    @endphp

    @if(isset($componentMap[$type]))
        @livewire($componentMap[$type], [
            'type' => $type,
            'payroll_id' => $payroll_id,
        ])
    @endif

    <style>
        
        table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
        }

        th, td {
            font-size: 12px;
            padding: 10px;
            border: 3px solid #ddd;
            padding: 8px 20px 8px 20px;
            vertical-align: middle;
            width: 300px !important;
        }

        th {
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
        }

        .vertical-text {
            color: #fff;
            font-weight: bold;
            text-align: center !important;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: normal !important;
            word-wrap: break-word !important;
            font-size: 12px;
            height: 120px;
            letter-spacing: 1px;
        }

        .marked-changed {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;

            i {
                font-size: 20px;
            }

            i.unsaved {
                color: #feb406;
            }

            i.ready {
                color:rgb(33, 179, 4);
            }
        }

    </style>
</div>
