<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use App\Models\EmployementTypes;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PayrollController extends Controller
{

    public $payroll_id;
    public $employment_type;

    public function index(Request $request)
    {
        $defaultType = 'salary';
        $defaultEmploymentType = 1;

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:salary,mid_year,13th_month,cto',
            'employment_type' => 'nullable|exists:employment_types,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('payroll.index', [
                'type' => $defaultType,
                'employment_type' => $defaultEmploymentType,
            ]);
        }

        $type = strtolower($request->input('type', $defaultType));
        $employment_type = $request->input('employment_type', $defaultEmploymentType);

        $employmentTypes = EmployementTypes::all();

        return view('admin.payroll.index', compact('type', 'employment_type', 'employmentTypes'));
    }

    public function process(int $id) {

        $payroll = Payroll::find($id);

        if(!$payroll) {
            return redirect()->route('payroll.index');
        }

        return view('admin.payroll.process', compact('id'));
    }

}
