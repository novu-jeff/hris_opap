<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use App\Models\EmployementTypes;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function index(Request $request) {
        
 
        $data = $this->getData($request->all());

        // dd($data->toArray());

        return view('admin.download.template.employee', compact('data'));
    }

    public function getData(array $payload) {

        $target = $payload['show'];

        switch($target) {
            case 'employee':
                return $this->getEmployee($payload);
                break;
        }

    }

    public function getEmployee($payload) {

        $model = EmployeeInformation::with([
            'employment_type',
            'account',
            'section',
            'personal',
            'education',
            'parents',
            'children',
            'employment_history',
            'civil_service',
            'trainings',
            'others',
            'skills',
            'positions',
        ]);

        if (!empty($payload['role']) && $payload['role'] != 'all') {
            $model = $model->where('employment_type_id', $payload['role']);
        }
        
        if (!empty($payload['employee_no'])) {
            $model = $model->where('employee_no', $payload['employee_no']);
        }
        
        return $model->get();

    }
}
