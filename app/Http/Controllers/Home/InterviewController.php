<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function index(int $job_id, int $interview_id) {
        return view('home.interview', compact('job_id', 'interview_id'));
    }
}
