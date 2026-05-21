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

class PremiumService extends Controller {

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

      //  dd($payload);

        $employmentType = $payload['employment_type'];
        $bonusType      = 'premium';
        $semester       = $payload['semester'] ?? null;
        $currentYear    = now()->year;

        $coverageFrom = null;
        $coverageTo   = null;

        /*
        |--------------------------------------------------------------------------
        | Plantilla (employment_type = 1)
        |--------------------------------------------------------------------------
        */

        

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
                'premium'
            );
        }

        $payroll_date = Carbon::parse($payroll->payroll_date)->format('M d, Y');

        if($type == 'premium') {
            $name = 'Semestral Premium Bonus For ' . $payroll_date;
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

    public function computePayroll($payroll, $employees, $type)
{
    if ($this->product == 'government') {

        $data = [];

        foreach ($employees as $employee) {

            /*
            |--------------------------------------------------------------------------
            | Employee Information
            |--------------------------------------------------------------------------
            */

            $employee_no = $employee['employee_no'];

            $employee_name = trim(
                ($employee['firstname'] ?? '') . ' ' .
                ($employee['lastname'] ?? '')
            );

            $employee_position = $employee['position_name'] ?? '';

            $date_hired = $employee['date_hired'] ?? null;

            $employment_type_id =
                $employee['employment_type_id'];

            $basicSalary = round(
                floatval($employee['salary'] ?? 0),
                2
            );

            /*
            |--------------------------------------------------------------------------
            | Initialize Monthly Amounts
            |--------------------------------------------------------------------------
            */

            $months = [

                'january_amount' => 0,
                'february_amount' => 0,
                'march_amount' => 0,
                'april_amount' => 0,
                'may_amount' => 0,
                'june_amount' => 0,

                'july_amount' => 0,
                'august_amount' => 0,
                'september_amount' => 0,
                'october_amount' => 0,
                'november_amount' => 0,
                'december_amount' => 0,

            ];

            /*
            |--------------------------------------------------------------------------
            | Coverage Dates
            |--------------------------------------------------------------------------
            */

            $start = Carbon::parse(
                $payroll->coverage_from
            );

            $end = Carbon::parse(
                $payroll->coverage_to
            );

            /*
            |--------------------------------------------------------------------------
            | Latest Payroll Reference
            |--------------------------------------------------------------------------
            |
            | Used as fallback if month has no payroll row
            |
            */

            $latestPayrollItem =
                \DB::table('payroll_salary_items as psi')

                    ->join(
                        'payroll_salary as ps',
                        'ps.id',
                        '=',
                        'psi.payroll_id'
                    )

                    ->where(
                        'psi.employee_no',
                        $employee_no
                    )

                    ->where(
                        'ps.employment_type',
                        $employment_type_id
                    )

                    ->where(
                        'ps.status',
                        'approved'
                    )

                    ->select(
                        'psi.basic_salary',
                        'psi.aut'
                    )

                    ->orderBy(
                        'ps.payroll_date',
                        'desc'
                    )

                    ->first();

            /*
            |--------------------------------------------------------------------------
            | Default Values
            |--------------------------------------------------------------------------
            */

            $defaultSalary = round(
                floatval(
                    $latestPayrollItem->basic_salary
                        ?? $basicSalary
                ),
                2
            );

            $defaultAut = round(
                floatval(
                    $latestPayrollItem->aut
                        ?? 0
                ),
                2
            );

            /*
            |--------------------------------------------------------------------------
            | Get Payroll Rows
            |--------------------------------------------------------------------------
            */

            $salaryItems =
                \DB::table('payroll_salary_items as psi')

                    ->join(
                        'payroll_salary as ps',
                        'ps.id',
                        '=',
                        'psi.payroll_id'
                    )

                    ->where(
                        'psi.employee_no',
                        $employee_no
                    )

                    ->where(
                        'ps.employment_type',
                        $employment_type_id
                    )

                    ->where(
                        'ps.status',
                        'approved'
                    )

                    ->whereBetween(
                        'ps.payroll_date',
                        [
                            $payroll->coverage_from,
                            $payroll->coverage_to
                        ]
                    )

                    ->select(
                        'ps.payroll_date',
                        'psi.basic_salary',
                        'psi.aut'
                    )

                    ->orderBy(
                        'ps.payroll_date'
                    )

                    ->get()

                    ->keyBy(function ($item) {

                        return Carbon::parse(
                            $item->payroll_date
                        )->format('Y-m');

                    });

            /*
            |--------------------------------------------------------------------------
            | Build Semester Months
            |--------------------------------------------------------------------------
            */

            $current = $start->copy();

            $generatedMonths = [];

            while ($current <= $end) {

                $monthKey =
                    $current->format('Y-m');

                $generatedMonths[] = [

                    'month_key' => $monthKey,

                    'month_name' => strtolower(
                        $current->format('F')
                    ),

                    'salary' => round(
                        floatval(
                            $salaryItems[$monthKey]
                                ->basic_salary
                                ?? $defaultSalary
                        ),
                        2
                    ),

                    'aut' => round(
                        floatval(
                            $salaryItems[$monthKey]
                                ->aut
                                ?? $defaultAut
                        ),
                        2
                    ),

                ];

                $current->addMonth();
            }

            /*
            |--------------------------------------------------------------------------
            | Semester Computation
            |--------------------------------------------------------------------------
            */

            $semesterTotal = 0;

            foreach ($generatedMonths as $salaryItem) {

                $month =
                    $salaryItem['month_name'];

                $salary = round(
                    floatval(
                        $salaryItem['salary'] ?? 0
                    ),
                    2
                );

                $aut = round(
                    floatval(
                        $salaryItem['aut'] ?? 0
                    ),
                    2
                );

                /*
                |--------------------------------------------------------------------------
                | Premium Formula
                |--------------------------------------------------------------------------
                |
                | (Basic Salary - AUT) * 20%
                |
                */

                $netBase = $salary - $aut;

                $premiumAmount = round(
                    $netBase * 0.20,
                    2
                );

                /*
                |--------------------------------------------------------------------------
                | Month Column
                |--------------------------------------------------------------------------
                */

                $column =
                    $month . '_amount';

                if (
                    array_key_exists(
                        $column,
                        $months
                    )
                ) {

                    $months[$column] =
                        $premiumAmount;
                }

                /*
                |--------------------------------------------------------------------------
                | Semester Total
                |--------------------------------------------------------------------------
                */

                $semesterTotal +=
                    $premiumAmount;
            }

            /*
            |--------------------------------------------------------------------------
            | Percentage
            |--------------------------------------------------------------------------
            */

            $percentage = round(
                floatval(
                    $payroll->percentage ?? 100
                ),
                2
            );

            /*
            |--------------------------------------------------------------------------
            | Bonus
            |--------------------------------------------------------------------------
            */

            $bonus = round(
                $semesterTotal *
                ($percentage / 100),
                2
            );

            /*
            |--------------------------------------------------------------------------
            | Tax
            |--------------------------------------------------------------------------
            */

            $tax = round(
                $semesterTotal * 0.05,
                2
            );

            /*
            |--------------------------------------------------------------------------
            | Net Amount
            |--------------------------------------------------------------------------
            */

            $net = round(
                $semesterTotal - $tax,
                2
            );

            /*
            |--------------------------------------------------------------------------
            | Save Row
            |--------------------------------------------------------------------------
            */

            $data[] = [

                'payroll_id' => $payroll->id,

                'employee_no' => $employee_no,

                'employment_type_id' =>
                    $employment_type_id,

                'name' => strtoupper(
                    $employee_name
                ),

                'position' => strtoupper(
                    $employee_position
                ),

                'basic_salary' =>
                    $basicSalary,

                /*
                |--------------------------------------------------------------------------
                | Monthly Columns
                |--------------------------------------------------------------------------
                */

                'january_amount' =>
                    $months['january_amount'],

                'february_amount' =>
                    $months['february_amount'],

                'march_amount' =>
                    $months['march_amount'],

                'april_amount' =>
                    $months['april_amount'],

                'may_amount' =>
                    $months['may_amount'],

                'june_amount' =>
                    $months['june_amount'],

                'july_amount' =>
                    $months['july_amount'],

                'august_amount' =>
                    $months['august_amount'],

                'september_amount' =>
                    $months['september_amount'],

                'october_amount' =>
                    $months['october_amount'],

                'november_amount' =>
                    $months['november_amount'],

                'december_amount' =>
                    $months['december_amount'],

                /*
                |--------------------------------------------------------------------------
                | Totals
                |--------------------------------------------------------------------------
                */

                'total_amount' =>
                    round($semesterTotal, 2),

                'percentage' =>
                    $percentage,

                'remarks' => null,

                'bonus' =>
                    $bonus,

                'cash_gift' => 0,

                'date_hired' =>
                    $date_hired,

                'coverage_from' =>
                    $payroll->coverage_from,

                'coverage_to' =>
                    $payroll->coverage_to,

                'semester' =>
                    $payroll->semester,

                'tax' =>
                    $tax,

                'net_amount' =>
                    $net,

            ];
        }

        return $data;
    }
}

}