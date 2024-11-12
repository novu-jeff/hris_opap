<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public $user_id;

    public function index()
    {
        
        $user_id = Auth::user()->employee_id;

        $records = EmployeeLeave::where('employee_id', $user_id)->get();
        return response([
            'status' => 'success',
            'stats' => [
                'total' => $records->count(),
                'pending' => $records->where('status', 'pending')->count(),
                'granted' => $records->where('status', 'granted')->count(),
                'rejected' => $records->where('status', 'rejected')->count()
            ],
            'data' => $records ?? []
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    
     public function store(Request $request)
     {

        $user_id = Auth::user()->employee_id;
        
        $validator = Validator::make($request->all(), $this->rules());
         
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400); 
        }
     
        try {
            $from = Carbon::parse($request->from);
            $to = Carbon::parse($request->to);
            $consumed_hours = $from->diffInHours($to);
    
            $model = EmployeeLeave::class;
            $pending = $model::where('employee_id', $user_id)
                ->where('status', false)
                ->count();
    
            $max_pending = env('MAX_PENDING_LEAVE_APPLICATION');
    
            if ($pending >= $max_pending) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unfortunately, you have reached your maximum limit for leave applications. You currently have ' . $pending . ' applications awaiting approval.'
                ], 500); 
            }
    
            $model::create([
                'employee_id' => $user_id,
                'type' => $request->type,
                'reason' => $request->reason,
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
                'measurement' => 'full day',
                'consumed_hours' => $consumed_hours 
            ]);
        
            return response()->json([
                'status' => 'success',
                'message' => 'Your application has been submitted. You will receive an email regarding your application status as soon as we review it. Thank you for your understanding.'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500); 
        }
     }

    public function rules() {
        return [
            'type' => 'required|in:casual,medical,unpaid,emergency,sick',
            'reason' => 'required',
            'from' => 'required|date|after:today',
            'to' => 'required|date|after:from'
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user_id = Auth::user()->employee_id;

        $records = EmployeeLeave::where('employee_id', $user_id)
            ->where('id', $id)
            ->first();

        return response([
            'status' => 'success',
            'data' => $records ?? []
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {

        $user_id = Auth::user()->employee_id;

        $validator = Validator::make($request->all(), $this->rules());
         
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400); 
        }
     
        try {

            $from = Carbon::parse($request->from);
            $to = Carbon::parse($request->to);
            $consumed_hours = $from->diffInHours($to);
    
            $model = EmployeeLeave::class;

            if(!$model::where('id', $id)->first()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error: Leave ID `'.$id.'` does not exists '
                ], 500); 
            }

            $model::where('employee_id', $user_id)
                ->where('id', $id)->update([
                    'employee_id' => $user_id,
                    'type' => $request->type,
                    'reason' => $request->reason,
                    'from' => $from->format('Y-m-d'),
                    'to' => $to->format('Y-m-d'),
                    'measurement' => 'full day',
                    'consumed_hours' => $consumed_hours 
            ]);
        
            return response()->json([
                'status' => 'success',
                'message' => 'Your application has been updated.'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500); 
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {

        $user_id = Auth::user()->employee_id;

        $record = EmployeeLeave::where('employee_id', $user_id)
            ->find($id);

        if($record) {       
            $record->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Leave Application #' . strtoupper(format_id($record->id, 6)) . ' deleted successfully.'
            ], 200);
        }
    }



    
}
