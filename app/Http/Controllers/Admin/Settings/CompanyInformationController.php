<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyInformationController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.settings.company.index');
    }
}
