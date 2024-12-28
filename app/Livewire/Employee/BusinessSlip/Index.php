<?php

namespace App\Livewire\Employee\BusinessSlip;

use App\Models\EmployeeBusinessSlip;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

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

        return $this->user_id = $user_id;
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
