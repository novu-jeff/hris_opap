<?php

namespace App\Livewire\Employee\TimeAdjustments;

use App\Models\EmployeeTimeAdjustments;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

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
            return redirect()->route('employee.time-adjustments');
        }

        return $this->user_id = $user_id;
    }

    public function remove(bool $isNotify = true, ? int $id = null) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to delete your request timelog application <b>#' . strtoupper(format_id($id, 6)) . '</b>. Once this action is completed, it cannot be undone or reversed!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = EmployeeTimeAdjustments::find($this->selected_id);
                
            if($record) {
                
                $record->isDeleted = true;
                $record->save();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Time adjustments #' . strtoupper(format_id($record->id, 6)) . ' has been deleted successfully.' 
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
            $message = 'Please be informed that you are about to cancel your request timelog application <b>#' . strtoupper(format_id($id, 6)) . '</b>. Once this action is completed, it cannot be undone or reversed!';
            $action = 'cancel';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = EmployeeTimeAdjustments::find($this->selected_id);
                
            if($record) {
                
                $record->status = 'cancelled';
                $record->save();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Time adjustments #' . strtoupper(format_id($record->id, 6)) . ' has been cancelled successfully.' 
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
        
        $query = EmployeeTimeAdjustments::where('employee_no', $this->user_id)
            ->where('isDeleted', false);

        $status = $this->status === 'granted' ? 'approved' : $this->status;

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $records = $query->latest()->paginate($this->entries);

        return view('livewire.employee.time-adjustments.index', [
            'records' => $records
        ]);
    }
}
