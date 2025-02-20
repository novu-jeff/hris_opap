<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeave;
use App\Models\EmployeeLeaveCard;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaveController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read apply-leave')->only('index');
        $this->middleware('permission:write apply-leave')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('employee.leave', [
            'action' => 'view',
            'title' => 'ESS | Leave Applications',
            'header' => 'Manage Leaves',
            'sub' => 'Track and monitor your leave applications.'
        ]);
    }

    public function create()
    {
        return view('employee.leave', [
            'action' => 'create',
            'title' => 'Apply Leave',
            'header' => 'Leave Application',
            'sub' => 'By proceeding, you\'ll be able to apply for a leave.'
        ]);

    }

    public function edit(int $id)
    {
        return view('employee.leave', [
            'id' => $id,
            'action' => 'edit',
            'title' => 'Edit Leave',
            'header' => 'Edit Application',
            'sub' => 'Feel free to edit or update your leave application.'
        ]);

    }


}
