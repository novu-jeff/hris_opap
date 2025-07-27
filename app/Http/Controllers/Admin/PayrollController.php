<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Services\PayrollService;
use App\Models\EmployementTypes;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{

    public $payroll_id;
    public $employment_type;

    public function index(Request $request)
    {
        $employmentTypes =  EmployementTypes::with(['setting'])->get();

        $defaultActions = 'salary';
        $defaultEmploymentType = $employmentTypes[0]->name;

        $options = $employmentTypes->mapWithKeys(function ($item) {
            $subs = [];

            $settings = $item->setting ?? [];

            if ($settings['is_salary']) {
                $subs['salary'] = 'Salary';
            }
            if ($settings['is_clothing_allowance']) {
                $subs['clothing_allowance'] = 'Clothing Allowance';
            }
            if ($settings['is_mid_year']) {
                $subs['mid_year'] = 'Mid Year Bonus';
            }
            if ($settings['is_year_end']) {
                $subs['year_end'] = 'Year End Bonus';
            }
            if ($settings['is_ot_pay']) {
                $subs['ot_pay'] = 'Overtime Pay';
            }

            return [
                strtolower($item->name) => [
                    'name' => $item->name,
                    'sub' => $subs,
                ]
            ];
        })->toArray();

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
