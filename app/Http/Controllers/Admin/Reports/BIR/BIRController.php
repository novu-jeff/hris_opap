<?php

namespace App\Http\Controllers\Admin\Reports\BIR;

use App\Http\Controllers\Controller;
use App\Models\CompanyInformation;
use App\Models\EmployeeInformation;
use App\Services\ContributionsService;
use App\Services\Employee1601Service;
use App\Services\Employee2316Service;
use Illuminate\Http\Request;

class BIRController extends Controller
{
    protected $employee2316Service;
    protected $employee1601Service;
    protected $contributionsService;

    public function __construct(
        Employee2316Service $employee2316Service, 
        Employee1601Service $employee1601Service,
        ContributionsService $contributionsService,
        )
    {
        $this->employee2316Service = $employee2316Service;
        $this->employee1601Service = $employee1601Service;
        $this->contributionsService = $contributionsService;
    }

    public function index()
    {
        return view('admin.reports.bir.index');
    }

    public function form2316($id, Request $request) {

        $year = $request->query('year');

        $company_information = CompanyInformation::firstOrFail();

        $employee = EmployeeInformation::with('account', 'personal')
            ->where('id', $id)
            ->firstOrFail();

        $resigned_date = $employee->date_resignation ?? null;
        $date_hired = $employee->date_hired;

        $name = trim("{$employee->personal->firstname} {$employee->personal->middlename} {$employee->personal->lastname}");

        $result = $this->employee2316Service->compute([
            'year' =>  $year,
            'date_hired' => $date_hired,
            'resigned_date' => $resigned_date,
            'name' => $name,
            'tin' => $employee->tin_no,
            'company_name' => $company_information->name,
            'company_address' => $company_information->address,
            'salary' => $employee->monthly_rate,
        ]);

        return view('admin.reports.bir.form-2316', compact('result'));
    }

    public function form1601(Request $request) {
        
        $this->validate($request, [
            'month' => 'required|date_format:Y-m',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
        ]);

        $month = $request->query('month');
        $year = $request->query('year');

        dd($month, $year);

        $total_salary = 0;
        $total_pagibig = 0;
        $total_sss = 0;
        $total_philhealth = 0;
        $count = 0;

        $employees = EmployeeInformation::with('account', 'personal')->get();

        foreach ($employees as $employee) {
            $total_salary += $employee->monthly_rate;
            $count++;

            $philHealth = $this->contributionsService->computePhilHealth($employee->monthly_rate);
            $pagibig = $this->contributionsService->computePagibig($employee->monthly_rate);
            $sss = $this->contributionsService->computeSSS($employee->monthly_rate);

            $total_philhealth += $philHealth['employee_share'];
            $total_pagibig += $pagibig['employee_share'];
            $total_sss += $sss['employee_share'];
        }

        $part2 = $this->employee1601Service->compute([
            'total_salary' => $total_salary,
            'employee_count' => 5,
            'month' => '2025-06',
        ]);

        $part1 = $this->employee1601Service->generateReport([
            'period' => 'March 2025',
            'gross_pay' => $part2['14'],
            'non_taxable_other_income' => $part2['15'],
            'sss' => $total_sss,
            'philhealth' => $total_philhealth,
            'pagibig' => $total_pagibig,
            'wtax_1601c' => 362467.84,
            'expanded_wtax' => 121113.46,
        ]);

        return view('admin.reports.bir.form-1601', compact('part2', 'part1'));
    }
}
