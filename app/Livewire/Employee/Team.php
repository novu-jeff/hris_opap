<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeInformation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Team extends Component
{

    public $records;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $user_id = Auth::user()->employee_id;
        $user = EmployeeInformation::find($user_id);

        if(!$user) {
            return redirect()->route('employee.dashboard');
        }

        $branch_id = $user->branch_id;
        $department_id = $user->department_id;

        $records = EmployeeInformation::with('branch', 'department', 'positions', 'personal', 'account')
            ->where('branch_id', $branch_id)
            ->where('department_id', $department_id)->get();

        $groupedRecords = [
            'branch' => [
                'branch_id' => $branch_id,
                'branch_name' => $records->first()->branch->name ?? 'Unknown Branch',
            ],
            'department' => [
                'department_id' => $department_id,
                'department_name' => $records->first()->department->name ?? 'Unknown Department',
            ],
            'positions' => []
        ];
        
        foreach ($records as $record) {
            $positionId = $record->position_id;
            $positionName = $record->positions->name ?? 'Unknown Position';
        
            if (!isset($groupedRecords['positions'][$positionId])) {
                $groupedRecords['positions'][$positionId] = [
                    'position_id' => $positionId,
                    'position_name' => $positionName,
                    'employees' => []
                ];
            }
        
            $groupedRecords['positions'][$positionId]['employees'][] = $record->toArray();
        }
        
        $groupedRecords['positions'] = array_values($groupedRecords['positions']);
        
        return $this->records = $groupedRecords; 
    }

    public function render()
    {
        return view('livewire.employee.team');
    }
}
