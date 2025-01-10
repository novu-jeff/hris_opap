<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeamController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read my-team')->only('index');
    }

    public function index() {
        return view('employee.team', [
            'action' => 'index',
            'title' => 'ESS | My Team',
            'header' => 'My Team',
            'sub' => 'Lists of all employees under my team'
        ]);
    }
}
