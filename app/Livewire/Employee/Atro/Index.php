<?php

namespace App\Livewire\Employee\Atro;

use App\Models\EmployeeAtro;
use App\Models\EmployeeAtroRelative;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;
use Illuminate\Support\Str;

class Index extends Component
{

    use WithPagination;

    public $selected_id;
    public $user_id;
    protected $listeners = ['remove', 'cancel'];

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $status = 'all';

    public function mount() {
        $user_id = Auth::user()->employee_no;

        if(is_null($user_id)) {
            return redirect()->route('employee.atro');
        }

        return $this->user_id = $user_id;
    }


    public function remove(bool $isNotify = true, ? int $id = null) {

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

            $record = EmployeeAtro::find($this->selected_id);
                
            if($record) {
                
                $record->isDeleted = true;
                $record->save();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Yey!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Authority to render overtime application #' . strtoupper(format_id($record->id, 6)) . ' has been deleted successfully.' 
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

    public function cancel(bool $isNotify = true, ? int $id = null) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to cancel your application for rendering overtime <b>#' . strtoupper(format_id($id, 6)) . '</b>. Once this action is completed, it cannot be undone or reversed!';
            $action = 'cancel';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = EmployeeAtro::find($this->selected_id);
                
            if($record) {
                
                $record->status = 'cancelled';
                $record->save();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Yey!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Authority to render overtime application #' . strtoupper(format_id($record->id, 6)) . ' has been cancelled successfully.' 
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

    private function getMentionedRecords() {
        $record = EmployeeAtroRelative::with('atro')
            ->where('employee_no', $this->user_id)
            ->whereHas('atro', function($query) {
                $query->where('status', 'pending')
                      ->where('isDeleted', false);
            })
            ->first();

        if ($record) {
            $record->status = 'mentioned';
        }

        return $record;
    }
    
    public function download($id)
    {
        $employee_no = $this->user_id;
    
        $relative = EmployeeAtroRelative::with('atro')
            ->where('employee_no', $employee_no)
            ->first();

        if($relative) {
            $employee_no = $relative->atro->employee_no;
        }
        
        $records = EmployeeAtro::with('personal', 'information.section', 'relative.personal')
            ->where('employee_no', $employee_no)
            ->first();
    
        if (!$records) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Not Found',
                'message' => 'Record not found.',
            ]);
        }
    
        $templatePath = public_path('templates/forms/HRMS-PD Form 05.docx');
    
        if (!file_exists($templatePath)) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops',
                'message' => 'Template file does not exist!',
            ]);
        }
    
        try {
            $templateProcessor = new TemplateProcessor($templatePath);
    
            // Prepare values
            $requesting_unit = $records->information->section->name . ' (' . strtoupper($records->information->section->code) . ')'  ?? '';
            $date_requested = Carbon::parse($records->created_at)->format('F d, Y') ?? '';
            $justification = ucfirst($records->justification) ?? '';
            $date = Carbon::parse($records->date)->format('F d, Y') ?? '';
            $time = Carbon::parse($records->start_time)->format('h:i A') . ' - ' . Carbon::parse($records->end_time)->format('h:i A');
    
            $names = $records->relative->map(function ($relative) {
                return $relative->personal->firstname . ' ' . $relative->personal->lastname . ' (' . $relative->employee_no . ')';
            })->toArray();
    
            $employeeName = ucwords($records->personal->firstname . ' ' . $records->personal->lastname) . ' (' . $records->employee_no . ')';
            array_unshift($names, $employeeName);
    
            $namesText = implode("\n", $names); // Line break for Word
    
            // Set values
            $templateProcessor->setValue('date_requested', $date_requested);
            $templateProcessor->setValue('requesting_unit', $requesting_unit);
            $templateProcessor->setValue('justification', $justification);
            $templateProcessor->setValue('date', $date);
            $templateProcessor->setValue('time', $time);
            $templateProcessor->setValue('names', $namesText);
    
            // Save edited file to temporary path
            $tempFilename = 'temp_' . Str::random(10) . '.docx';
            $tempPath = storage_path('app/public/' . $tempFilename);
    
            $templateProcessor->saveAs($tempPath);
    
            return response()->download($tempPath, now()->format('Ymd_His') . '_ATRO.docx')->deleteFileAfterSend(true);
    
        } catch (\Exception $e) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops',
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }
    

    public function render()
    {
        $query = EmployeeAtro::query()
            ->where('employee_no', $this->user_id)
            ->where('isDeleted', false);
    
        if (!empty($this->status) && $this->status !== 'all') {
            $status = $this->status === 'granted' ? 'approved' : $this->status;
            $query->where('status', $status);
        }

        $records = $query->latest()->get(); 
    
        $mentioned = $this->getMentionedRecords();
        if ($mentioned) {
            $records->push($mentioned);
        }
    
        $records = $records->sortByDesc('created_at');
    
        $currentPage = request()->get('page', 1);
        $perPage = $this->entries;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $records->forPage($currentPage, $perPage),
            $records->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    
        return view('livewire.employee.atro.index', [
            'records' => $paginated,
        ]);
    }
    
}
