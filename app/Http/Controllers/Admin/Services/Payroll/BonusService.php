<?php

namespace App\Http\Controllers\Admin\Services\Payroll;

use App\Http\Controllers\Admin\Services\PayrollService;
use App\Http\Controllers\Controller;
use App\Jobs\Payroll\SalaryJob;
use App\Jobs\PayrollJob;
use App\Models\BonusPayroll;
use App\Models\EmployementTypes;
use App\Models\OtherEarnings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BonusService extends Controller {

    protected $product;
    protected $bsd_emp_identical;
    protected $payrollService;

    public function __construct(PayrollService $payrollService) {
        $this->product = config('app.product');
        $this->bsd_emp_identical = config('app.bsd_emp_identical');
        $this->payrollService = $payrollService;
    }

    public function getPayroll(int $payroll_id) {

        $payroll = BonusPayroll::with('items.information.section', 'employment_type')->findOrFail($payroll_id);

        $payroll->formatted_payroll_date = Carbon::parse($payroll->payroll_date)->format('F d, Y');

        if(!is_null($payroll->cut_off_period)) {
            [$startPeriod, $endPeriod] = explode(' to ', $payroll->cut_off_period);
            $payroll->formatted_cutoff_period = sprintf(
                '%s - %s',
                Carbon::parse(trim($startPeriod))->format('M d, Y'),
                Carbon::parse(trim($endPeriod))->format('M d, Y')
            );
        } else {
            $payroll->formatted_cutoff_period = null;
        }

        $employmentType = EmployementTypes::find($payroll->employment_type);
        $payroll->formatted_employment_type = $employmentType->name ?? '';

        $payroll->no_employees = $payroll->items->count();

        $bonus = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->bonus));
        $cash_gift = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->cash_gift));
        $tax = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->tax));
        $net_amount = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->net_amount));

        $payroll->total_bonus = round($bonus, 2);
        $payroll->total_cash_gift_bonus = round($cash_gift, 2);
        $payroll->total_tax = round($tax, 2);
        $payroll->total_net_amount = round($net_amount, 2);


        $payroll->type = ucwords(str_replace('_', ' ', $payroll->bonus_type) . ' Bonus');
        
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
            'payroll_date' => 'required|date|unique:payroll_clothing_allowance,payroll_date',
        ];
    }


    public function createPayroll($payload)
    {
        /*
        |--------------------------------------------------------------------------
        | Auto-set coverage dates
        |--------------------------------------------------------------------------
        */

        $employmentType = $payload['employment_type'];
        $bonusType      = $payload['type'];
        $semester       = $payload['semester'] ?? null;
        $currentYear    = now()->year;

        $coverageFrom = null;
        $coverageTo   = null;

        /*
        |--------------------------------------------------------------------------
        | Plantilla (employment_type = 1)
        |--------------------------------------------------------------------------
        */

        if ($employmentType == 1) {

            if ($bonusType === 'mid_year') {
                $coverageFrom = Carbon::create($currentYear, 1, 1)->format('Y-m-d');
                $coverageTo   = Carbon::create($currentYear, 6, 30)->format('Y-m-d');
                $semester     = null;
            }

            if ($bonusType === 'year_end') {
                $coverageFrom = Carbon::create($currentYear, 7, 1)->format('Y-m-d');
                $coverageTo   = Carbon::create($currentYear, 12, 31)->format('Y-m-d');
                $semester     = null;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | COS / Contractual (employment_type = 2)
        |--------------------------------------------------------------------------
        */

        if ($employmentType == 2) {

            if ($semester === 'first_semester') {
                $coverageFrom = Carbon::create($currentYear, 1, 1)->format('Y-m-d');
                $coverageTo   = Carbon::create($currentYear, 6, 30)->format('Y-m-d');
            }

            if ($semester === 'second_semester') {
                $coverageFrom = Carbon::create($currentYear, 7, 1)->format('Y-m-d');
                $coverageTo   = Carbon::create($currentYear, 12, 31)->format('Y-m-d');
            }
        }

        Log::info('Midyear create Payroll', ['Payload' => $payload, 'employment_type' => $employmentType, 'bonus_type' => $bonusType, 'coverage_from' => $coverageFrom, 'coverage_to'=> $coverageTo]);

        /*
        |--------------------------------------------------------------------------
        | Duplicate Check
        |--------------------------------------------------------------------------
        */

        if (BonusPayroll::where([
            'payroll_date'    => $payload['payroll_date'],
            'employment_type' => $employmentType,
            'bonus_type'      => $bonusType,
            'coverage_from'   => $coverageFrom,
            'coverage_to'     => $coverageTo,
        ])->exists()) {
            throw new \Exception(
                'Payroll for this period and employment type already exists.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create Payroll
        |--------------------------------------------------------------------------
        */

        $payroll = BonusPayroll::create([
            'payroll_date'       => $payload['payroll_date'],
            'employment_type'    => $employmentType,
            'bonus_type'         => $bonusType,
            'coverage_from'      => $coverageFrom,
            'coverage_to'        => $coverageTo,
            'semester'           => $semester,
            'percentage'         => $payload['percentage'] ?? 100,
            'remarks'            => $payload['remarks'] ?? null,
            'selected_employees' => json_encode(
                $payload['selected_employees'] ?? []
            ),
            'status'             => 'pending',
        ]);

        return $payroll;
    }

    public function generateChunks(int $payroll_id, int $employment_type, string $type) {

        $payroll = BonusPayroll::findOrFail($payroll_id);

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
            $jobs[] = new PayrollJob(
                $chunk,          // already an array
                $payroll->id,    // pass only ID
                $type 
            );
        }

        $payroll_date = Carbon::parse($payroll->payroll_date)->format('M d, Y');

        if($type == 'mid_year') {
            $name = 'Mid Year Bonus For ' . $payroll_date;
        } else {
            $name = 'Year End Bonus For ' . $payroll_date;
        }


        if(!empty($jobs)) {
            return [
                'status' => 'success',
                'jobs' => $jobs,
                'name' => $name,
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

            $data = [];
            $currentYear = now()->year;

            foreach ($employees as $employee) {

                $employee_no = $employee['employee_no'];
                $employee_name = trim($employee['firstname'] . ' ' . $employee['lastname']);
                $employee_position = $employee['position_name'];
                $date_hired = $employee['date_hired'];
                $employment_type_id = $employee['employment_type_id'];
                $employee_salary = round(floatval($employee['salary']), 2);

                $cash_gift = 0;
                $reasons = [];

                if($type == 'year_end') {
                    $cash_gift = OtherEarnings::where('code', 'CASHGIFT')->value('amount') ?? 0;
                }

                if ($type === 'mid_year') {
                    $dateHired = $date_hired ? Carbon::parse($date_hired) : null;
                    $may15 = Carbon::create($currentYear, 5, 15);
                    $july1Prev = Carbon::create($currentYear - 1, 7, 1);

                    if (!$dateHired || $dateHired->gt($may15)) {
                        $reasons[] = 'not in service as of May 15';
                    }

                    if (!$dateHired) {
                        $reasons[] = 'no date hired';
                    } elseif ($dateHired->gt($july1Prev)) {
                        if ($dateHired->diffInMonths($may15) < 4) {
                            $reasons[] = 'less than 4 months of service from July 1 to May 15';
                        }
                    }
                }

                $hasDisqualification = !empty($reasons);

                if ($hasDisqualification) {

                    $bonus = 0;
                    $tax = 0;
                    $net = 0;
                
                } else {
                
                    $bonus = $employee_salary;
                    if($type == 'year_end') {
                        $tax = $this->payrollService->computeBonusTax($bonus, $cash_gift);
                        $net = ($bonus + $cash_gift) - $tax;
                    }else{
                        $tax = 0;
                        $net = $bonus;
                    }
                }    

                $data[] = [
                    'payroll_id' => $payroll->id,
                    'employee_no' => $employee_no,
                    'employment_type' => $employment_type_id,
                    'name' => $employee_name,
                    'position' => $employee_position,
                    'date_hired' => $date_hired,
                    'basic_salary' => $employee_salary,
                    'bonus' => $bonus,
                    'cash_gift' => $cash_gift,
                    'percentage' => $payroll->percentage,
                    'coverage_from' => $payroll->coverage_from,
                    'coverage_to' => $payroll->coverage_to,
                    'remarks' => !empty($reasons)
                    ? implode('; ', $reasons)
                    : null,
                    'tax' => $tax,
                    'net_amount' => $net 
                ];
            }

            return $data;

        } else {

            $data = [];

            foreach ($employees as $employee) {

                $employee_no = $employee['employee_no'];
                $employee_name = trim($employee['firstname'] . ' ' . $employee['lastname']);
                $employee_position = $employee['position_name'];
                $employee_salary = round(floatval($employee['salary']), 2);
                
                $bonus = $employee_salary;
                $tax = $this->payrollService->computeBonusTax($bonus);
                $net = $bonus - $tax;

                $data[] = [
                    'payroll_id' => $payroll->id,
                    'employee_no' => $employee_no,
                    'employment_type_id' => $employee['employment_type_id'],
                    'name' => $employee_name,
                    'position' => $employee_position,
                    'basic_salary' => $employee_salary,
                    'bonus' => $employee_salary,
                    'cash_gift' => 0,
                    'tax' => $tax,
                    'net_amount' => $net 
                ];
            }

            return $data;

        }


    }

}