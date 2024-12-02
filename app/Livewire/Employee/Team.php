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

        $user_id = Auth::user()->employee_no;

        $user = EmployeeInformation::where('employee_no', $user_id)->first();

        if(!$user) {
            return redirect()->route('employee.dashboard');
        }

        $branch_id = $user->section_id->branch_id ?? null;
        $department_id = $user->section_id->department_id ?? null;

        $records = EmployeeInformation::with('section', 'positions', 'personal', 'account')->get();

        $groupedRecords = [
            'branch' => [
                'branch_id' => $branch_id,
                'branch_name' => $records->first()->section->branch->name ?? 'Unknown Branch',
            ],
            'department' => [
                'department_id' => $department_id,
                'department_name' => $records->first()->section->department->name ?? 'Unknown Department',
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
