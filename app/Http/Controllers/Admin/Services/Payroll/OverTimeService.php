<?php

namespace App\Http\Controllers\Admin\Services\Payroll;

use App\Http\Controllers\Admin\Services\PayrollService;
use App\Services\DailyTimeRecordService;
use App\Http\Controllers\Controller;
use App\Jobs\PayrollJob;
use App\Models\EmployementTypes;
use App\Models\OTPayroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OverTimeService extends Controller {

    protected $product;
    protected $payrollService;

    public function __construct(PayrollService $payrollService) {
        $this->product = config('app.product');
        $this->payrollService = $payrollService;
    }

    public function getPayroll(int $payroll_id) {

        $payroll = OTPayroll::with('items.information.section', 'employment_type')->findOrFail($payroll_id);

        $payroll->formatted_payroll_date = Carbon::parse($payroll->payroll_date)->format('F d, Y');

        if(!is_null($payroll->period)) {
            [$startPeriod, $endPeriod] = explode(' to ', $payroll->period);
            $payroll->formatted_ot_period = sprintf(
                '%s - %s',
                Carbon::parse(trim($startPeriod))->format('M d, Y'),
                Carbon::parse(trim($endPeriod))->format('M d, Y')
            );
        } else {
            $payroll->formatted_ot_period = null;
        }

        $employmentType = EmployementTypes::find($payroll->employment_type);
        $payroll->formatted_employment_type = $employmentType->name ?? '';

        $payroll->no_employees = $payroll->items->count();

        $amount = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->amount));
        $tax = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->tax));
        $net_amount = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->net_amount));

        $payroll->total_amount = round($amount, 2);
        $payroll->total_tax = round($tax, 2);
        $payroll->total_net_amount = round($net_amount, 2);


        $payroll->type = 'OverTime Payroll';
        
        $grouped = [];

        foreach ($payroll->items as $item) {
            $section = $item->information->section ?? null;

            if (!$section) continue;

            $sectionId = $section->id;
            $sectionName = $section->name;

            if (!isset($grouped[$sectionId])) {
                $grouped[$sectionId] = [
                    'section_id' => $sectionId,
                    'section_name' => $sectionName,
                    'employees' => [],
                ];
            }

            $grouped[$sectionId]['employees'][] = $item->toArray() ?? [];
        }

        $items = array_values($grouped);

        return [
            'payroll' => $payroll->toArray() ?? [],
            'payroll_items' => $items,
            'batch_id' => $payroll->batch_id ?? null,
        ];
    
    }

    public function rules()
    {
        return [
            'ot_period' => [
                'required',
                'unique:payroll_overtime,period',
                'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/'
            ]
        ];
    }


    public function createPayroll($payload) {

        if (OTPayroll::where([
            'period' => $payload['ot_period'],
            'employment_type' => $payload['employment_type'],
        ])->exists()) {
            throw new \Exception('Payroll for this period and employment type already exists.');
        }

        $payroll = OTPayroll::create([
            'period' => $payload['ot_period'],
            'employment_type' => $payload['employment_type'],
            'selected_employees' => json_encode($payload['selected_employees'] ?? []),
            'status' => 'pending'
        ]);

        return $payroll;

    }

    public function generateChunks(int $payroll_id, int $employment_type, string $type) {

        $payroll = OTPayroll::findOrFail($payroll_id);

       // dd($payroll);

        if(!$payroll) {
            return [
                'status' => 'error',
                'message' => 'Error occured: Unknown payroll_id ' . $payroll_id
            ];
        }

        $employees = $this->payrollService->getEmployees($employment_type, $type);
        $employees = $employees['eligible']['items'];

        $selectedEmployees = json_decode($payroll->selected_employees ?? '[]', true);

        if (!empty($selectedEmployees)) {
            $employees = collect($employees)
                ->filter(function ($employee) use ($selectedEmployees) {
                    return in_array($employee['employee_no'], $selectedEmployees);
                })
                ->values()
                ->toArray();
        }

        $chunks = array_chunk($employees, 25);

        $jobs = [];


        foreach ($chunks as $chunk) {
            Log::info('OT chuck data', ['chuck' => $chunk, 'id' => $payroll->id]);
            $jobs[] = new PayrollJob(
                $chunk,          // already an array
                $payroll->id,    // pass only ID
                'ot_pay'
            );
        }

        [$startPeriod, $endPeriod] = explode(' to ', $payroll->period);
        $period_date = sprintf(
            '%s - %s',
            Carbon::parse(trim($startPeriod))->format('M d, Y'),
            Carbon::parse(trim($endPeriod))->format('M d, Y')
        );


        if(!empty($jobs)) {
            return [
                'status' => 'success',
                'jobs' => $jobs,
                'name' => 'Overtime For Period of' . $period_date,
                'payroll' => $payroll
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'No jobs were processed'
            ];
        }

    }

    public function computePayroll($payroll, $employees, $type) {
        
        if($this->product == 'government') {

           // dd($payroll);
         
            $dtr_service = app(DailyTimeRecordService::class);

            $data = [];

            foreach ($employees as $employee) {
                

                if (!is_array($employee)) {
                    Log::error('Invalid employee format in OT payroll', [
                        'employee' => $employee,
                        'type' => gettype($employee),
                    ]);
                    continue;
                }
            
                if (
                    !isset(
                        $employee['employee_no'],
                        $employee['firstname'],
                        $employee['lastname'],
                        $employee['position_name'],
                        $employee['salary']
                    )
                ) {
                    Log::error('Missing employee fields in OT payroll', [
                        'employee' => $employee,
                    ]);
                    continue;
                }

                $employee_no = $employee['employee_no'];
                $name = trim($employee['firstname'] . ' ' . $employee['lastname']);
                $position = $employee['position_name'];
                $basic_salary = $employee['salary'];

              //  $dtr = $dtr_service->getDailyTimeRecord($employee_no, $payroll->period, true);
              [$start, $end] = explode(' to ', $payroll->period);
                try {
                    $dtr = $dtr_service->getDailyTimeRecord($employee_no,  [trim($start), trim($end)], true);
                } catch (\Throwable $e) {
                    Log::error('DTR CRASHED', [
                        'employee_no' => $employee_no,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }

                if (!is_array($dtr) || !isset($dtr['summary'])) {
                    Log::error('Invalid DTR response', [
                        'employee_no' => $employee_no,
                        'dtr' => $dtr,
                        'type' => gettype($dtr),
                    ]);
                    continue;
                }
                
                $totalDays = $dtr['summary']['total_days'] ?? 0;
                $workedDays = $dtr['summary']['worked_days'] ?? 0;
                $overtime = $dtr['summary']['overtime_minues'] ?? 0;

                Log::info('DTR summary', [
                    'totalDays' => $totalDays,
                    'workedDays' => $workedDays,
                    'overtime' => $overtime,
                ]);

              //  $totalDays = $dtr['summary']['total_days'];
              //  $workedDays = $dtr['summary']['worked_days'];
              //  $overtime = $dtr['summary']['overtime_minues'];

                $ot = $this->payrollService->computeOvertimePay($basic_salary, $workedDays, $overtime);

                if ($ot['gross_ot_pay'] > 0) {
                    $data[] = [
                        'payroll_id'    => $payroll->id,
                        'employee_no'   => $employee_no,
                        'name'          => $name,
                        'position'      => $position,
                        'basic_salary'  => $basic_salary,
                        'duration'      => $overtime,
                        'amount'        => $ot['gross_ot_pay'],
                        'tax'           => $ot['tax'],
                        'net_amount'    => $ot['net_ot_pay'],
                    ];
                }

            }

            return $data;

        } else {
            
        }


    }

}