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

        $user = EmployeeInformation::with('section.department', 'section.branch')->where('employee_no', $user_id)->first();

        if(!$user) {
            return redirect()->route('employee.dashboard');
        }


        $section_id = $user->section->id ?? null;
        $branch_id = $user->section->branch_id ?? null;
        $department_id = $user->section->department_id ?? null;

        $records = EmployeeInformation::with('section.department', 'section.branch', 'positions', 'personal', 'account')->get();


        $groupedRecords = [
            'branch' => [
                'branch_id' => $branch_id,
                'branch_name' => $records->first()->section->branch->name ?? 'Unknown Branch',
            ],
            'department' => [
                'department_id' => $department_id,
                'department_name' => $records->first()->section->department->name ?? 'Unknown Department',
            ],
            'section' => [
                'section_id' => $section_id,
                'section_name' => $records->first()->section->name ?? 'Unknown Department',
            ],
        ];

        
        foreach ($records as $record) {
            // Check if the record matches the groupedRecords criteria.
            $isSameBranch = isset($record['section']['branch_id']) && $record['section']['branch_id'] === $groupedRecords['branch']['branch_id'];
            $isSameDepartment = isset($record['section']['department_id']) && $record['section']['department_id'] === $groupedRecords['department']['department_id'];
            $isSameSection = isset($record['section_id']) && $record['section_id'] === ($groupedRecords['section']['section_id'] ?? null);
        
            // If the record matches, process it.
            if ($isSameBranch && $isSameDepartment && $isSameSection) {
                // Add the record to the employees list.
        
                // Group employees by position.
                $positionId = $record['position_id'];
                $positionName = $record['positions']['name'] ?? 'Unknown Position';
        
                if (!isset($groupedRecords['positions'][$positionId])) {
                    $groupedRecords['positions'][$positionId] = [
                        'position_id' => $positionId,
                        'position_name' => $positionName,
                        'employees' => []
                    ];
                }
        
                $groupedRecords['positions'][$positionId]['employees'][] = $record->toArray();
            }
        }
        
        // Convert position groups to a clean array structure.
        $groupedRecords['positions'] = array_values($groupedRecords['positions']);
        
        // Debug the grouped data.
        $this->records = $groupedRecords;

    }

    public function render()
    {
        return view('livewire.employee.team');
    }
}
