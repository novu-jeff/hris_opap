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

        $records = EmployeeInformation::with('section.department', 'section.branch', 'positions', 'personal', 'account')
            ->get();

        $groupedRecords = [
            'branch' => [
                'branch_id' => null,
                'branch_name' => 'Unassigned Branch',
            ],
            'department' => [
                'department_id' => null,
                'department_name' => 'Unassigned Department',
            ],
            'section' => [
                'section_id' => null,
                'section_name' => 'Unassigned Section',
            ],
            'positions' => [],
        ];
        
        // Initialize branch, department, and section details using the first record as a reference.
        if ($records->isNotEmpty()) {
            $firstRecord = $records->first();
            $groupedRecords['branch'] = [
                'branch_id' => $firstRecord->section->branch_id ?? null,
                'branch_name' => $firstRecord->section->branch->name ?? 'Unassigned Branch',
            ];
            $groupedRecords['department'] = [
                'department_id' => $firstRecord->section->department_id ?? null,
                'department_name' => $firstRecord->section->department->description ?? 'Unassigned Department',
            ];
            $groupedRecords['section'] = [
                'section_id' => $firstRecord->section->id ?? null,
                'section_name' => $firstRecord->section->name ?? 'Unassigned Section',
            ];
        }
        
        // Process records to group by position.
        foreach ($records as $record) {
            $section = $record->section;
        
            // Validate branch, department, and section match.
            $isSameBranch = isset($section->branch_id) && $section->branch_id === $groupedRecords['branch']['branch_id'];
            $isSameDepartment = isset($section->department_id) && $section->department_id === $groupedRecords['department']['department_id'];
            $isSameSection = isset($section->id) && $section->id === $groupedRecords['section']['section_id'];
        
            if ($isSameBranch && $isSameDepartment && $isSameSection) {
                // Group employees by position.
                $positionId = $record->position_id;
                $positionName = $record->positions->name ?? 'Unassigned Position';
        
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
        
        // Assign grouped records to the component property.
        $this->records = $groupedRecords;
    }

    public function render()
    {
        return view('livewire.employee.team');
    }
}
