<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use App\Models\OtherDeductions;
use App\Models\OtherEarnings;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:read employee-earnings')->only('index');
        $this->middleware('permission:write employee-earnings')->only(['create', 'edit']);
    }

    public function index(int $id)
    {

        $header = OtherEarnings::where('id', $id)
            ->first();


        if(!$header) {
            return redirect()->route('other-earnings.index');
        }

        $formatted = ucwords('Employees ' . $header->name);

        return view('admin.settings.hris.emp-earnings.index', compact('id', 'formatted'));
    }

}
