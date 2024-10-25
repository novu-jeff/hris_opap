<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function interview(int $job_id, int $interview_id) {
        return view('home.interview', compact('job_id', 'interview_id'));
    }

    public function offer(int $job_id) {
        return view('home.signed-offer', compact('job_id'));
    }

    public function requirements(int $job_id) {
        return view('home.requirements', compact('job_id'));
    }
}
