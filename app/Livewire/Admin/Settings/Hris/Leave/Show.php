<?php

namespace App\Livewire\Admin\Settings\Hris\Leave;

use App\Models\EmployeeInformation;
use App\Models\LeaveCredits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public $id;
    public $entries = 10;
    public $search = '';
    public $credits = [];

    protected $paginationTheme = 'bootstrap';

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

   
        $employees = EmployeeInformation::with(['personal'])
            ->get();
            
        // Retrieve the leave credits based on the leave type ID
        $leaveCredits = LeaveCredits::where('leave_type_id', $this->id)->get();

        // Initialize the credits array
        $this->credits = [];

        // Loop through the employees
        foreach ($employees as $employee) {
            // Find the leave credit matching the employee's employee_no
            $leaveCredit = $leaveCredits->firstWhere('employee_no', $employee['employee_no']);
            
            // If a matching leave credit is found, set the credits value, otherwise set it to 0
            $this->credits[$employee['employee_no']] = $leaveCredit ? $leaveCredit->credits : 0;
        }
    }
    
    public function save()
    {

        if (Gate::denies('write leave-credits')) {
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
            // Iterate through the credits array and update or create leave credits for each employee
            foreach ($this->credits as $employeeId => $credit) {
                if ($employeeId && $credit !== null) {
                    if($credit != 0) {
                        LeaveCredits::updateOrCreate(
                            [
                                'employee_no' => $employeeId,
                                'leave_type_id' => $this->id,
                            ],
                            [
                                'credits' => $credit ?? 0,
                            ]
                        );
                    }  
                }
            }

            DB::commit();

            // Success alert after saving the credits
            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Saved!',
                'showAlert' => true,
                'message' => 'Leave credits updated successfully.',
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

    // Render method to display the records with pagination and search functionality
    public function render()
    {
        $model = EmployeeInformation::with(['personal']);

        // Apply search filters
        if ($this->search) {
            $this->resetPage();
            $model->where('employee_no', 'like', '%' . $this->search . '%')
                ->orWhereHas('personal', function ($query) {
                    $query->where('firstname', 'like', '%' . $this->search . '%')
                        ->orWhere('lastname', 'like', '%' . $this->search . '%');
                });
        }

        $records = $model->paginate($this->entries);

        return view('livewire.admin.settings.hris.leave.show', [
            'records' => $records,
        ]);
    }
}
