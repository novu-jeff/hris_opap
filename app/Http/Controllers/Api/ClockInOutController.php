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
                'capture_image' => 'required|image|mimes:jpg,jpeg,png'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        }
    
        // Capture and save the photo
        $imageName = $this->savePhoto($request->file('capture_image'), true);

        if (!$imageName) {
            return response()->json([
                'status' => 'error',
                'message' => 'Image capture failed. Please enable your camera and try again.'
            ], 400);
        }
    
        // Get the location data
        $locationData = $this->saveLocation();
    
        // Fetch existing clock-in/out record for today
        $existingRecord = EmployeeClockInOut::where('employee_id', $user_id)
            ->whereDate('created_at', $today)
            ->first();
    
        if ($action == 'clockin') {
    
            if ($existingRecord && $existingRecord->clock_in) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already clocked in today at ' . Carbon::parse($existingRecord->clock_in)->format('M d, Y h:i A')
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
                'clock_out' => $timestamp,
                'captured_image_clockout' => $imageName,
                'captured_location_clockout' => $locationData,
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
    
     
    public function savePhoto($capture_image, $isImageCaptured)
    {
        if (!$isImageCaptured || !$capture_image) {
            return null;
        }
    
        // Ensure that the uploaded file is an image
        if (!$capture_image->isValid() || !$capture_image->isFile()) {
            return null; // Return null if the uploaded file is invalid
        }
    
        // Define the image name and storage path
        $imageName = Auth::user()->employee_id . '_' . time() . '.' . $capture_image->getClientOriginalExtension();
        
        // Store the image in the 'public/clockinout' directory
        $capture_image->storeAs('public/clockinout', $imageName);
    
        return $imageName; // Return the saved image name
    }

    public function saveLocation()
    {
        $ip = request()->ip();
        $api = env('IPINFO_API');
        
        // Make the request to the IP info API
        $response = Http::get("http://ipinfo.io/{$ip}/json?token={$api}");

        // Check if the response is successful
        if ($response->successful()) {
            $locationData = $response->json();

            // Check if we're in a local environment or if 'loc' is present
            if (isset($locationData['bogon']) && $locationData['bogon']) {
                return 'Running in local environment';
            } elseif (isset($locationData['loc'])) {
                $location = explode(',', $locationData['loc']);
                $latitude = $location[0];
                $longitude = $location[1];

                return $latitude . ' ' . $longitude;
            } else {
                return 'Location data not available for this IP.';
            }
        }

        // Return an error message if the response is unsuccessful
        return 'Unable to retrieve location data. Please check your network or API configuration.';
    }

}
