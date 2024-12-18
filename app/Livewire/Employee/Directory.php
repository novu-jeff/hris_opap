<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeInformation;
use Livewire\Component;

class Directory extends Component
{

    public $records;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $records = EmployeeInformation::with('section.department', 'section.branch', 'positions', 'personal', 'account')->get();

        $sortedRecords = $records->sort(function ($a, $b) {
            $branchA = $a->section->branch_id ?? PHP_INT_MAX;
            $branchB = $b->section->branch_id ?? PHP_INT_MAX;
        
            if ($branchA === $branchB) {
                $departmentA = $a->section->department_id ?? PHP_INT_MAX;
                $departmentB = $b->section->department_id ?? PHP_INT_MAX;
        
                if ($departmentA === $departmentB) {
                    return ($a->position_id ?? PHP_INT_MAX) <=> ($b->position_id ?? PHP_INT_MAX);
                }
                return $departmentA <=> $departmentB;
            }
            return $branchA <=> $branchB;
        });
        
        // Initialize the nested array
        $nestedArray = [];
        
        // Grouping into the nested structure
        foreach ($sortedRecords as $record) {
            // Handle null section as "Unassigned Employees"
            if (!$record->section) {
                if (!isset($nestedArray['unassigned'])) {
                    $nestedArray['unassigned'] = [
                        'group_name' => 'Unassigned Employees',
                        'employees' => []
                    ];
                }
                $nestedArray['unassigned']['employees'][] = $record->toArray();
                continue;
            }
        
            // Get branch, department, and section details
            $branchId = $record->section->branch_id;
            $branchName = $record->section->branch->name ?? 'Unknown Branch';
            $departmentId = $record->section->department_id;
            $departmentName = $record->section->department->name ?? 'Unknown Department';
            $sectionId = $record->section_id;
            $sectionName = $record->section->name ?? 'Unknown Section';
        
            // Initialize the branch if it doesn't exist
            if (!isset($nestedArray[$branchId])) {
                $nestedArray[$branchId] = [
                    'branch_id' => $branchId,
                    'branch_name' => $branchName,
                    'departments' => []
                ];
            }
        
            // Initialize the department if it doesn't exist
            if (!isset($nestedArray[$branchId]['departments'][$departmentId])) {
                $nestedArray[$branchId]['departments'][$departmentId] = [
                    'department_id' => $departmentId,
                    'department_name' => $departmentName,
                    'sections' => []
                ];
            }
        
            // Initialize the section if it doesn't exist
            if (!isset($nestedArray[$branchId]['departments'][$departmentId]['sections'][$sectionId])) {
                $nestedArray[$branchId]['departments'][$departmentId]['sections'][$sectionId] = [
                    'section_id' => $sectionId,
                    'section_name' => $sectionName,
                    'employees' => []
                ];
            }
        
            // Add the full employee record to the respective section
            $nestedArray[$branchId]['departments'][$departmentId]['sections'][$sectionId]['employees'][] = $record->toArray();
        }
        
        // Convert associative arrays to zero-based indexed arrays
        foreach ($nestedArray as &$branch) {
            if (isset($branch['departments'])) {
                $branch['departments'] = array_values($branch['departments']);
                foreach ($branch['departments'] as &$department) {
                    if (isset($department['sections'])) {
                        $department['sections'] = array_values($department['sections']);
                    }
                }
            }
        }
        
        if (isset($nestedArray['unassigned'])) {
            $nestedArray['unassigned']['employees'] = array_values($nestedArray['unassigned']['employees']);
        }
        
        // Re-index the outer array
        $nestedArray = array_values($nestedArray);
        
        $this->records = $nestedArray;
        
    }


    public function render()
    {
        return view('livewire.employee.directory');
    }
}
