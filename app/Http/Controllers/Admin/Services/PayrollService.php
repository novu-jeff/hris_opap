<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use App\Models\EmployeeLeave;
use App\Models\EmployeeTimelogs;
use App\Models\EmployementTypes;
use App\Models\Payroll;
use App\Models\GSISBilling;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService extends Controller {

    public function getEmployeesPreview($employment_type)
    {
        $employees = [
            'eligible' => [],
            'ineligible' => [],
        ];

        $results = DB::table('employee_information as ei')
            ->select(
                'ei.id as employee_id',
                'ei.employee_no',
                'ei.employment_type_id',
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

        foreach ($results as $row) {
            $reasons = [];

            if (empty($row->bsd_no)) {
                $reasons[] = 'no bsd';
            }

            if (empty($row->position_id)) {
                $reasons[] = 'no position';
            }

            $employeeData = [
                'employee_no' => $row->employee_no,
                'name'   => $row->firstname . ' ' . $row->lastname,
                'status' => count($reasons) > 0 ? 'ineligible' : 'eligible',
                'reason' => count($reasons) > 0 ? $reasons : null,
            ];

            if ($employeeData['status'] === 'eligible') {
                $employees['eligible'][] = $employeeData;
            } else {
                $employees['ineligible'][] = $employeeData;
            }
        }

        return $employees;
    }

    public function getEmployees($employment_type) {
        $query = DB::table('employee_information as ei')
            ->select(
                'ei.id as employee_id',
                'ei.employee_no',
                'ei.employment_type_id',
                'ei.monthly_rate',
                'ei.bsd_no',
                'p.firstname',
                'p.lastname',
                'p.gsis_no',
                'po.name as position_name',
                'po.salary_grade',
                'po.w_tax',
                's.name as section_name'
            )
            ->join('employee_personal as p', 'ei.employee_no', '=', 'p.employee_no')
            ->join('positions as po', 'ei.position_id', '=', 'po.id')
            ->leftJoin('sections as s', 'ei.section_id', '=', 's.id')
            ->where('ei.employment_type_id', $employment_type)
            ->whereNotNull('ei.position_id')
            ->whereNotNull('ei.bsd_no');

        $employees = $query->get();

        return $employees;
    }

    public function getPayroll($payroll_id)
    {
        $payroll = Payroll::with('items.information.section')->findOrFail($payroll_id);

        $payroll->formatted_payroll_date = Carbon::parse($payroll->payroll_date)->format('F d, Y');

        [$startPeriod, $endPeriod] = explode(' to ', $payroll->cut_off_period);
        $payroll->formatted_cutoff_period = sprintf(
            '%s - %s',
            Carbon::parse(trim($startPeriod))->format('M d, Y'),
            Carbon::parse(trim($endPeriod))->format('M d, Y')
        );

        $employmentType = EmployementTypes::find($payroll->employment_type);
        $payroll->formatted_employment_type = $employmentType->name ?? '';

        $payroll->no_employees = $payroll->items->count();

        $netAmount = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->net_amount));
        $salaryAmount = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->salary));

        $payroll->overall_net_amount = round($netAmount, 2);
        $payroll->overall_salary_amount = round($salaryAmount, 2);

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

    private function convertMinsToMoney($salary, $workedDays, $aut)
    {
        \Log::info('aut', [
            'salary' => $salary,
            'workedDays' => $workedDays,
            'aut' => $aut
        ]);
        if ($workedDays <= 0) return 0;
        return ($salary / $workedDays / 8 / 60) * $aut;
    }

}