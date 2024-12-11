<?php

namespace App\Livewire\Employee\Atro;

use App\Models\EmployeeAtro;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{

    public object $records;
    public $selected_id;
    protected $listeners = ['remove'];


    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $user_id = Auth::user()->employee_no;

        if(is_null($user_id)) {
            return redirect()->route('employee.leave');
        };

        $records = EmployeeAtro::where('employee_no', $user_id)
            ->get();
        
        $this->records = $records;
    }

    public function remove(bool $isNotify = true, int $id = null) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
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
                
                $record->delete();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
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


    public function render()
    {
        return view('livewire.employee.atro.index');
    }
}
