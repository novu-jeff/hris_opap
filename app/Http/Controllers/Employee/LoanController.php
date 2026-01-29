<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;

class LoanController extends Controller
{

    public function __construct() {
        //$this->middleware('permission:read apply-leave')->only('index');
        //$this->middleware('permission:write apply-leave')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('employee.loan', [
            'action' => 'view',
            'title' => 'ESS | Loan History ',
            'header' => 'Manage Loans',
            'sub' => 'Track and monitor your Loan History.'
        ]);
    }

    public function card()
    {
        return view('employee.loan-card', [
            'action' => 'view',
            'title' => 'ESS | Leave Card',
            'header' => 'View Leave Card',
            'sub' => 'Track and monitor your leaves.'
        ]);
    }

    public function create()
    {
        return view('employee.loan', [
            'action' => 'create',
            'title' => 'Apply Loan',
            'header' => 'Loan Application',
            'sub' => 'By proceeding, you\'ll be able to apply for a Loan.'
        ]);

    }

    public function edit(int $id)
    {
     
        return view('employee.loan', [
            'loan_id' => $id,
            'action' => 'edit',
            'title' => 'Edit Loan',
            'header' => 'Edit Loan Application',
            'sub' => 'Feel free to edit or update your loan application.'
        ]);

    }


}
