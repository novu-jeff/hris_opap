<?php

namespace App\Http\Controllers\Admin\Job;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RequirementsController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:read requirements')->only('index');
        $this->middleware('permission:write requirements')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.job.requirements.index');
    }

    public function create()
    {
        return view('admin.job.requirements.create');
    }

    public function edit(string $id)
    {
        return view('admin.job.requirements.edit', compact('id'));
    }

}
