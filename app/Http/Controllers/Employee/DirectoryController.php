<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function index() {
        return view('employee.directory', [
            'action' => 'index',
            'title' => 'ESS | My Directory',
            'header' => 'Directory Listing',
            'sub' => 'Lists of all employees in all branches and departments'
        ]);
    }
}
