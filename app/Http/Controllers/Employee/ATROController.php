<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ATROController extends Controller
{

    

    public function index() {
        return view('employee.atro', [
            'action' => 'view',
            'title' => 'ESS | Authority to render overtime',
            'header' => 'Authority to render overtime ',
            'sub' => ''
        ]);
    }

    public function create()
    {
        return view('employee.atro', [
            'action' => 'create',
            'title' => 'ESS | Authority to render overtime',
            'header' => 'Create Authority to render overtime',
            'sub' => 'By proceeding, you\'ll be able to apply for a leave.'
        ]);

    }

    public function edit(int $id) {
        return view('employee.atro', [
            'id' => $id,
            'action' => 'edit',
            'title' => 'ESS | Authority to render overtime',
            'header' => 'Edit Authority to render overtime ',
            'sub' => ''
        ]);
    }
}
