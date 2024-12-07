<?php

namespace App\Http\Controllers\Admin\Job;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $status)
    {
        return view('admin.job.applicant.index', compact('status'));
    }
}
