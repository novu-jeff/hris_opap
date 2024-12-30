<?php

namespace App\Livewire\Admin\Settings\Users;

use App\Livewire\Admin\Hris\Index as HrisIndex;
use App\Models\ApplicantUsers;
use App\Models\EmployeeInformation;
use App\Models\JobApplicants;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $type = 'applicants';
    public $selected_id;
    public $user_information;
    protected $listeners = ['remove'];

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';

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
            $message = 'Please be informed that you are about to delete this '.substr($this->type, 0, -1).'. Once this action is processed, it cannot be undone or reversed!';
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


        if($this->type === 'applicants') {
            $model = ApplicantUsers::query();
        }

        if($this->type === 'employees') {
            $model = EmployeeInformation::with('personal', 'account');
        }

        if($this->type === 'admin') {
            $model = User::query();
        }

        if ($this->search) {

            $this->resetPage(); 


            if($this->type === 'applicants') {
                $records = $model->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%'])
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            }

            if($this->type === 'employees') {
                $records = $model->whereHas('personal', function($query) {
                    $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                })
                ->orWhereHas('account', function($query) {
                    $query->where('email', 'like', '%' . $this->search . '%');
                });
                    
            }

        }

        $records = $model->latest()->paginate($this->entries);

        return view('livewire.admin.settings.users.index', [
            'records' => $records
        ]);
    }
}
