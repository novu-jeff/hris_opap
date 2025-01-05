<?php

namespace App\Livewire\Admin\Settings\Hris\EmpDeductions;

use App\Models\EmployeeDeductions;
use App\Models\EmployeeInformation;
use App\Models\OtherDeductions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $id;
    public $entries = 10;
    public $search = '';
    public $deductions = [];

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['remove'];

    public function mount() {
        $this->loadRecords();
    }


    public function loadRecords() {

   
        $employees = EmployeeInformation::with(['personal'])
            ->get();
            
        // Retrieve the leave deductions based on the leave type ID
        $deductions = EmployeeDeductions::where('deduction_id', $this->id)->get();

        // Initialize the deductions array
        $this->deductions = [];

        // Loop through the employees
        foreach ($employees as $employee) {
            // Find the leave credit matching the employee's employee_no
            $leaveCredit = $deductions->firstWhere('employee_no', $employee['employee_no']);
            
            // If a matching leave credit is found, set the deductions value, otherwise set it to 0
            $this->deductions[$employee['employee_no']] = $leaveCredit ? $leaveCredit->amount : 0;
        }
    }

    public function save() {
        
        if (Gate::denies('write employee-deductions')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            // Iterate through the deductions array and update or create leave deductions for each employee
                        
            foreach ($this->deductions as $employeeId => $deduction) {
                if ($employeeId && $deduction !== null) {
                    $record = EmployeeDeductions::updateOrCreate(
                        [
                            'employee_no' => $employeeId,
                            'deduction_id' => $this->id,
                        ],
                        [
                            'amount' => $deduction ?? 0,
                        ]
                    ); 
                }
            }

            DB::commit();

            $deduction = OtherDeductions::find($this->id);

            $action = $record->wasRecentlyCreated ? 'added' : 'updated';

            // Success alert after saving the deductions
            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Saved!',
                'showAlert' => true,
                'message' => 'Deduction for ' . $deduction->name . ' was ' . $action . '.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Error alert if something goes wrong
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Error!',
                'showAlert' => true,
                'message' => $e->getMessage(),
            ]);
        }

    }

    public function render()
    {

        $model = EmployeeInformation::with(['personal']);


        if ($this->search) {

            $this->resetPage(); 

            $records = $model->where('employee_no', 'like', '%' . $this->search . '%')
                ->orWhere('amount', 'like', '%' . $this->search . '%')
                ->orWhereHas('personal', function($query) {
                    $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
        }

        $records = $model->paginate($this->entries);

        return view('livewire.admin.settings.hris.emp-deductions.index', [
            'records' => $records
        ]);
    }
}
