<?php

namespace App\Http\Controllers\Admin\Reports\BIR;

use App\Http\Controllers\Controller;
use App\Models\CompanyInformation;
use App\Models\EmployeeInformation;
use App\Services\ContributionsService;
use App\Services\Employee1601Service;
use App\Services\Employee2316Service;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
            'salary' => $employee->salary,
        ]);

        
        return $this->saveForm2316(data: $result);

    }

    public function form1601(Request $request) {
        
        $this->validate($request, [
            'month' => 'required|date_format:Y-m',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
        ]);

        $month = $request->query('month');
        $year = $request->query('year');

        $total_salary = 0;
        $total_pagibig = 0;
        $total_sss = 0;
        $total_philhealth = 0;
        $count = 0;

        $employees = EmployeeInformation::with('account', 'personal')->get();

        foreach ($employees as $employee) {
            $total_salary += $employee->salary;
            $count++;

            $philHealth = $this->contributionsService->computePhilHealth($employee->salary);
            $pagibig = $this->contributionsService->computePagibig($employee->salary);
            $sss = $this->contributionsService->computeSSS($employee->salary);

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

    public function saveForm2316($data)
    {
        $template = public_path('templates/forms/BIR/BIR 2316.xlsx');

        if (!file_exists($template)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Template file does not exist!'
            ], 404);
        }

        try {
            $spreadsheet = IOFactory::load($template);
            
            $sheet = $spreadsheet->getActiveSheet();
            $cellMappings = [
                ['G11:L12', 'G11', $data['1'] ?? ''],
                ['AA11:AD12', 'AA11', $data['2']['from'] ?? ''],
                ['AJ11:AM12', 'AJ11', $data['2']['to'] ?? ''],
                ['D14:S15', 'D14', $data['3'] ?? ''],
                ['A17:P18', 'A17', $data['4'] ?? ''],
                ['R17:T18', 'R17', $data['4'] ?? ''],
                ['A20:P21', 'A20', $data['5'] ?? ''],
                ['R20:T21', 'R20', $data['6A'] ?? ''], 
                ['A24:P25', 'A24', $data['6B'] ?? ''],
                ['R24:T25', 'R24', $data['6C'] ?? ''],
                ['B27:S28', 'B27', $data['6D'] ?? ''],
                ['B30:C31', 'B30', $data['7'] ?? ''],
                ['K30:T31', 'K30', $data['8'] ?? ''],
                ['N32:S34', 'N32', $data['9'] ?? ''],
                ['N35:S37', 'N35', $data['10'] ?? ''],
                ['B44:S45', 'B44', $data['13'] ?? ''],
                ['B47:P48', 'B47', $data['14'] ?? ''],
                ['R47:T48', 'R47', $data['14A'] ?? ''],
                ['B55:S56', 'B55', $data['17'] ?? ''],
                ['B58:P59', 'B58', $data['18'] ?? ''],
                ['R58:T59', 'R58', $data['18A'] ?? ''],
                ['N61:S62', 'N61', $data['19'] ?? ''],
                ['N63:S64', 'N63', $data['20'] ?? ''],
                ['N65:S66', 'N65', $data['21'] ?? ''],
                ['N67:S68', 'N67', $data['22'] ?? ''],
                ['N69:S70', 'N69', $data['23'] ?? ''],
                ['N71:S72', 'N71', $data['24'] ?? ''],
                ['N73:S74', 'N73', $data['25A'] ?? ''],
                ['N75:S76', 'N75', $data['25B'] ?? ''],
                ['N77:S78', 'N77', $data['26'] ?? ''],
                ['N79:S79', 'N79', $data['27'] ?? ''],
                ['N80:S80', 'N80', $data['28'] ?? ''],
            ];

            foreach ($cellMappings as [$mergeRange, $cell, $value]) {
                $sheet->mergeCells($mergeRange);
                $sheet->setCellValue($cell, $value);
            }

            $filename = strtolower(str_replace(' ', '_', $data['4'] . '-bir-2316-' . now()->format('Ymd_His')));
            
            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
            }, $filename . '.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.xlsx"',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate the form: ' . $e->getMessage()
            ], 500);
        }
    }



}
