<?php

namespace App\Livewire\Employee\Leave;

use App\Models\EmployeeLeave;
use App\Models\EmployeeLeaveCard;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{

    use WithPagination;

    public $selected_id;
    public $user_id;
    protected $listeners = ['remove'];

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $status = '';

    public function mount() {
        $user_id = Auth::user()->employee_no;

        if(is_null($user_id)) {
            return redirect()->route('employee.leave');
        }

        $this->user_id = $user_id;
    }

    public function download(int $leave_id) {
        
        $employee_no = $this->user_id;
    
        // Fetch leave record with employee details
        $records = EmployeeLeave::with('employee.personal', 'employee.positions')->where('employee_no', $employee_no)
            ->where('id', $leave_id)
            ->first();
    
        // Template file path
        $template = public_path('templates/forms/HRMS-PD Form 03.xlsx');
    
        // Check if template file exists
        if (!file_exists($template)) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops', 
                'message' => 'File does not exists!'
            ]);
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


            // Get holidays from the database
            $holidays = Holiday::pluck('date')->map(function ($date) {
                return Carbon::createFromFormat('m-d', $date)->format('m-d'); // Normalize to MM-DD
            })->toArray();

            // Generate a period of dates between 'from' and 'to'
            $period = $to ? CarbonPeriod::create($from, $to) : CarbonPeriod::create($from, $from);
            $dates = [];

            foreach ($period as $date) {
                $formattedDate = $date->format('m-d'); // Extract MM-DD format
                $dayOfWeek = $date->format('D'); // Get day of the week (Sat/Sun)

                // Skip weekends and holidays
                if ($dayOfWeek !== 'Sat' && $dayOfWeek !== 'Sun' && !in_array($formattedDate, $holidays)) {
                    $dates[] = $date->format('m/d/y'); // Keep only valid dates
                }
            }

            $leaveCardBalance = $this->getLeaveCard();
            $currentTimestamp = Carbon::now()->format('F Y');

            if($records->leave_id == 1) {
                $vl_latest = $leaveCardBalance->vl_bal;
                $vl_coveredBal = round(count($dates), 3);
                $vl_bal = $vl_latest - $vl_coveredBal;
            } else if($records->leave_id == 2) {
                $sl_latest = $leaveCardBalance->sl_bal;
                $sl_coveredBal = round(count($dates), 3);
                $sl_bal = $sl_latest - $sl_coveredBal;
            }

            $sheet->setCellValue('F42', $currentTimestamp ?? '');

            $sheet->setCellValue('F45', $vl_latest ?? 0);
            $sheet->setCellValue('F46', $vl_coveredBal ?? 0);
            $sheet->setCellValue('F47', $vl_bal ?? 0);

            $sheet->setCellValue('G45', $sl_latest ?? 0);
            $sheet->setCellValue('G46', $sl_coveredBal ?? 0);
            $sheet->setCellValue('G47', $sl_bal ?? 0);

            // Set the days covered and list of dates
            $sheet->setCellValue('E33', count($dates) . ($daysCovered > 1 ? ' days' : ' day'));
            $sheet->setCellValue('E35', implode(', ', $dates));
    
            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
            }, ucwords($records->employee->personal->lastname) . '_Leave_Application' .  '.xlsx');
        
        } catch (\Exception $e) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops', 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    private function getLeaveCard() {

        $employee_no = $this->user_id;

        $currentMonth = strtoupper(Carbon::now()->format('F'));
        $currentYear = Carbon::now()->year;


        $leaveCardBalance = EmployeeLeaveCard::where('employee_no', $employee_no)
            ->where('period', $currentMonth)
            ->where('year', $currentYear)
            ->orderBy('year', 'asc')
            ->first();

        return $leaveCardBalance;
    }

    public function remove(bool $isNotify = true, int $id = null) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to delete your leave application <b>#' . strtoupper(format_id($id, 6)) . '</b>. Once this action is completed, it cannot be undone or reversed!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = EmployeeLeave::find($this->selected_id);
                
            if($record) {
                
                $record->delete();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Leave Application #' . strtoupper(format_id($record->id, 6)) . ' deleted successfully.' 
                ]);
            } else {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!', 
                    'isRemoveRowDT' => false,
                    'message' => 'Error: ID does not exists' 
                ]);
            }
        }
    }

    public function render()
    {
        
        $model = EmployeeLeave::where('employee_no', $this->user_id);

        if ($this->status) {
            $records = $model->where('status', $this->status);
        }

        $records = $model->latest()->paginate($this->entries);

        return view('livewire.employee.leave.index', [
            'records' => $records
        ]);
    }
}
