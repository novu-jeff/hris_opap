<?php

namespace App\Livewire\Admin\Settings\Users;

use App\Livewire\Admin\Hris\Index as HrisIndex;
use App\Models\ApplicantUsers;
use App\Models\EmployeeInformation;
use App\Models\JobApplicants;
use App\Models\User;
use Livewire\Component;

class Index extends Component
{

    public $type = 'applicants';
    public $records;
    public $selected_id;
    public $user_information;
    protected $listeners = ['remove'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        if($this->type === 'applicants') {
            $record = ApplicantUsers::all();
        }

        if($this->type === 'employees') {
            $record = EmployeeInformation::with('personal', 'account')->get();
        }

        if($this->type === 'admin') {
            $record = User::all();
        }

        $this->records = $record;
    }

    public function view_user(int $id) {
        if($this->type == 'applicants') {
            $record =  $record = ApplicantUsers::with(['applied.job'])
                ->where('id', $id);

            if($record) {
                $this->user_information = $record->first();
                $this->dispatch('showModal', [
                    'modal' => 'user_info'
                ]);
            }
        } else if($this->type == 'employees') {
            session()->put('target', [
                'id' => $id,
            ]);
            return redirect()->route('hris.index');
        }
    }

    public function remove($isNotify = true, int $id = null) {
        
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

            switch ($this->type) {
                case 'applicants':
                    $record = ApplicantUsers::find($this->selected_id);
                    break;
                case 'employees':
                    $record = EmployeeInformation::find($this->selected_id);
                    break;
                default:
                    break;
            }
                
            if($record) {
                
                $record->delete();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'User ' . strtoupper($record->firstname . ' ' . $record->lastname) . ' deleted successfully' 
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
        return view('livewire.admin.settings.users.index');
    }
}
