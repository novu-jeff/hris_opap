<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyInformationController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:read company-information')->only('index');
    }

    public function index()
    {
        return view('admin.settings.company.index');
    }
}
