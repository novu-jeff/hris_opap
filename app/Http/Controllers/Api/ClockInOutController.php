<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Livewire\Employee\Clock;
use App\Models\EmployeeClockInOut;
use App\Models\EmployeeLeave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClockInOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {

        $user_id = Auth::user()->employee_id;

        $records = EmployeeClockInOut::where('employee_id', $user_id)->get();
        
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
    
     public function store(Request $request)
     {
         $user_id = Auth::user()->employee_id;
         $today = Carbon::today();
         $action = $request->get('action');
     
        try {
            // Validate action and captured data
            $request->validate([
                'action' => 'required|in:clockin,clockout',
                'capture_image' => 'required|image|mimes:jpg,jpeg,png', // Expecting base64 image string
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors(),
            ], 422);
        }
     
        // Decode and save the photo
        $imageName = $this->savePhoto($request->capture_image, true);
    
        if (!$imageName) {
            return response()->json([
                'status' => 'error',
                'message' => 'Image capture failed. Please enable your camera and try again.',
            ], 400);
        }
     
        $locationData = $this->saveLocation();
    
        $existingRecord = EmployeeClockInOut::where('employee_id', $user_id)
            ->whereDate('created_at', $today)
            ->first();
    
        if ($action === 'clockin') {
            if ($existingRecord && $existingRecord->clock_in) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already clocked in today at ' . Carbon::parse($existingRecord->clock_in)->format('M d, Y h:i A'),
                ], 400);
            }
    
            $timestamp = Carbon::now();
    
            EmployeeClockInOut::create([
                'employee_id' => $user_id,
                'clock_in' => $timestamp,
                'captured_image_clockin' => $imageName,
                'captured_location_clockin' => $locationData,
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'You have successfully clocked in at ' . $timestamp->format('M d, Y h:i A'),
            ]);
        }
    
        if ($action === 'clockout') {
            if (!$existingRecord || !$existingRecord->clock_in) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You must clock in before you can clock out.',
                ], 400);
            }
    
            if ($existingRecord->clock_out) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already clocked out today at ' . Carbon::parse($existingRecord->clock_out)->format('M d, Y h:i A'),
                ], 400);
            }
    
            $timestamp = Carbon::now();
    
            $existingRecord->update([
                'clock_out' => $timestamp,
                'captured_image_clockout' => $imageName,
                'captured_location_clockout' => $locationData,
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'You have successfully clocked out at ' . $timestamp->format('M d, Y h:i A'),
            ]);
        }
    
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid action. Please specify either "clockin" or "clockout".',
        ], 400);
    }
    
    public function savePhoto($imageData, $isImageCaptured)
    {
        if (!$isImageCaptured) {
            return null;
        }
    
        // Decode the base64 image
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imageName = Auth::user()->employee_id . '_' . time() . '.png';
    
        // Store the image in the 'public/clockinout' directory
        Storage::disk('public')->put('clockinout/' . $imageName, base64_decode($image));
    
        return $imageName;
    }
    
    public function saveLocation()
    {
        $ip = request()->ip();
        $api = env('IPINFO_API');
        $response = Http::get("http://ipinfo.io/{$ip}/json?token={$api}");
    
        if ($response->successful()) {
            $locationData = $response->json();
    
            if ($locationData) {
                return 'running in local environment';
            }
    
            if (!empty($locationData['loc'])) {
                $location = explode(',', $locationData['loc']);
                $latitude = $location[0];
                $longitude = $location[1];
    
                return "{$latitude}, {$longitude}";
            }
        }
    
        return 'Unable to retrieve location data. Please check your network or API configuration.';
    }
     

}
