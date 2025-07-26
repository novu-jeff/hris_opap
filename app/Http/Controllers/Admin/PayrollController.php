<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Services\PayrollService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{

    public $payroll_id;
    public $employment_type;

    public function index(Request $request)
    {
        $product = config('app.product');

        $defaultActions = 'salary';
        $defaultEmploymentType = $product === 'government' ? 'contractual' : 'rank and file';

        if ($product === 'government') {
            $options = [
                'contractual' => [
                    'name' => 'contractual',
                    'sub' => [
                        'salary' => 'Salary',
                        'clothing_allowance' => 'Clothing Allowance',
                        'mid_year' => 'Mid Year Bonus',
                        'year_end' => 'Year End Bonus',
                        'ot_pay' => 'Overtime Pay',
                    ],
                ],
                'contract of service' => [
                    'name' => 'contract of service',
                    'sub' => [
                        'salary' => 'Salary',
                        'ot_pay' => 'Overtime Pay',
                    ],
                ],
                'job order' => [
                    'name' => 'Job Order',
                    'sub' => [
                        'salary' => 'Salary',
                    ],
                ],
            ];
        } else {
            $options = [
                'rank and file' => [
                    'name' => 'rank and file',
                    'sub' => [
                        'salary' => 'Salary',
                        'mid_year' => 'Mid Year Bonus',
                        'year_end' => 'Year End Bonus',
                    ],
                ],
                'manager' => [
                    'name' => 'manager',
                    'sub' => [
                        'salary' => 'Salary',
                        'mid_year' => 'Mid Year Bonus',
                        'year_end' => 'Year End Bonus',
                    ],
                ],
                'supervisor' => [
                    'name' => 'Supervisor',
                    'sub' => [
                        'salary' => 'Salary',
                        'mid_year' => 'Mid Year Bonus',
                        'year_end' => 'Year End Bonus',
                    ],
                ],
            ];
        }

        $employmentTypeInput = strtolower($request->input('employment_type', $defaultEmploymentType));
        $typeInput = strtolower($request->input('type', $defaultActions));

        if (!array_key_exists($employmentTypeInput, $options)) {
            return redirect()->route('payroll.index', [
                'employment_type' => $defaultEmploymentType,
                'type' => $defaultActions,
            ]);
        }

        $validSubTypes = array_keys($options[$employmentTypeInput]['sub']);

        if (!in_array($typeInput, $validSubTypes)) {
            $firstType = $validSubTypes[0] ?? $defaultActions;

            return redirect()->route('payroll.index', [
                'employment_type' => $employmentTypeInput,
                'type' => $firstType,
            ]);
        }

        return view('admin.payroll.index', [
            'type' => $typeInput,
            'employment_type' => $employmentTypeInput,
            'actions' => $defaultActions,
            'options' => $options,
        ]);
    }


    public function process(string $type, int $id) {

        $service = app(PayrollService::class);
        $model = $service->getProcess($type)['models']['parent'];

        $payroll = $model::find($id);
        
        if(!$payroll) {
            return redirect()->route('payroll.index');
        }

        $payroll_date = Carbon::parse(time: $payroll->payroll_date)->format('F d, Y');

        return view('admin.payroll.process', compact('id', 'payroll_date', 'type'));
    }

}
