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

    public function loadRecords()
    {
        $user = EmployeeInformation::with('section.branch', 'section.department')
            ->where('employee_no', Auth::user()->employee_no)
            ->first();

        if (!$user || !$user->section_id) {
            // If no section assigned, redirect or show empty
            $this->records = [];
            return;
        }

        $sectionId = $user->section_id;

        // Get all employees in the same section
        $employees = EmployeeInformation::with([
            'section.branch',
            'section.department',
            'positions',
            'personal',
            'account'
        ])->where('section_id', $sectionId)->get();

        // Group employees by position within the section
        $section = [
            'section_id' => $sectionId,
            'section_name' => $user->section->name ?? 'Unassigned Section',
            'department_name' => $user->section->department?->description ?? 'Unassigned Department',
            'branch_name' => $user->section->branch?->name ?? 'Unassigned Branch',
            'positions' => []
        ];

        foreach ($employees as $emp) {
            $positionId = $emp->position_id ?? 0;
            $positionName = $emp->positions?->name ?? 'Unassigned Position';

            if (!isset($section['positions'][$positionId])) {
                $section['positions'][$positionId] = [
                    'position_id' => $positionId,
                    'position_name' => $positionName,
                    'employees' => []
                ];
            }

            $section['positions'][$positionId]['employees'][] = $emp->toArray();
        }

        // Convert positions associative array to indexed array
        $section['positions'] = array_values($section['positions']);

        // Only one section for logged-in user
        $this->records = [$section];
    }

    public function render()
    {
        return view('livewire.employee.team');
    }
}
