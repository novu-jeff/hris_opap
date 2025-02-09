<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeave;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaveController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read apply-leave')->only('index');
        $this->middleware('permission:write apply-leave')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('employee.leave', [
            'action' => 'view',
            'title' => 'ESS | Leave Applications',
            'header' => 'Manage Leaves',
            'sub' => 'Track and monitor your leave applications.'
        ]);
    }

    public function show(int $leave_id) {
        
        $employee_no = Auth::user()->employee_no;
    
        // Fetch leave record with employee details
        $records = EmployeeLeave::with('employee.personal', 'employee.positions')->where('employee_no', $employee_no)
            ->where('id', $leave_id)
            ->first();
    
        // Template file path
        $template = public_path('templates/ess/leave_application.xlsx');
    
        // Check if template file exists
        if (!file_exists($template)) {
            return response()->json(['error' => 'Error: Leave template does not exist!'], 404);
        }
    
        try {
            // Load the spreadsheet template
            $spreadsheet = IOFactory::load($template);
            $sheet = $spreadsheet->getActiveSheet();
    
            // Set employee information
            $sheet->setCellValue('G9', $records->employee->personal->lastname ?? '');
            $sheet->setCellValue('I9', $records->employee->personal->firstname ?? '');
            $sheet->setCellValue('N9', $records->employee->personal->middlename ?? '');
            $sheet->setCellValue('O11', $records->employee->monthly_rate ?? '');
            $sheet->setCellValue('H11', $records->employee->positions->name ?? '');

            // Set created date
            $sheet->setCellValue('F11', Carbon::parse($records->created_at)->format('m/d/y'));
    
            // Define leave type mapping
            $leaveType = [
                1 => 'C17',
                2 => 'C18',
                3 => 'C19',
                4 => 'C20',
                5 => 'C21',
                6 => 'C22',
                7 => 'C23',
                8 => 'C24',
                9 => 'C25',
                10 => 'C26',
                11 => 'C27',
                12 => 'C28',
                13 => 'C29',
            ];
    
            // Set leave type
            if (isset($leaveType[$records->leave_id])) {
                $sheet->setCellValue($leaveType[$records->leave_id], '/');
            }
    
            // Handle specific leave types with additional conditions
            if($records->leave_id == 1 || $records->leave_id == 6) {
                if($records->location == 'ph') {
                    $sheet->setCellValue('J18', '/');
                    $sheet->setCellValue('N18', $records->location_specific ?? '');
                } else {
                    $sheet->setCellValue('J19', '/');
                    $sheet->setCellValue('N19', $records->location_specific ?? '');
                }
            }
    
            if($records->leave_id == 3) {
                if($records->confinement == 'hospital') {
                    $sheet->setCellValue('J18', '/');
                    $sheet->setCellValue('N18', $records->illness ?? '');
                } else {
                    $sheet->setCellValue('J21', '/');
                    $sheet->setCellValue('N21', $records->illness ?? '');
                }
            }
    
            if($records->leave_id == 8) {
                if($records->study == 'completion_masters') {
                    $sheet->setCellValue('J26', '/');
                } elseif($records->study == 'examination') {
                    $sheet->setCellValue('J27', '/');
                } else {
                    $sheet->setCellValue('M28', $records->study_other_purpose ?? '');
                }
            }
    
            // Handle commutation
            $sheet->setCellValue($records->commutation == 'yes' ? 'J34' : 'J33', '/');
    
            // Calculate the number of days covered
            $from = Carbon::parse($records->from);
            $to = isset($records->to) ? Carbon::parse($records->to) : null;
            $daysCovered = $to ? $from->diffInDays($to) + 1 : 1;
    
            // Generate a period of dates between 'from' and 'to'
            $period = $to ? CarbonPeriod::create($from, $to) : CarbonPeriod::create($from, $from);
            $dates = [];
    
            foreach ($period as $date) {
                $dates[] = $date->format('m/d/y');
            }
    
            // Set the days covered and list of dates
            $sheet->setCellValue('E33', $daysCovered . ($daysCovered > 1 ? ' days' : ' day'));
            $sheet->setCellValue('E35', implode(', ', $dates));
    
            // Prepare response to download the Excel file
            $response = new StreamedResponse(function () use ($spreadsheet) {
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
            });
    
            $fileName = 'leave_application_' . $leave_id . '_' . time() . '.xlsx';
    
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        
            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error processing the leave application template: ' . $e->getMessage()], 500);
        }
    }
    

    public function create()
    {
        return view('employee.leave', [
            'action' => 'create',
            'title' => 'Apply Leave',
            'header' => 'Leave Application',
            'sub' => 'By proceeding, you\'ll be able to apply for a leave.'
        ]);

    }

    public function edit(int $id)
    {
        return view('employee.leave', [
            'id' => $id,
            'action' => 'edit',
            'title' => 'Edit Leave',
            'header' => 'Edit Application',
            'sub' => 'Feel free to edit or update your leave application.'
        ]);

    }


}
