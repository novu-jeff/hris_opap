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
    public $nextAndPrev;

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

            $this->getPreviousNextAnnouncements($records->id);

            return $this->view = $records;
        }

    }

    public function getPreviousNextAnnouncements($id) {

        $currentJobId = EmployeeAnnouncements::where('id', $id)->value('id');

        $prev = EmployeeAnnouncements::select('id')->where('id', '<', $currentJobId)
            ->orderBy('id', 'desc')
            ->first();

        $next =  EmployeeAnnouncements::select('id')->where('id', '>', $currentJobId)
            ->orderBy('id', 'asc')
            ->first();
        
        return $this->nextAndPrev = [
            'prev' => !is_null($prev) ? route('employee.announcements.view', ['id' => $prev['id']]) : null,
            'next' => !is_null($next) ? route('employee.announcements.view', ['id' => $next['id']]) : null
        ];

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
