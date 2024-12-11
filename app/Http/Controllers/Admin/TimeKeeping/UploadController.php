<?php

namespace App\Http\Controllers\Admin\TimeKeeping;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(string $month = null, int $year = null)
    {
        return view('admin.timekeeping.index', compact('month', 'year'));
    }

    public function upload()
    {
        return view('admin.timekeeping.upload');
    }

}
