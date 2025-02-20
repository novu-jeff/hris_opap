<?php

namespace App\Livewire\Employee\BusinessSlip;

use App\Models\EmployeeBusinessSlip;
use App\Models\EmployeeLeave;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Index extends Component
{

    use WithPagination;

    public $user_id;
    public $selected_id;
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

    public function download(int $id) {
        
        $employee_no = $this->user_id;
    
        // Fetch leave record with employee details
        $records = EmployeeBusinessSlip::with('employee.personal', 'employee.section', 'employee.positions')->where('employee_no', $employee_no)
            ->where('id', $id)
            ->first();
    
        // Template file path
        $template = public_path('templates/forms/HRMS-PD Form 02.xlsx');
    
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

            $spreadsheet = IOFactory::load($template);
            $sheet = $spreadsheet->getActiveSheet();
    
            $lastname = $records->employee->personal->lastname;
            $firstname = $records->employee->personal->firstname;

            // FIRST

            $sheet->setCellValue('A5', $records->employee->section->name ?? '');
            $sheet->setCellValue('G5', Carbon::parse($records->date_filed)->format('F d, Y') ?? '');
            $sheet->setCellValue('A7', $lastname ?? '');
            $sheet->setCellValue('D7', $firstname ?? '');
            $sheet->setCellValue('G7', $records->employee->personal->middlename ?? '');
            $sheet->setCellValue('H7', $records->employee->positions->name ?? '');
            $sheet->setCellValue('A9', $records->destination ?? '');
            $sheet->setCellValue('F9', $records->purpose ?? '');
            $sheet->setCellValue('A11', 'DEPARTURE TIME: ' . Carbon::parse($records->departure_time)->format('g:i A') ?? '');
            $sheet->setCellValue('F11', 'ARRIVAL TIME: ' . Carbon::parse($records->arrival_time)->format('g:i A') ?? '');
            $sheet->setCellValue('A13', $firstname . ' ' . $lastname ?? '');

            // SECOND

            $sheet->setCellValue('A20', $records->employee->section->name ?? '');
            $sheet->setCellValue('G20', Carbon::parse($records->date_filed)->format('F d, Y') ?? '');
            $sheet->setCellValue('A22', $lastname ?? '');
            $sheet->setCellValue('D22', $firstname ?? '');
            $sheet->setCellValue('G22', $records->employee->personal->middlename ?? '');
            $sheet->setCellValue('H22', $records->employee->positions->name ?? '');
            $sheet->setCellValue('A24', $records->destination ?? '');
            $sheet->setCellValue('F24', $records->purpose ?? '');
            $sheet->setCellValue('A26', 'DEPARTURE TIME: ' . Carbon::parse($records->departure_time)->format('g:i A') ?? '');
            $sheet->setCellValue('F26', 'ARRIVAL TIME: ' . Carbon::parse($records->arrival_time)->format('g:i A') ?? '');
            $sheet->setCellValue('A28', $firstname . ' ' . $lastname ?? '');

            // THIRD

            $sheet->setCellValue('A35', $records->employee->section->name ?? '');
            $sheet->setCellValue('G35', Carbon::parse($records->date_filed)->format('F d, Y') ?? '');
            $sheet->setCellValue('A37', $lastname ?? '');
            $sheet->setCellValue('D37', $firstname ?? '');
            $sheet->setCellValue('G37', $records->employee->personal->middlename ?? '');
            $sheet->setCellValue('H37', $records->employee->positions->name ?? '');
            $sheet->setCellValue('A39', $records->destination ?? '');
            $sheet->setCellValue('F39', $records->purpose ?? '');
            $sheet->setCellValue('A41', 'DEPARTURE TIME: ' . Carbon::parse($records->departure_time)->format('g:i A') ?? '');
            $sheet->setCellValue('F41', 'ARRIVAL TIME: ' . Carbon::parse($records->arrival_time)->format('g:i A') ?? '');
            $sheet->setCellValue('A43', $firstname . ' ' . $lastname ?? '');


            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
            }, ucwords($records->employee->personal->lastname) . '_Business_Slip' .  '.xlsx');
        
        } catch (\Exception $e) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops', 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function remove(bool $isNotify = true, int $id = null) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to delete your application for rendering overtime <b>#' . strtoupper(format_id($id, 6)) . '</b>. Once this action is completed, it cannot be undone or reversed!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = EmployeeBusinessSlip::find($this->selected_id);
                
            if($record) {
                
                $record->delete();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'OBS application #' . strtoupper(format_id($record->id, 6)) . ' has been deleted successfully.' 
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

        $model = EmployeeBusinessSlip::where('employee_no', $this->user_id);

        if ($this->status) {
            $records = $model->where('status', $this->status);
        }

        $records = $model->latest()->paginate($this->entries);

        return view('livewire.employee.business-slip.index', [
            'records' => $records
        ]);
    }
}
