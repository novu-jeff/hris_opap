<?php

namespace App\Livewire\Admin\Ess\Leave;

use App\Models\EmployeeAccount;
use App\Models\EmployeeLeave;
use App\Models\LeaveCredits;
use App\Models\LeaveType;
use App\Notifications\Notifications;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
 
    use WithPagination;

    public $status;
    public $view_records;
    public $selected_id;
    public $activeTab = 'pending';
    protected $listeners = ['remove', 'rejected', 'granted'];

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';

    public function view(int $id) {
        $this->selected_id = $id;
        $this->loadRecords($id);
        if(!is_null($this->view_records)) {
            return $this->dispatch('showModal', [
                'modal' => 'showModal', 
            ]);
        }
    }

    public function loadRecords(int $id) {
        $this->view_records = EmployeeLeave::with('employment', 'employee', 'leave_type')
            ->where('id', $id)
            ->first();
    }

    public function rejected(bool $isNotify = true) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to reject this leave application <b>#' . strtoupper(format_id($this->selected_id, 6)) . '</b>. Once this action is processed, it cannot be undone or reversed!';
            $action = 'rejected';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        } else {

            $record = EmployeeLeave::where('id', $this->selected_id)
                ->where('status', 'pending')
                ->first();

            $record->status = 'rejected';
            $record->save();

            $this->dispatch('alert', [
                'id' => $this->selected_id,
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Success', 
                'isRemoveRowDT' => true,
                'message' => 'Application has been rejected'
            ]);


            $user = EmployeeAccount::where('employee_no', $record->employee_no)->first();
            $user?->notify(new Notifications('error', 'You\'re leave application <strong>#' . format_id($record->id, 6) . '</strong> was <strong>REJECTED</strong>.', route('employee.leave'), 'employee'));
        }
    }

    public function granted(bool $isNotify = true) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to grant this leave application <b>#' . strtoupper(format_id($this->selected_id, 6)) . '</b>. Once this action is processed, it cannot be undone or reversed!';
            $action = 'granted';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        } else {

            $record = EmployeeLeave::with('employment')->where('id', $this->selected_id)
                ->where('status', 'pending')
                ->first();
                
            if(is_null($record)) {
                return redirect()->route('ess.leave');
            }

            $from = Carbon::parse($record->from);
            $to = Carbon::parse($record->to);

            if ($to) {
                $daysCovered = $from->diffInDays($to) + 1; 
            } else {
                $daysCovered = 1; 
            }

            $leaveCreditsModel = LeaveCredits::class;
            $leaveTypeModel = LeaveType::find($record->leave_id);

            $leaveCredits = $leaveCreditsModel::where('leave_type_id', $record->leave_id)
                    ->where('employee_no', $record->employee_no)
                    ->first();
            
            // if no credits left
            if(is_null($leaveCredits) || $leaveCredits->credits == 0) {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops', 
                    'message' => 'Unfortunately, you have no credits left for <b>' . $leaveTypeModel->name . '</b>.'
                ]);
            } 

            // if leave days covered is greater than leave credits remaining
            if($daysCovered > $leaveCredits->credits) {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops', 
                    'message' => 'Unfortunately, you have insufficient leave credits. You\'re applying to leave for '.$daysCovered.' days(s) but only have ' . $leaveCredits->credits . ' remaining leave credits.'
                ]);
            }
            
            // deduct leave credits

            $leaveCredits->credits -= $daysCovered;
            $leaveCredits->save();

            // Update the EmployeeLeave record's status
            $record->update([
                'status' => 'granted'
            ]);
        
            $this->dispatch('alert', [
                'id' => $this->selected_id,
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Success', 
                'isRemoveRowDT' => true,
                'message' => 'Application has been granted'
            ]);

            $user = EmployeeAccount::where('employee_no', $record->employee_no)->first();
            $user?->notify(new Notifications('success', 'You\'re leave application <strong>#' . format_id($record->id, 6) . '</strong> was <strong>APPROVED</strong>. Click this notification to view more details.', route('employee.leave'), 'employee'));

            return;

        }
    }

    public function remove(bool $isNotify = true, int $id = null) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to delete this leave application <b>#' . strtoupper(format_id($id, 6)) . '</b>. Once this action is processed, it cannot be undone or reversed!';
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
                
                $user = EmployeeAccount::where('employee_no', $record->employee_no)->first();
                $user?->notify(new Notifications('error', 'You\'re leave application <strong>#' . format_id($record->id, 6) . '</strong> was <strong>REMOVED</strong>. Click this notification to view more details.', route('employee.leave'), 'employee'));

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
       
        $model = EmployeeLeave::with('employment', 'employee', 'leave_type')
            ->where('status', $this->status);

        if ($this->search) {

            $this->resetPage(); 

            $records = $model->where(function ($query) {
                $query->where('employee_no', 'like', '%' . $this->search . '%')
                ->orWhereHas('employee', function ($subQuery) {
                    $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
            });
        }

        $records = $model->latest()->paginate($this->entries);

        return view('livewire.admin.ess.leave.index', [
            'records' => $records
        ]);
    }
}
