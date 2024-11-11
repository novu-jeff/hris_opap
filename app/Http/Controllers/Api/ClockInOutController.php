<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Livewire\Employee\Clock;
use App\Models\EmployeeClockInOut;
use App\Models\EmployeeLeave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClockInOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public $user_id;

    public function __construct() {
        $this->user_id = 1;
    }

    public function index()
    {
        $records = EmployeeClockInOut::where('employee_id', $this->user_id)->get();
        return response([
            'status' => 'success',
            'stats' => [
                'total' => $records->count(),
            ],
            'data' => $records ?? []
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    
     public function store(Request $request) {
        $today = Carbon::today();

        $action = $request->get('action');
    
        $existingRecord = EmployeeClockInOut::where('employee_id', $this->user_id)
                ->whereDate('created_at', $today)
                ->first();
    
        if ($action == 'clockin') {

            if ($existingRecord) {
                if ($existingRecord->clock_in) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You have already clocked in today at ' . Carbon::parse($existingRecord->clock_in)->format('M d, Y h:i A')
                    ], 400);
                }
    
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are already clocked in, but haven\'t clocked out yet. Please clock out first.'
                ], 400);
            }
    
            $timestamp = Carbon::now();
    
            EmployeeClockInOut::create([
                'employee_id' => $this->user_id,
                'clock_in' => $timestamp
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'You have successfully clocked in at ' . Carbon::parse($timestamp)->format('M d, Y h:i A')
            ]);
        }
    
        if ($action == 'clockout') {

            if (!$existingRecord || !$existingRecord->clock_in) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You must clock in before you can clock out.'
                ], 400);
            }
    
            if ($existingRecord->clock_out) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already clocked out today at ' . Carbon::parse($existingRecord->clock_out)->format('M d, Y h:i A')
                ], 400);
            }
    
            $timestamp = Carbon::now();
    
            $existingRecord->update([
                'clock_out' => $timestamp
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'You have successfully clocked out at ' . Carbon::parse($timestamp)->format('M d, Y h:i A')
            ]);
        }
    
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid action. Please specify either "clockin" or "clockout".'
        ], 400);
    }

}
