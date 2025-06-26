<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use App\Models\EmployementTypes;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PayrollController extends Controller
{

    public $payroll_id;
    public $employment_type;

    public function index(Request $request)
    {
        $defaultActions = 'salary';
        $defaultEmploymentType = 'contractual'; 

        $options = [
            'contractual' => [
                'name' => 'contractual',
                'sub' => [
                    'salary' => 'Salary',
                    'mid_year' => 'Mid Year Bonus',
                    'year_end' => 'Year End Bonus',
                    'rata' => 'RATA',
                    'eme' => 'EME',
                    'ot_pay' => 'OT Pay'
                ]
            ],
            'cos' => [
                'name' => 'contract of service',
                'sub' => [
                    'salary' => 'Salary',
                    'ot_pay' => 'OT Pay'
                ]
            ],
            'jo' => [
                'name' => 'Job Order',
                'sub' => [
                    'salary' => 'Salary'
                ]
            ]
        ];

        $employmentTypeInput = $request->input('employment_type', $defaultEmploymentType);
        $typeInput = strtolower($request->input('type', $defaultActions));

        if (!array_key_exists($employmentTypeInput, $options)) {
            return redirect()->route('payroll.index', [
                'type' => $defaultActions,
                'employment_type' => $defaultEmploymentType,
            ]);
        }

        if (!array_key_exists($typeInput, $options[$employmentTypeInput]['sub'])) {
            return redirect()->route('payroll.index', [
                'type' => $defaultActions,
                'employment_type' => $defaultEmploymentType,
            ]);
        }


        return view('admin.payroll.index', [
            'type' => $typeInput,
            'employment_type' => $employmentTypeInput,
            'actions' => $defaultActions,
            'options' => $options,
        ]);
    }


    public function process(int $id) {

        $payroll = Payroll::find($id);
        
        if(!$payroll) {
            return redirect()->route('payroll.index');
        }

        $payroll_date = Carbon::parse($payroll->payroll_date)->format('F d, Y');

        return view('admin.payroll.process', compact('id', 'payroll_date'));
    }

}
