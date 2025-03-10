<?php

namespace App\Livewire\Admin\Payroll;

use App\Models\EmployeeInformation;
use App\Models\Payroll;
use Livewire\Component;
use Livewire\WithPagination;

class Choose extends Component
{

    use WithPagination;

    public $payroll_id;

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        

    }

    public function render()
    {

        $model = EmployeeInformation::with('personal')
            ->where('isDeleted', false);

        if ($this->search) {

            $this->resetPage(); 

            $employees = $model->where(function ($query) {
                $query->where('employee_no', 'like', '%' . $this->search . '%')
                ->orWhereHas('personal', function ($subQuery) {
                    $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
            });
        } else {
            $employees = $model;
        }

        $employees = $employees->latest()->paginate($this->entries);

        return view('livewire.admin.payroll.choose', [
            'employees' => $employees
        ]);

    }
}
