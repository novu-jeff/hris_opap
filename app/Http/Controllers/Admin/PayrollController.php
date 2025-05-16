<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use App\Models\EmployementTypes;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{

    public $payroll_id;
    public $employment_type;

    public function index(Request $request) {
        
        $type = $request->input('type');

        $allowed_types = ['salary', 'mid_year', '13th_month', 'cto'];

        if(empty($type) || is_null($type)) {
            return redirect()->route('payroll.index', ['type' => 'salary']);
        }

        if(!in_array($type, $allowed_types)) {
            return redirect()->route('payroll.index', ['type' => 'salary']);
        }

        $type = strtolower($type);

        return view('admin.payroll.index', compact('type'));
    }

    public function process(int $id) {

        $payroll = Payroll::find($id);

        if(!$payroll) {
            return redirect()->route('payroll.index');
        }

        return view('admin.payroll.process', compact('id'));
    }

}
