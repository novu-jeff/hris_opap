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
    protected $listeners = ['remove', 'cancel'];

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $status = 'pending';

    public function mount() {
        $user_id = Auth::user()->employee_no;

        if(is_null($user_id)) {
            return redirect()->route('employee.leave');
        }

        $this->user_id = $user_id;
    }

    public function download(int $leave_id) {
        
        $employee_no = $this->user_id;
    
        $records = EmployeeLeave::with('employee.personal', 'employee.positions')->where('employee_no', $employee_no)
            ->where('id', $leave_id)
            ->first();
    
        $template = public_path('templates/forms/HRMS-PD Form 03.xlsx');
    
        if (!file_exists($template)) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops', 
                'message' => 'File does not exists!'
            ]);
        }
    
        try {
            $spreadsheet = IOFactory::load($template);
            $sheet = $spreadsheet->getActiveSheet();
    
            $sheet->setCellValue('G9', strtoupper($records->employee->personal->lastname) ?? '');
            $sheet->setCellValue('I9', strtoupper($records->employee->personal->firstname) ?? '');
            $sheet->setCellValue('N9', strtoupper($records->employee->personal->middlename) ?? '');
            $sheet->setCellValue('O11', strtoupper($records->employee->monthly_rate) ?? '');
            $sheet->setCellValue('H11', strtoupper($records->employee->positions->name) ?? '');

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
    
            if (isset($leaveType[$records->leave_id])) {
                $sheet->setCellValue($leaveType[$records->leave_id], '/');
            }
    
            if($records->leave_id == 1 || $records->leave_id == 6) {
                if($records->location == 'ph') {
                    $sheet->setCellValue('J18', '/');
                    $sheet->setCellValue('N18', strtoupper($records->location_specific) ?? '');
                } else {
                    $sheet->setCellValue('J19', '/');
                    $sheet->setCellValue('N19', strtoupper($records->location_specific) ?? '');
                }
            }
    
            if($records->leave_id == 3) {
                if($records->confinement == 'hospital') {
                    $sheet->setCellValue('J18', '/');
                    $sheet->setCellValue('N18', strtoupper($records->illness) ?? '');
                } else {
                    $sheet->setCellValue('J21', '/');
                    $sheet->setCellValue('N21', strtoupper($records->illness) ?? '');
                }
            }
    
            if($records->leave_id == 8) {
                if($records->study == 'completion_masters') {
                    $sheet->setCellValue('J26', '/');
                } elseif($records->study == 'examination') {
                    $sheet->setCellValue('J27', '/');
                } else {
                    $sheet->setCellValue('M28', strtoupper($records->study_other_purpose) ?? '');
                }
            }
    
            $sheet->setCellValue($records->commutation == 'YES' ? 'J34' : 'J33', '/');
    
            $from = Carbon::parse($records->from);
            $to = isset($records->to) ? Carbon::parse($records->to) : null;
            $daysCovered = $to ? $from->diffInDays($to) + 1 : 1;


            $holidays = Holiday::pluck('date')->map(function ($date) {
                return Carbon::createFromFormat('m-d', $date)->format('m-d'); // Normalize to MM-DD
            })->toArray();

            $period = $to ? CarbonPeriod::create($from, $to) : CarbonPeriod::create($from, $from);
            $dates = [];

            foreach ($period as $date) {
                $formattedDate = $date->format('m-d'); 
                $dayOfWeek = $date->format('D'); 

                if ($dayOfWeek !== 'Sat' && $dayOfWeek !== 'Sun' && !in_array($formattedDate, $holidays)) {
                    $dates[] = $date->format('m/d/y');
                }
            }

            $leaveCardBalance = $this->getLeaveCard();
            $currentTimestamp = Carbon::now()->format('F Y');

            if($records->leave_id == 1) {
                $vl_latest = $leaveCardBalance->vl_bal;
                $vl_covered = number_format($daysCovered, 2);
                $vl_bal = $vl_latest - $vl_covered;
            } else if($records->leave_id == 2) {
                $sl_latest = $leaveCardBalance->sl_bal;
                $sl_covered = number_format($daysCovered, 2);
                $sl_bal = $sl_latest - $sl_covered;
            }

            $sheet->setCellValue('F42', $currentTimestamp ?? '');

            $sheet->setCellValue('F45', $vl_latest ?? 0);
            $sheet->setCellValue('F46', $vl_covered ?? 0);
            $sheet->setCellValue('F47', $vl_bal ?? 0);

            $sheet->setCellValue('G45', $sl_latest ?? 0);
            $sheet->setCellValue('G46', $sl_covered ?? 0);
            $sheet->setCellValue('G47', $sl_bal ?? 0);

            $sheet->setCellValue('E33', $daysCovered . ($daysCovered > 1 ? ' days' : ' day'));
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
                
                $record->isDeleted = true;
                $record->save();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Leave Application #' . strtoupper(format_id($record->id, 6)) . ' was deleted successfully.' 
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

    public function cancel(bool $isNotify = true, int $id = null) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to cancel your leave application <b>#' . strtoupper(format_id($id, 6)) . '</b>. Once this action is completed, it cannot be undone or reversed!';
            $action = 'cancel';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = EmployeeLeave::find($this->selected_id);
                
            if($record) {
                
                $record->status = 'cancelled';
                $record->save();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Leave Application #' . strtoupper(format_id($record->id, 6)) . ' was cancelled successfully.' 
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
        $query = EmployeeLeave::where('employee_no', $this->user_id)
            ->where('isDeleted', false);

        $status = $this->status === 'granted' ? 'approved' : $this->status;

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $records = $query->latest()->paginate($this->entries);

        return view('livewire.employee.leave.index', [
            'records' => $records
        ]);
    }

}
