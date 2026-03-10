<?php

namespace App\Http\Controllers\Admin\Services;

use App\Helper\Generate;
use App\Http\Controllers\Controller;
use App\Models\EmployeeAccount;
use App\Models\EmployeeChildren;
use App\Models\EmployeeCivilService;
use App\Models\EmployeeEducation;
use App\Models\EmployeeEmploymentHistory;
use App\Models\EmployeeInformation;
use App\Models\EmployeeOtherWorks;
use App\Models\EmployeeParents;
use App\Models\EmployeePersonal;
use App\Models\EmployeeSkillsHobbies;
use App\Models\EmployeeTrainings;
use App\Models\EmployementTypes;
use App\Models\Positions;
use App\Models\Sections;
use App\Models\Tranche;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EmployeeUploadService extends Controller
{
    private function normalizeHeader($header)
    {
        return strtolower(trim($header));
    }

    public function transformDate($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                ->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function uploadEmployeeInformation(array $rows, array $schedules)
    {
        if (count($rows) === 0) {
            Log::warning('Employee Information sheet is emptyssss');
            return;
        }

        $skippedRows = [];
        $duplicateRows = [];
        $processedRows = 0;

        $expectedHeaders = [
            'employee no.', 'bsd no.', 'lastname', 'firstname', 'middlename',
            'address', 'sex', 'civil status', 'birthday', 'age',
            'bp no', 'gsis id', 'pagibig id', 'sss id', 'philhealth id', 'tin id',
            'bank account no.', 'date hired', 'position', 'monthly salary',
            'job category', 'email', 'unit'
        ];

        $firstRow = array_map(fn ($v) => strtolower(trim((string) $v)), $rows[0]);
        $hasHeaderRow = count(array_intersect($expectedHeaders, $firstRow)) >= 3;

        $columnMap = $hasHeaderRow
            ? array_map(fn ($h) => array_search($h, $firstRow), $expectedHeaders)
            : array_combine($expectedHeaders, range(0, count($expectedHeaders) - 1));

        $startRow = $hasHeaderRow ? 1 : 0;

        for ($i = $startRow; $i < count($rows); $i++) {
            $row = $rows[$i];

            $data = [];
            foreach ($columnMap as $field => $index) {
                $data[$field] = $row[$index] ?? null;
            }

            if (empty($data['employee no.'])) {
                $skippedRows[] = ['row' => $i + 1, 'reason' => 'Missing employee number'];
                Log::warning('Employee upload skipped', ['row' => $i + 1]);
                continue;
            }

            $processedRows++;

            $jobCategoryName = ucfirst(strtolower(trim($data['job category'] ?? '')));
            $positionName = trim($data['position'] ?? '');

            $jobCategory = $jobCategoryName
                ? EmployementTypes::firstOrCreate(['name' => $jobCategoryName])
                : null;

            $position = $positionName
                ? Positions::firstOrCreate(['name' => $positionName])
                : null;

            Log::info('Uploading employee', [
                'row' => $i + 1,
                'employee_no' => $data['employee no.']
            ]);

            $employeeInfo = EmployeeInformation::updateOrCreate(
                ['employee_no' => $data['employee no.']],
                [
                    'bsd_no' => $data['bsd no.'],
                    'bank_account_no' => $data['bank account no.'],
                    'date_hired' => $this->transformDate($data['date hired']),
                    'position_id' => $position?->id,
                    'salary' => $data['monthly salary'],
                    'employment_type_id' => $jobCategory?->id,
                    'email' => $data['email'],
                    'unit' => $data['unit'],
                    'shift_id' => $schedules['shift'] ?? null,
                    'schedule_id' => $schedules['schedule'] ?? null,
                ]
            );

            if (!$employeeInfo->wasRecentlyCreated) {
                $duplicateRows[] = [
                    'row' => $i + 1,
                    'employee_no' => $data['employee no.'],
                    'table' => 'employee_information'
                ];
                Log::notice('Duplicate employee information detected', [
                    'employee_no' => $data['employee no.']
                ]);
            }

            EmployeePersonal::updateOrCreate(
                ['employee_no' => $data['employee no.']],
                [
                    'lastname' => $data['lastname'],
                    'firstname' => $data['firstname'],
                    'middlename' => $data['middlename'],
                    'present_address' => $data['address'],
                    'sex' => strtolower($data['sex'] ?? ''),
                    'civil_status' => strtolower($data['civil status'] ?? ''),
                    'birthday' => $this->transformDate($data['birthday']),
                    'age' => $data['age'],
                    'bp_no' => $data['bp no'],
                    'gsis_no' => $data['gsis id'],
                    'pagibig_no' => $data['pagibig id'],
                    'sss_no' => $data['sss id'],
                    'philhealth_no' => $data['philhealth id'],
                    'tin_no' => $data['tin id'],
                ]
            );

            $this->createAccount(
                $data['employee no.'],
                $data['firstname'],
                $data['lastname'],
                $data['email']
            );
        }

        Log::info('Employee upload completed', [
            'processed' => $processedRows,
            'skipped' => count($skippedRows),
            'duplicates' => count($duplicateRows),
            'skipped_rows' => $skippedRows,
            'duplicate_rows' => $duplicateRows,
        ]);
    }

    private function createAccount($employeeNo, $firstName, $lastName, $email)
    {
        $generate = new Generate;
        $email_id = $generate->email($employeeNo, $firstName, $lastName);

        // If an account already exists for this employee number, don't update it
        $user = EmployeeAccount::where('employee_no', $employeeNo)->first();

        if ($user) {
            Log::notice('Existing employee account found, skipping update', [
                'employee_no' => $employeeNo,
            ]);
        } else {
            $user = EmployeeAccount::create([
                'employee_no' => $employeeNo,
                'email' => $email,
                'email_id' => $email_id,
                'password' => bcrypt('password'),
            ]);
        }

        $user->assignRole('employee');
    }
}
