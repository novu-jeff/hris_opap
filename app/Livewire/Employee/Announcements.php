<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeAnnouncements;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Announcements extends Component
{

    use WithPagination;

    public $record_id;
    public $user_id;

    protected $paginationTheme = 'bootstrap';
    public $entries = 6;
    public $search = '';

    public $view;

    public function mount() {
        $user_id = Auth::user()->employee_no;

        if(is_null($user_id)) {
            return redirect()->route('employee.leave');
        }

        $this->loadRecords();

        return $this->user_id = $user_id;
    }

    public function loadRecords() {
        $records = EmployeeAnnouncements::class;
        if(!is_null($this->record_id)) {
            $records = $records::where('id', $this->record_id)->first();

            if(!$records) {
                return redirect()->route('employee.announcements.index');
            }

            return $this->view = $records;
        }

    }

    public function render()
    {

        $model = EmployeeAnnouncements::query();

        if ($this->search) {
            $this->resetPage(); 
            $model->where('title', 'like', '%' . $this->search . '%');
        }

        $records = $model->latest()->paginate($this->entries);

        return view('livewire.employee.announcements', [
            'records' => $records
        ]);
    }
}
