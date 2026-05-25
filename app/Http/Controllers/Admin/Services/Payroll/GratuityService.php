<?php

namespace App\Http\Controllers\Admin\Services\Payroll;

use App\Http\Controllers\Admin\Services\PayrollService;
use App\Http\Controllers\Admin\Services\OtherServices;
use App\Http\Controllers\Controller;
use App\Jobs\Payroll\SalaryJob;
use App\Jobs\PayrollJob;
use App\Models\PayrollGratuity;
use App\Models\EmployementTypes;
use App\Models\OtherEarnings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GratuityService extends Controller {

    protected $product;
    protected $bsd_emp_identical;
    protected $payrollService;

    public function __construct(PayrollService $payrollService) {
        $this->product = config('app.product');
        $this->bsd_emp_identical = config('app.bsd_emp_identical');
        $this->payrollService = $payrollService;
    }

    public function getPayroll(int $payroll_id) {

        $payroll = PayrollGratuity::with('items.information.section', 'employment_type')->findOrFail($payroll_id);

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

        $gratuity_pay = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->gratuity_pay));
        $tax = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->tax));
        $net_amount = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->net_amount));

        $payroll->total_gratuity_pay = round($gratuity_pay, 2);
        $payroll->total_tax = round($tax, 2);
        $payroll->total_net_amount = round($net_amount, 2);

        
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
        

        $employmentType = $payload['employment_type'];
       

        if (PayrollGratuity::where([
            'payroll_date'    => $payload['payroll_date'],
            'employment_type' => $employmentType,
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

        $payroll = PayrollGratuity::create([
            'payroll_date'       => $payload['payroll_date'],
            'employment_type'    => $employmentType,
            'remarks'            => $payload['remarks'] ?? null,
            'selected_employees' => json_encode(
                $payload['selected_employees'] ?? []
            ),
            'status'             => 'pending',
        ]);

        return $payroll;
    }

    public function generateChunks(int $payroll_id, int $employment_type, string $type) {

        $payroll = PayrollGratuity::findOrFail($payroll_id);

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
                'gratuity'
            );
        }

        $payroll_date = Carbon::parse($payroll->payroll_date)->format('M d, Y');

     
        $name = 'Gratuity For ' . $payroll_date;
    


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
        $other_service = new OtherServices;
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
    
                $employee_position =
                    $employee['position_name'] ?? '';
    
                $date_hired =
                    $employee['date_hired'] ?? null;
    
                $employment_type_id =
                    $employee['employment_type_id'];
    
                $basicSalary = round(
                    floatval($employee['salary'] ?? 0),
                    2
                );
    
                $earnings = $other_service->earnings($employee_no);
                $gratuity_pay = round(
                    floatval(
                        collect($earnings)
                            ->firstWhere('code', 'GRATUITY')['amount'] ?? 0
                    ),
                    2
                );
                
                /*
                |--------------------------------------------------------------------------
                | Computation
                |--------------------------------------------------------------------------
                */
                
                $tax = round($gratuity_pay * 0.05, 2);
                
                $net = round($gratuity_pay - $tax, 2);
    
                $data[] = [
    
                    'payroll_id' =>
                        $payroll->id,
    
                    'employee_no' =>
                        $employee_no,
    
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
    
                    'remarks' => null,
    
                    'date_hired' =>
                        $date_hired,
                    
                    'gratuity_pay' =>
                        $gratuity_pay,
    
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