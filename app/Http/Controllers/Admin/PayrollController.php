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

    public function index() {
        return view('admin.payroll.index');
    }

    public function process(int $id, int $employment_type = null) {

        $payroll = Payroll::find($id);
        $employment_type = EmployementTypes::where('id', $employment_type)->first();


        if(!$payroll) {
            return redirect()->route('payroll.index');
        }

        return view('admin.payroll.process', compact('id', 'employment_type'));
    }

}
