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


    public function createPayroll($payload) {

        if (BonusPayroll::where([
            'payroll_date' => $payload['payroll_date'],
            'employment_type' => $payload['employment_type'],
            'bonus_type' => $payload['type'],
        ])->exists()) {
            throw new \Exception('Payroll for this period and employment type already exists.');
        }

        $payroll = BonusPayroll::create([
            'payroll_date' => $payload['payroll_date'],
            'employment_type' => $payload['employment_type'],
            'bonus_type' => $payload['type'],
            'status' => 'pending'
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
        $employees = $employees['eligible'];

        $chunks = array_chunk($employees, 1000);

        $jobs = [];


        foreach ($chunks as $chunk) {
            $jobs[] = new PayrollJob(collect($chunk), $payroll, $type);
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

            foreach ($employees as $employee) {

                $employee_no = $employee['employee_no'];
                $employee_name = trim($employee['firstname'] . ' ' . $employee['lastname']);
                $employee_position = $employee['position_name'];
                $employee_salary = round(floatval($employee['salary']), 2);

                $cash_gift = 0;

                if($type == 'year_end') {
                    $cash_gift = OtherEarnings::where('code', 'cashgift')->value('amount') ?? 0;
                }
                
                $bonus = $employee_salary;
                $tax = $this->payrollService->computeBonusTax($bonus, $cash_gift);
                $net = ($bonus + $cash_gift) - $tax;

                $data[] = [
                    'payroll_id' => $payroll->id,
                    'employee_no' => $employee_no,
                    'employment_type' => $employee['employment_type_id'],
                    'name' => $employee_name,
                    'position' => $employee_position,
                    'basic_salary' => $employee_salary,
                    'bonus' => $employee_salary,
                    'cash_gift' => $cash_gift,
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
                    'employment_type' => $employee['employment_type_id'],
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