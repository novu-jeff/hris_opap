<?php

namespace App\Http\Controllers\Admin\Services\Payroll;

use App\Http\Controllers\Admin\Services\PayrollService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Services\OtherServices;
use App\Http\Controllers\Admin\Services\LeaveCardService;
use App\Jobs\PayrollJob;
use App\Models\EmployementTypes;
use App\Models\PayrollEme;
use App\Models\PayrollEmeItems;
use App\Services\ContributionsService;
use App\Services\SummaryServices;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\DailyTimeRecordService;
use App\Models\Loan;
use App\Models\Positions;
use App\Models\Tranche;
use Illuminate\Support\Facades\Log;

class EmeService extends Controller {

    protected $product;
    protected $bsd_emp_identical;
    protected $payrollService;

    public function __construct(PayrollService $payrollService) {
        $this->product = config('app.product');
        $this->bsd_emp_identical = config('app.bsd_emp_identical');
        $this->payrollService = $payrollService;
    }

    public function getPayroll(int $payroll_id) {

        $payroll = PayrollEme::with('items.information.section', 'employment_type')->findOrFail($payroll_id);

        $payroll->formatted_payroll_date = Carbon::parse($payroll->payroll_date)->format('F d, Y');

       /* if(!is_null($payroll->cut_off_period)) {
            [$startPeriod, $endPeriod] = explode(' to ', $payroll->cut_off_period);
            $payroll->formatted_cutoff_period = sprintf(
                '%s - %s',
                Carbon::parse(trim($startPeriod))->format('M d, Y'),
                Carbon::parse(trim($endPeriod))->format('M d, Y')
            );
        } else {
            $payroll->formatted_cutoff_period = null;
        }*/

        $employmentType = EmployementTypes::find($payroll->employment_type);
        $payroll->formatted_employment_type = $employmentType->name ?? '';

        $payroll->no_employees = $payroll->items
            ->pluck('employee_no')
            ->unique()
            ->count();

       



        $netAmount = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->net_amount));
        $emeAmount = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->eme));

        $payroll->overall_net_amount = round($netAmount, 2);
        $payroll->overall_eme_amount = round($emeAmount, 2);
        $payroll->type = 'EME';

        //$payroll->overall_salary = round($overallSalary, 2);
      //  $payroll->overall_net_amount = round($overallNetAmount, 2);
       // $payroll->type = 'Salary Payroll';

        $grouped = [];

        foreach ($payroll->items as $item) {

            $section = $item->information->section ?? null;
        
            $sectionId = $section->id ?? 0;
            $sectionName = $section->name ?? 'Unassigned Section';
        
            if (!isset($grouped[$sectionId])) {
                $grouped[$sectionId] = [
                    'section_id' => $sectionId,
                    'section_name' => $sectionName,
                    'employees' => [],
                ];
            }
        
            $grouped[$sectionId]['employees'][] = $item->toArray();
        }

        $items = array_values($grouped);

        return [
            'payroll' => $payroll->toArray() ?? [],
            'payroll_items' => $items,
            'batch_id' => $payroll->batch_id ?? null,
        ];
    
    }

    public function rules(array $payload)
    {
        return [
            'payroll_date' => [
                'required',
                'date',
            ],
            'employment_type' => 'exists:employment_types,id'
        ];
    }




    public function createPayroll($payload) {
        //dd($payload);
        $payroll = PayrollEme::create([
            'payroll_date' => $payload['payroll_date'],
            'cut_off_period' => $payload['cut_off_period'] ?? false,
            'employment_type' => $payload['employment_type'],
            'hasDeductions' => $payload['has_deductions'] ?? false,
            'selected_employees' => json_encode($payload['selected_employees'] ?? []),
            'status' => 'pending'
        ]);

        return $payroll;

    }

    public function generateChunks(int $payroll_id, int $employment_type, string $type) {

        $payroll = PayrollEme::findOrFail($payroll_id);

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

       // dd( $chunks );

        foreach ($chunks as $chunk) {
            Log::info('EME chuck data', ['chuck' => $chunk]);
            $jobs[] = new PayrollJob(
                                    $chunk,          // already an array
                                    $payroll->id,    // pass only ID
                                    'eme'
                                );
        }

        $payroll_date = Carbon::parse($payroll->payroll_date)->format('M d, Y');

        if(!empty($jobs)) {
            return [
                'status' => 'success',
                'jobs' => $jobs,
                'name' => 'Payroll For ' . $payroll_date,
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

        $hasDeductions = $payroll->hasDeductions ?? false;

     


        if ($this->product == 'government') {

            


            $other_service = new OtherServices;
            $dtr_service = new DailyTimeRecordService;
            $leaveCard_service = new LeaveCardService;
            $payroll_service = app(PayrollService::class);

            $data = [];

            foreach ($employees as $employee) {
                $employee_no = $employee['employee_no'];
                $name = trim($employee['firstname'] . ' ' . $employee['lastname']);
                $position = $employee['position_name'];
                $position_id = $employee['position_id'];
                $employment_type_id = $employee['employment_type_id'];
                $eligible = $employee['employment_type_id'];
                $basic_salary = round(floatval($employee['salary']), 2);
                $salary_type = $employee['salary_type'];
               // $gw_tax = $employee['w_tax'];
                

              
                $earnings = $other_service->earnings($employee_no);
               

                // Earnings
                $eme = round(floatval(collect($earnings)->firstWhere('code', 'EME')['amount'] ?? 0), 2);
                
                $net = round($eme, 2);
               
                
              //  $firstHalf  = floor(($net / 2) * 100) / 100;
              //  $secondHalf = round($net - $firstHalf, 2);

                $data[] = [
                    'payroll_id' => $payroll->id,
                    'employee_no' => $employee_no,
                    'employment_type_id' => $employment_type_id,
                    'name' => $name,
                    'position' => $position,
                    'basic_salary' => $basic_salary,
                    'eme' => $eme ?? 0,
                    'net_amount' => $net ?? 0,

                    
                ];
            }

            Log::info('Data to save in payroll items', ['data' => $data]);

            return $data;

        

        }

    
    }


}