<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeaveCreditsImport;
use Illuminate\Support\Facades\Log;

class LeaveImportController extends Controller
{
    /**
     * Show the upload form.
     */
    public function index()
    {
        return view('admin.settings.leave-import.index');
    }

    /**
     * Handle the Excel import request.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'import_type' => 'required', // 'vl_sl' or 'credits'
        ]);

        try {
            $file = $request->file('file');
            $importType = $request->input('import_type');

            // Determine type of import
            $isVlSl = $importType === 'vl_sl';
            $employee_no = $request->input('employee_no');
            $leave_type_id = $request->input('leave_type_id');

            Excel::import(
                new LeaveCreditsImport($employee_no, $isVlSl, $leave_type_id),
                $file
            );

            return back()->with('success', 'Excel file imported successfully!');
        } catch (\Exception $e) {
            Log::error('Leave import failed: ' . $e->getMessage());
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
