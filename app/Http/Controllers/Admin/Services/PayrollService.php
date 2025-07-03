<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Admin\Services\Payroll\BonusService;
use App\Http\Controllers\Admin\Services\Payroll\ClothingAllowanceService;
use App\Http\Controllers\Admin\Services\Payroll\OverTimeService;
use App\Http\Controllers\Admin\Services\Payroll\SalaryService;
use App\Http\Controllers\Controller;
use App\Models\BonusItemsPayroll;
use App\Models\BonusPayroll;
use App\Models\ClothingAllowanceItemsPayroll;
use App\Models\ClothingAllowancePayroll;
use App\Models\OTItemsPayroll;
use App\Models\OTPayroll;
use App\Models\SalaryItemsPayroll;
use App\Models\SalaryPayroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService extends Controller {

    protected $bsd_emp_identical;
    protected string $product;

    public function __construct() {
        $this->product = config('app.product');
        $this->bsd_emp_identical = config('app.bsd_emp_identical');
    }

    public function getEmployees($employment_type, $type = null)
    {
        $currentYear = now()->year;

        $results = DB::table('employee_information as ei')
            ->select(
                'ei.id as employee_id',
                'ei.employee_no',
                'ei.employment_type_id',
                'ei.date_hired',
                'ei.monthly_rate',
                'ei.bsd_no',
                'ei.position_id',
                'p.firstname',
                'p.lastname',
                'p.gsis_no',
                'po.name as position_name',
                'po.salary_grade',
                'po.w_tax',
                's.name as section_name'
            )
            ->join('employee_personal as p', 'ei.employee_no', '=', 'p.employee_no')
            ->leftJoin('positions as po', 'ei.position_id', '=', 'po.id')
            ->leftJoin('sections as s', 'ei.section_id', '=', 's.id')
            ->where('ei.employment_type_id', $employment_type)
            ->get();

        $employees = [
            'eligible' => [],
            'ineligible' => [],
        ];

        foreach ($results as $row) {
            $reasons = [];

            $dateHired = $row->date_hired ? Carbon::parse($row->date_hired) : null;

            if ($this->product === 'government') {
                if (empty($row->bsd_no)) {
                    $reasons[] = 'no BSD number';
                }

                if (empty($row->position_id)) {
                    $reasons[] = 'no position assigned';
                }

                if ($type === 'mid_year') {
                    $may15 = Carbon::create($currentYear, 5, 15);
                    $july1Prev = Carbon::create($currentYear - 1, 7, 1);

                    if (!$dateHired || $dateHired->gt($may15)) {
                        $reasons[] = 'not in service as of May 15';
                    }

                    if (!$dateHired || $dateHired->gt($july1Prev)) {
                        if ($dateHired->diffInMonths($may15) < 4) {
                            $reasons[] = 'less than 4 months of service from July 1 to May 15';
                        }
                    }
                }

                if ($type === 'year_end') {
                    $oct31 = Carbon::create($currentYear, 10, 31);
                    $jan1 = Carbon::create($currentYear, 1, 1);

                    if (!$dateHired || $dateHired->gt($oct31)) {
                        $reasons[] = 'not in service as of October 31';
                    }

                    if (!$dateHired || $dateHired->gt($jan1)) {
                        if ($dateHired->diffInMonths($oct31) < 4) {
                            $reasons[] = 'less than 4 months of service from January 1 to October 31';
                        }
                    }
                }
            }

            if (empty($row->monthly_rate) || floatval($row->monthly_rate) === 0.0) {
                $reasons[] = 'no salary rate';
            }

            $employeeData = (array) $row;
            $employeeData['name'] = trim(($row->firstname ?? '') . ' ' . ($row->lastname ?? ''));
            $employeeData['status'] = $reasons ? 'ineligible' : 'eligible';
            $employeeData['reason'] = $reasons ?: null;

            $employees[$employeeData['status']][] = $employeeData;
        }

        return $employees;
    }

    public function getProcess(string $type) {

        $map = [
            'salary' => [
                'service' => SalaryService::class,
                'models' => [
                    'parent' => SalaryPayroll::class,
                    'child' => SalaryItemsPayroll::class,
                ]
            ],
            'clothing_allowance' => [
                'service' => ClothingAllowanceService::class,
                'models' => [
                    'parent' => ClothingAllowancePayroll::class,
                    'child' => ClothingAllowanceItemsPayroll::class,
                ]
            ],
            'mid_year' => [
                'service' => BonusService::class,
                'models' => [
                    'parent' => BonusPayroll::class,
                    'child' => BonusItemsPayroll::class,
                ]
            ],
            'year_end' => [
                'service' => BonusService::class,
                'models' => [
                    'parent' => BonusPayroll::class,
                    'child' => BonusItemsPayroll::class,
                ]
            ],
            'ot_pay' => [
                'service' => OverTimeService::class,
                'models' => [
                    'parent' => OTPayroll::class,
                    'child' => OTItemsPayroll::class,
                ]
            ]

        ];

        return $map[$type] ?? null;

    }

    public function computeWithholdingTax(float $taxableIncome): float
    {
        if ($taxableIncome <= 20833) {
            return 0;
        } elseif ($taxableIncome <= 33333) {
            return ($taxableIncome - 20833) * 0.20;
        } elseif ($taxableIncome <= 66667) {
            return 2500 + ($taxableIncome - 33333) * 0.25;
        } elseif ($taxableIncome <= 166667) {
            return 10833.33 + ($taxableIncome - 66667) * 0.30;
        } elseif ($taxableIncome <= 666667) {
            return 40833.33 + ($taxableIncome - 166667) * 0.32;
        } else {
            return 200833.33 + ($taxableIncome - 666667) * 0.35;
        }
    }

    public function computeBonusTax(float $bonus, float $cashGift = 0): float
    {
        $total = $bonus + $cashGift;
        $exemptLimit = 90000; 

        if ($total <= $exemptLimit) {
            return 0;
        }

        $taxable = $total - $exemptLimit;
        $rate = 0.20;

        return round($taxable * $rate, 2);
    }

    public function computeOvertimePay(float $basic_salary, int $workedDays, string $time_in_minutes): array
    {
        
        if ($workedDays <= 0) {
            return [
                'overtime_hours' => 0,
                'hourly_rate' => 0,
                'ot_rate' => 0,
                'gross_ot_pay' => 0,
                'tax' => 0,
                'net_ot_pay' => 0,
            ];
        }

        $decimalHours = floatval($time_in_minutes) / 60;
        $dailyRate = $basic_salary / $workedDays;
        $hourlyRate = $dailyRate / 8;
        $otRate = $hourlyRate * 1.25;
        $grossOtPay = $decimalHours * $otRate;
        $tax = $this->computeWithholdingTax($grossOtPay);
        $netOtPay = round($grossOtPay - $tax, 2);

        return [
            'overtime_hours' => round($decimalHours, 2),
            'hourly_rate' => round($hourlyRate, 2),
            'ot_rate' => round($otRate, 2),
            'gross_ot_pay' => round($grossOtPay, 2),
            'tax' => round($tax, 2),
            'net_ot_pay' => $netOtPay,
        ];
    }




}