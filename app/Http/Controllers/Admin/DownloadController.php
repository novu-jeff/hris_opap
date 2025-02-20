<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use App\Models\EmployementTypes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function index(Request $request) {
        
 
        $data = $this->getData($request->all());

        if($data instanceof \Illuminate\Http\JsonResponse || $data instanceof \Symfony\Component\HttpFoundation\StreamedResponse) {
            return $data;
        }

        return view('admin.download.template.employee', compact('data'));
    }

    public function getData(array $payload) {

        $target = $payload['show'];

        switch($target) {
            case 'employee':
                return $this->getEmployee($payload);
                break;
        }

    }

    public function getEmployee($payload) {

        $model = EmployeeInformation::with([
            'employment_type',
            'account',
            'section',
            'personal',
            'education',
            'parents',
            'children',
            'employment_history',
            'civil_service',
            'trainings',
            'others',
            'skills',
            'positions',
        ]);
        
        if (!empty($payload['employee_no'])) {
            $model = $model->where('employee_no', $payload['employee_no']);
        }

        if (isset($payload['toPDS']) && $payload['toPDS'] == 'true') {
            $data = $model->first();
            return $this->toPDS($data);
        }
        
        return $model->get();

    }

    public function toPDS($data) {

        $template = public_path('templates/forms/PDS.xlsx');
    
        if (!file_exists($template)) {
            return response()->json(['error' => 'Error: PDS template does not exist!'], 404);
        }

        $data = $data->toArray();

        try {
            // Load the spreadsheet template
            $spreadsheet = IOFactory::load($template);
            
            $c1 = $spreadsheet->getSheet(0);
            $c2 = $spreadsheet->getSheet(1);
            $c3 = $spreadsheet->getSheet(2);
            $checkbox = $spreadsheet->getSheet(3);

            // Prepare response to download the Excel file
            
            // FORMAT PERSONAL
            $data['personal']['email'] = $data['account']['email'];
            $this->formatPersonal($c1, $checkbox, $data['personal']);

            //  FAMILY
            $this->formatFamily($c1, $data['parents']);

            // CHILDREN
            $this->formatChildren($c1, $data['children']);

            // EDUCATION
            $this->formatEducation($c1, $data['education']);
            
            // CIVIL SERVICE
            $this->formatCS($c2, $data['civil_service']);
    
            // EMPLOYMENT HISTORY
            $this->formatEH($c2, $data['employment_history']);

            // OTHER VOLUNTARY WORKS
            $this->formatOthers($c3, $data['others']);

            // SKILLS AND HOBBIES
            $this->formatSkills($c3, $data['skills']);

            // TRAININGS
            $this->formatTrainings($c3, $data['trainings']);

            $response = new StreamedResponse(function () use ($spreadsheet) {
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
            });

            $fileName = 'pds_' . strtolower($data['employee_no'])  . '_' .  time() . '.xlsx';
    
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            $response->headers->set('Cache-Control', 'max-age=0'); 

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error processing the pds template: ' . $e->getMessage()], 500);
        }

    }
    
    public function formatPersonal($sheet, $checkbox, $data) {
        $sheet->setCellValue('D10', strtoupper($data['lastname']));
        $sheet->setCellValue('D11', strtoupper($data['firstname']));
        $sheet->setCellValue('D12', strtoupper($data['middlename']));
        // $sheet->setCellValue('L11', strtoupper($data['suffix']));
        $sheet->setCellValue('D13', strtoupper(Carbon::parse($data['birthday'])->format('F d, Y')));

        if($data['citizenship'] == 'filipino') {
            $checkbox->setCellValue('C1', 'TRUE');
        } else {
            $checkbox->setCellValue('D1', 'TRUE');
            // $checkbox->setCellValue('E1', strtoupper($data['country']));
        }

        if($data['citizenship_type'] == 'by_birth') {
            $checkbox->setCellValue('E1', 'TRUE');
        } else {
            $checkbox->setCellValue('F1', 'TRUE');
        }

        if($data['sex'] == 'male') {
            $checkbox->setCellValue('A1', 'TRUE');
        } else {
            $checkbox->setCellValue('B1', 'TRUE');
        }

        if($data['civil_status'] == 'single') {
            $checkbox->setCellValue('G1', 'TRUE');
        } else if($data['civil_status'] == 'married') {
            $checkbox->setCellValue('H1', 'TRUE');
        } else if($data['civil_status'] == 'widowed') {
            $checkbox->setCellValue('I1', 'TRUE');
        } else if($data['civil_status'] == 'seperated') {
            $checkbox->setCellValue('J1', 'TRUE');
        }

        $sheet->setCellValue('D22', strtoupper($data['height']));
        $sheet->setCellValue('D24', strtoupper($data['weight']));
        $sheet->setCellValue('D25', strtoupper($data['blood_type']));
        $sheet->setCellValue('D27', strtoupper($data['gsis_no']));
        $sheet->setCellValue('D29', strtoupper($data['pagibig_no']));
        $sheet->setCellValue('D31', strtoupper($data['philhealth_no']));
        $sheet->setCellValue('D32', strtoupper($data['sss_no']));
        $sheet->setCellValue('D33', strtoupper($data['tin_no']));
        $sheet->setCellValue('D34', strtoupper($data['employee_no']));

        $sheet->setCellValue('I17', strtoupper($data['present_address']));
        $sheet->setCellValue('I22', strtoupper($data['present_city']));
        $sheet->setCellValue('L22', strtoupper($data['present_province']));

        $sheet->setCellValue('I25', strtoupper($data['permanent_address']));
        $sheet->setCellValue('J29', strtoupper($data['permanent_city']));
        $sheet->setCellValue('M29', strtoupper($data['permanent_province']));

        $sheet->setCellValue('I34', strtoupper($data['email']));
        $sheet->setCellValue('I33', strtoupper($data['mobile_number']));
        $sheet->setCellValue('I32', strtoupper($data['tel_no']));

        return $sheet;
    }

    public function formatFamily($sheet, $data) {
        
        if(!is_null($data)) {
            $sheet->setCellValue('D36', strtoupper($data['spouse_surname']) ?? '');
            $sheet->setCellValue('D37', strtoupper($data['spouse_firstname']) ?? '');
            $sheet->setCellValue('D38', strtoupper($data['spouse_middlename']) ?? '');
            // $sheet->setCellValue('G37', strtoupper($data['spouse_suffix']) ?? '');
            $sheet->setCellValue('D39', strtoupper($data['spouse_occupation']) ?? '');
            $sheet->setCellValue('D40', strtoupper($data['spouse_business_name_employer']) ?? '');
            $sheet->setCellValue('D41', strtoupper($data['spouse_business_address']) ?? '');
            $sheet->setCellValue('D42', strtoupper($data['spouse_contact_no']) ?? '');

            $sheet->setCellValue('D43', strtoupper($data['father_surname']) ?? '');
            $sheet->setCellValue('D44', strtoupper($data['father_firstname']) ?? '');
            $sheet->setCellValue('D45', strtoupper($data['father_middlename']) ?? '');
            // $sheet->setCellValue('G44', strtoupper($data['father_suffix']) ?? '');

            $sheet->setCellValue('D47', strtoupper($data['mother_surname']) ?? '');
            $sheet->setCellValue('D48', strtoupper($data['mother_firstname']) ?? '');
            $sheet->setCellValue('D49', strtoupper($data['mother_middlename']) ?? '');
        }

        return $sheet;
    }

    public function formatChildren($sheet, $data) {
        
        $childrenNameStartCoordinates = 'I37';
        $childrenBdayStartCoordinates = 'M37';

        // Extract the starting row number
        preg_match('/(\D+)(\d+)/', $childrenNameStartCoordinates, $nameMatches);
        preg_match('/(\D+)(\d+)/', $childrenBdayStartCoordinates, $bdayMatches);

        $columnName = $nameMatches[1]; // 'I'
        $columnBday = $bdayMatches[1]; // 'M'
        $startRow = (int)$nameMatches[2]; // 37

        $maxRows = 12; // Maximum number of rows before adding new ones

        foreach ($data as $index => $child) {
            $fullname = $child['firstname'] . ' ' . $child['lastname'];
            $bday = Carbon::parse($child['birthdate'])->format('m-d-y');

            // Calculate current row based on index
            $currentRow = $startRow + $index;

            // Set cell values for name and birthday
            $sheet->setCellValue("{$columnName}{$currentRow}", strtoupper($fullname));
            $sheet->setCellValue("{$columnBday}{$currentRow}", strtoupper($bday));

            // Check if the limit is reached, then insert a new row
            if ($index + 1 >= $maxRows) {
                $startRow++; // Shift starting row down for additional entries
            }
        }

        return $sheet;
    }

    public function formatEducation($sheet, $data) {
        foreach($data as $item) {
            if($item['level'] == 'elementary') {
                $sheet->setCellValue('D54', strtoupper($item['school_name']));
                $sheet->setCellValue('G54', strtoupper($item['course']));
                $sheet->setCellValue('J54', Carbon::parse($item['from_year'])->format('m/d/y'));
                $sheet->setCellValue('K54', Carbon::parse($item['to_year'])->format('m/d/y'));
            }
    
            if($item['level'] == 'secondary' || $item['level'] == 'highschool' || $item['level'] == 'senior_highschool') {
                $sheet->setCellValue('D55', strtoupper($item['school_name']));
                $sheet->setCellValue('G55', strtoupper($item['course']));
                $sheet->setCellValue('J55', Carbon::parse($item['from_year'])->format('m/d/y'));
                $sheet->setCellValue('K55', Carbon::parse($item['to_year'])->format('m/d/y'));
            }
    
            if($item['level'] == 'vocational') {
                $sheet->setCellValue('D56', strtoupper($item['school_name']));
                $sheet->setCellValue('G56', strtoupper($item['course']));
                $sheet->setCellValue('J56', Carbon::parse($item['from_year'])->format('m/d/y'));
                $sheet->setCellValue('K56', Carbon::parse($item['to_year'])->format('m/d/y'));
            }
    
            if($item['level'] == 'college') {
                $sheet->setCellValue('D57', strtoupper($item['school_name']));
                $sheet->setCellValue('G57', strtoupper($item['course']));
                $sheet->setCellValue('J57', Carbon::parse($item['from_year'])->format('m/d/y'));
                $sheet->setCellValue('K57', Carbon::parse($item['to_year'])->format('m/d/y'));
            }

            
            if($item['level'] == 'masters' || $item['level'] =='doctoral') {
                $sheet->setCellValue('D58', strtoupper($item['school_name']));
                $sheet->setCellValue('G58', strtoupper($item['course']));
                $sheet->setCellValue('J58', Carbon::parse($item['from_year'])->format('m/d/y'));
                $sheet->setCellValue('K58', Carbon::parse($item['to_year'])->format('m/d/y'));
            }
        }

        return $sheet;
        

    }

    public function formatCS($sheet, $data) {
        // Column starting points
        $careerStart = 'A5';
        $ratingStart = 'F5';
        $dateExamStart = 'G5';
        $placeExamStart = 'I5';
        $numberStart = 'L5';
        $validityStart = 'M5';
    
        // Extract the starting row number for each column
        preg_match('/(\D+)(\d+)/', $careerStart, $careerMatches);
        preg_match('/(\D+)(\d+)/', $ratingStart, $ratingMatches);
        preg_match('/(\D+)(\d+)/', $dateExamStart, $dateExamMatches);
        preg_match('/(\D+)(\d+)/', $placeExamStart, $placeExamMatches);
        preg_match('/(\D+)(\d+)/', $numberStart, $numberMatches);
        preg_match('/(\D+)(\d+)/', $validityStart, $validityMatches);
    
        // Assign column letters and starting row
        $careerColumn = $careerMatches[1]; // 'A'
        $ratingColumn = $ratingMatches[1]; // 'F'
        $dateExamColumn = $dateExamMatches[1]; // 'G'
        $placeExamColumn = $placeExamMatches[1]; // 'I'
        $numberColumn = $numberMatches[1]; // 'L'
        $validityColumn = $validityMatches[1]; // 'M'
        $startRow = (int)$careerMatches[2]; // 5
    
        $maxRows = 7; // Maximum number of rows before adding new ones
    
        foreach ($data as $index => $child) {
            // Data for each row
            $certification = $child['certification'] ?? '';
            $rating = $child['rating'] ?? ''; 
            $date_exam = Carbon::parse($child['date_exam'])->format('m-d-y'); // Assuming exam date format
            $place_exam = $child['place_exam'] ?? ''; // Assuming exam place data
            $license_no = $child['license_no'] ?? ''; // Assuming number data
            $date_validity = $child['date_validity'] ?? ''; // Assuming validity data
    
            // Calculate current row based on index
            $currentRow = $startRow + $index;
    
            // Set cell values for all columns
            $sheet->setCellValue("{$careerColumn}{$currentRow}", strtoupper($certification));
            $sheet->setCellValue("{$ratingColumn}{$currentRow}", strtoupper($rating));
            $sheet->setCellValue("{$dateExamColumn}{$currentRow}", strtoupper($date_exam));
            $sheet->setCellValue("{$placeExamColumn}{$currentRow}", strtoupper($place_exam));
            $sheet->setCellValue("{$numberColumn}{$currentRow}", strtoupper($license_no));
            $sheet->setCellValue("{$validityColumn}{$currentRow}", strtoupper($date_validity));
    
            // Check if the limit is reached, then insert a new row
            if (($index + 1) % $maxRows === 0) {
                $sheet->insertNewRowBefore($currentRow + 1, 1); // Insert a new row after every 12 entries
                $startRow++; // Shift starting row down for additional entries
            }
        }
    
        return $sheet;
    }

    public function formatEH($sheet, $data) {
        // Column starting points
        $fromYearStart = 'A18';
        $toYearStart = 'C18';
        $positionStart = 'D18';
        $departmentStart = 'G18';
        $monthlySalaryStart = 'J18';
        $employmentStatusStart = 'L18';
        $isGovernmentStart = 'M18';
    
        // Extract the starting row number for each column
        preg_match('/(\D+)(\d+)/', $fromYearStart, $fromYearMatches);
        preg_match('/(\D+)(\d+)/', $toYearStart, $toYearMatches);
        preg_match('/(\D+)(\d+)/', $positionStart, $positionMatches);
        preg_match('/(\D+)(\d+)/', $departmentStart, $departmentMatches);
        preg_match('/(\D+)(\d+)/', $monthlySalaryStart, $monthlySalaryMatches);
        preg_match('/(\D+)(\d+)/', $employmentStatusStart, $employmentStatusMatches);
        preg_match('/(\D+)(\d+)/', $isGovernmentStart, $isGovernmentMatches);
    
        // Assign column letters and starting row
        $fromYearColumn = $fromYearMatches[1]; // 'A'
        $toYearColumn = $toYearMatches[1]; // 'B'
        $positionColumn = $positionMatches[1]; // 'C'
        $departmentColumn = $departmentMatches[1]; // 'D'
        $monthlySalaryColumn = $monthlySalaryMatches[1]; // 'E'
        $employmentStatusColumn = $employmentStatusMatches[1]; // 'G'
        $isGovernmentColumn = $isGovernmentMatches[1]; // 'H'
        $startRow = (int)$fromYearMatches[2]; // 18
    
        $maxRows = 28; // Maximum number of rows before adding new ones
    
        foreach ($data as $index => $item) {
            // Data for each row
            $position = $item['position'] ?? '';
            $department = $item['department'] .' | ' . $item['company_name'] ?? '';
            $monthlySalary = $item['monthly_salary'] ?? '';
            $employmentStatus = $item['employment_status'] ?? '';
            $isGovernment = $item['isGovernment'] ?? '';
            $fromYear = str_replace('-', '/', $item['from_year']) ?? '';
            $toYear = str_replace('-', '/', $item['to_year']) ?? '';
    
            // Calculate current row based on index
            $currentRow = $startRow + $index;
    
            // Set cell values for all columns
            $sheet->setCellValue("{$fromYearColumn}{$currentRow}", strtoupper($fromYear));
            $sheet->setCellValue("{$toYearColumn}{$currentRow}", strtoupper($toYear));
            $sheet->setCellValue("{$positionColumn}{$currentRow}", strtoupper($position));
            $sheet->setCellValue("{$departmentColumn}{$currentRow}", strtoupper($department));
            $sheet->setCellValue("{$monthlySalaryColumn}{$currentRow}", strtoupper($monthlySalary));
            $sheet->setCellValue("{$employmentStatusColumn}{$currentRow}", strtoupper($employmentStatus));
            $sheet->setCellValue("{$isGovernmentColumn}{$currentRow}", strtoupper($isGovernment ? 'Y' : 'N'));
    
            // Check if the limit is reached, then insert a new row
            if (($index + 1) % $maxRows === 0) {
                $sheet->insertNewRowBefore($currentRow + 1, 1); // Insert a new row after every 28 entries
                $startRow++; // Shift starting row down for additional entries
            }
        }
    
        return $sheet;
    }
    
    public function formatOthers($sheet, $data) {
        // Column starting points
        $organizationStart = 'A6';
        $dateFromStart = 'E6';
        $dateToStart = 'F6';
        $consumedHoursStart = 'G6';
        $positionStart = 'H6';
        
        // Extract the starting row number for each column
        preg_match('/(\D+)(\d+)/', $organizationStart, $organizationMatches);
        preg_match('/(\D+)(\d+)/', $dateFromStart, $dateFromMatches);
        preg_match('/(\D+)(\d+)/', $dateToStart, $dateToMatches);
        preg_match('/(\D+)(\d+)/', $consumedHoursStart, $consumedHoursMatches);
        preg_match('/(\D+)(\d+)/', $positionStart, $positionMatches);
        
        // Assign column letters and starting row
        $organizationColumn = $organizationMatches[1]; // 'A'
        $dateFromColumn = $dateFromMatches[1]; // 'C'
        $dateToColumn = $dateToMatches[1]; // 'D'
        $consumedHoursColumn = $consumedHoursMatches[1]; // 'E'
        $positionColumn = $positionMatches[1]; // 'F'
        $startRow = (int)$organizationMatches[2]; // 18
        
        $maxRows = 7; // Maximum number of rows before adding new ones
        
        foreach ($data as $index => $item) {
            // Data for each row
            $organization = $item['organization'] ?? '';
            $dateFrom = $item['date_from'] ?? '';
            $dateTo = $item['date_to'] ?? '';
            $consumedHours = $item['consumed_hours'] ?? '';
            $position = $item['position'] ?? '';
            
            // Format date fields if they exist
            $dateFrom = !empty($dateFrom) ? str_replace('-', '/', $dateFrom) : '';
            $dateTo = !empty($dateTo) ? str_replace('-', '/', $dateTo) : '';
    
            // Calculate current row based on index
            $currentRow = $startRow + $index;
    
            // Set cell values for all columns
            $sheet->setCellValue("{$organizationColumn}{$currentRow}", strtoupper($organization));
            $sheet->setCellValue("{$dateFromColumn}{$currentRow}", strtoupper($dateFrom));
            $sheet->setCellValue("{$dateToColumn}{$currentRow}", strtoupper($dateTo));
            $sheet->setCellValue("{$consumedHoursColumn}{$currentRow}", strtoupper($consumedHours));
            $sheet->setCellValue("{$positionColumn}{$currentRow}", strtoupper($position));
    
            // Check if the limit is reached, then insert a new row
            if (($index + 1) % $maxRows === 0) {
                $sheet->insertNewRowBefore($currentRow + 1, 1); // Insert a new row after every 28 entries
                $startRow++; // Shift starting row down for additional entries
            }
        }
    
        return $sheet;
    }

    public function formatSkills($sheet, $data) {
        // Column starting points
        $nameStart = 'A42';
        $recognitionStart = 'C42';
        $organizationStart = 'I42';
        
        // Extract the starting row number for each column
        preg_match('/(\D+)(\d+)/', $nameStart, $nameMatches);
        preg_match('/(\D+)(\d+)/', $recognitionStart, $recognitionMatches);
        preg_match('/(\D+)(\d+)/', $organizationStart, $organizationMatches);
        
        // Assign column letters and starting row
        $nameColumn = $nameMatches[1]; // 'A'
        $recognitionColumn = $recognitionMatches[1]; // 'B'
        $organizationColumn = $organizationMatches[1]; // 'C'
        $startRow = (int)$nameMatches[2]; // 6
        
        $maxRows = 7; // Maximum number of rows before adding new ones
        
        foreach ($data as $index => $item) {
            // Data for each row
            $name = $item['name'] ?? '';
            $recognition = $item['recognition'] ?? '';
            $organization = $item['organization'] ?? '';
        
            // Calculate current row based on index
            $currentRow = $startRow + $index;
        
            // Set cell values for all columns
            $sheet->setCellValue("{$nameColumn}{$currentRow}", strtoupper($name));
            $sheet->setCellValue("{$recognitionColumn}{$currentRow}", strtoupper($recognition));
            $sheet->setCellValue("{$organizationColumn}{$currentRow}", strtoupper($organization));
        
            // Check if the limit is reached, then insert a new row
            if (($index + 1) % $maxRows === 0) {
                $sheet->insertNewRowBefore($currentRow + 1, 1); // Insert a new row after every 28 entries
                $startRow++; // Shift starting row down for additional entries
            }
        }
    
        return $sheet;
    }

    public function formatTrainings($sheet, $data) {
        // Column starting points
        $typeStart = 'H18';
        $nameStart = 'A18';
        $dateFromStart = 'E18';
        $dateToStart = 'F18';
        $consumedHoursStart = 'G18';
        $sponsoredByStart = 'I18';
        
        // Extract the starting row number for each column
        preg_match('/(\D+)(\d+)/', $typeStart, $typeMatches);
        preg_match('/(\D+)(\d+)/', $nameStart, $nameMatches);
        preg_match('/(\D+)(\d+)/', $dateFromStart, $dateFromMatches);
        preg_match('/(\D+)(\d+)/', $dateToStart, $dateToMatches);
        preg_match('/(\D+)(\d+)/', $consumedHoursStart, $consumedHoursMatches);
        preg_match('/(\D+)(\d+)/', $sponsoredByStart, $sponsoredByMatches);
        
        // Assign column letters and starting row
        $typeColumn = $typeMatches[1]; // 'A'
        $nameColumn = $nameMatches[1]; // 'B'
        $dateFromColumn = $dateFromMatches[1]; // 'C'
        $dateToColumn = $dateToMatches[1]; // 'D'
        $consumedHoursColumn = $consumedHoursMatches[1]; // 'E'
        $sponsoredByColumn = $sponsoredByMatches[1]; // 'F'
        $startRow = (int)$typeMatches[2]; // 42
        
        $maxRows = 7; // Maximum number of rows before adding new ones
        
        foreach ($data as $index => $item) {
            // Data for each row
            $type = $item['type'] ?? '';
            $name = $item['name'] ?? '';
            $dateFrom = $item['date_from'] ?? '';
            $dateTo = $item['date_to'] ?? '';
            $consumedHours = $item['consumed_hours'] ?? '';
            $sponsoredBy = $item['sponsored_by'] ?? '';
        
            // Format date fields if they exist
            $dateFrom = !empty($dateFrom) ? str_replace('-', '/', $dateFrom) : '';
            $dateTo = !empty($dateTo) ? str_replace('-', '/', $dateTo) : '';
        
            // Calculate current row based on index
            $currentRow = $startRow + $index;
        
            // Set cell values for all columns
            $sheet->setCellValue("{$typeColumn}{$currentRow}", strtoupper($type));
            $sheet->setCellValue("{$nameColumn}{$currentRow}", strtoupper($name));
            $sheet->setCellValue("{$dateFromColumn}{$currentRow}", strtoupper($dateFrom));
            $sheet->setCellValue("{$dateToColumn}{$currentRow}", strtoupper($dateTo));
            $sheet->setCellValue("{$consumedHoursColumn}{$currentRow}", strtoupper($consumedHours));
            $sheet->setCellValue("{$sponsoredByColumn}{$currentRow}", strtoupper($sponsoredBy));
        
            // Check if the limit is reached, then insert a new row
            if (($index + 1) % $maxRows === 0) {
                $sheet->insertNewRowBefore($currentRow + 1, 1); // Insert a new row after every 7 entries
                $startRow++; // Shift starting row down for additional entries
            }
        }
    
        return $sheet;
    }
    

    
    
}
