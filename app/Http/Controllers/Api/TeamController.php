<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{


    public function index() {

        $user_id = Auth::user()->employee_id;

        $user = EmployeeInformation::find($user_id);

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
        
        return response()->json([
            'status' => true,
            'data' => $groupedRecords
        ]); 
    }
}
