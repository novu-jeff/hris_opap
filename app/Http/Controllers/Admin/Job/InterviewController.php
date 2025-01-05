<?php

namespace App\Http\Controllers\Admin\Job;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InterviewController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read assessments')->only('index');
        $this->middleware('permission:write assessments')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.job.interview.index');
    }

    public function create()
    {
        return view('admin.job.interview.create');
    }

    public function edit(string $id)
    {
        return view('admin.job.interview.edit', compact('id'));
    }
}
