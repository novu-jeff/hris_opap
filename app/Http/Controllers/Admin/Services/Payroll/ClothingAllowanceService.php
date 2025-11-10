<?php

namespace App\Http\Controllers\Admin\Services\Payroll;

use App\Http\Controllers\Admin\Services\PayrollService;
use App\Http\Controllers\Controller;
use App\Jobs\Payroll\SalaryJob;
use App\Jobs\PayrollJob;
use App\Models\ClothingAllowancePayroll;
use App\Models\EmployementTypes;
use App\Models\OtherEarnings;
use Carbon\Carbon;

class ClothingAllowanceService extends Controller {

    protected $product;
    protected $bsd_emp_identical;
    protected $payrollService;

    public function __construct(PayrollService $payrollService) {
        $this->product = config('app.product');
        $this->bsd_emp_identical = config('app.bsd_emp_identical');
        $this->payrollService = $payrollService;
    }

    public function getPayroll(int $payroll_id) {

        $payroll = ClothingAllowancePayroll::with('items.information.section', 'employment_type')->findOrFail($payroll_id);

        $payroll->formatted_payroll_date = Carbon::parse($payroll->payroll_date)->format('F Y');

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

        $allowance = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->allowance));
        $payroll->total_allowances = round($allowance, 2);
        $payroll->type = 'Clothing Allowance Payroll';

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
        if (ClothingAllowancePayroll::where([
            'payroll_date' => $payload['payroll_date'],
            'employment_type' => $payload['employment_type'],
        ])->exists()) {
            throw new \Exception('Payroll for this period and employment type already exists.');
        }

        $payroll = ClothingAllowancePayroll::create([
            'payroll_date' => $payload['payroll_date'],
            'employment_type' => $payload['employment_type'],
            'status' => 'pending'
        ]);

        return $payroll;

    }

    public function generateChunks(int $payroll_id, int $employment_type, string $type) {

        $payroll = ClothingAllowancePayroll::findOrFail($payroll_id);

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
            $jobs[] = new PayrollJob(collect($chunk), $payroll, 'clothing_allowance');
        }

        $payroll_date = Carbon::parse($payroll->payroll_date)->format('M Y');

        if(!empty($jobs)) {
            return [
                'status' => 'success',
                'jobs' => $jobs,
                'name' => 'Clothing Allowance For ' . $payroll_date,
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
                $date_hired = $employee['date_hired'] ?? null;

                $allowance = OtherEarnings::where('code', 'Clothing')->first()
                    ->amount ?? 0;

                $employee_salary = round(floatval($allowance), 2);

                $data[] = [
                    'payroll_id' => $payroll->id,
                    'employee_no' => $employee_no,
                    'name' => $employee_name,
                    'position' => $employee_position,
                    'allowance' => $employee_salary,
                    'date_hired' => $date_hired,
                ];
            }

            return $data;

        } else {

        }


    }

}