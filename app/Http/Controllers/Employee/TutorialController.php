<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    public function index() {
        return view('employee.tutorial', [
            'action' => 'index',
            'title' => 'Tutorial',
            'header' => 'Learn more about employee self service',
        ]);
    }
}
