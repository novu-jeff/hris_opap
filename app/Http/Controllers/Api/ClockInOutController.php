<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTimelogs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ClockInOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {

        $user_id = Auth::user()->employee_id;

        $records = EmployeeTimelogs::where('employee_id', $user_id)->get();

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
     
         Log::info('Clock-in/out request received.', [
             'user_id' => $user_id,
             'action' => $action,
             'date' => $today,
             'request' => $request->all()
         ]);
     
         try {
             // Validate action and captured data
             $request->validate([
                 'action' => 'required|in:clockin,clockout',
                 'capture_image' => 'required|image|mimes:jpg,jpeg,png',
             ]);
             Log::info('Validation successful.');
         } catch (\Illuminate\Validation\ValidationException $e) {
             Log::warning('Validation failed.', [
                 'errors' => $e->errors(),
             ]);
             return response()->json([
                 'status' => 'error',
                 'errors' => $e->errors(),
             ], 422);
         }
     
         // Capture and save the photo
         Log::info('Attempting to save captured image.');
         $imageName = $this->savePhoto($request->file('capture_image'), true);
     
         if (!$imageName) {
             Log::error('Image capture failed.');
             return response()->json([
                 'status' => 'error',
                 'message' => 'Image capture failed. Please enable your camera and try again.',
             ], 400);
         }
     
         Log::info('Image saved successfully.', ['imageName' => $imageName]);
     
         // Get the location data
         Log::info('Attempting to save location.');
         $locationData = $this->saveLocation();
         Log::info('Location saved successfully.', ['locationData' => $locationData]);
     
         // Fetch existing clock-in/out record for today
         Log::info('Checking for existing clock-in/out record.');
         $existingRecord = EmployeeTimelogs::where('employee_id', $user_id)
             ->whereDate('created_at', $today)
             ->first();
     
         Log::info('Existing record retrieved.', ['existingRecord' => $existingRecord]);
     
         if ($action == 'clockin') {
             if ($existingRecord && $existingRecord->clock_in) {
                 Log::warning('Clock-in attempt rejected. Already clocked in.', [
                     'clock_in_time' => $existingRecord->clock_in,
                 ]);
                 return response()->json([
                     'status' => 'error',
                     'message' => 'You have already clocked in today at ' . Carbon::parse($existingRecord->clock_in)->format('M d, Y h:i A'),
                 ], 400);
             }
     
             $timestamp = Carbon::now();
             Log::info('Clocking in.', ['timestamp' => $timestamp]);
     
             EmployeeTimelogs::create([
                 'employee_id' => $user_id,
                 'clock_in' => $timestamp,
                 'captured_image_clockin' => $imageName,
                 'captured_location_clockin' => $locationData,
             ]);
     
             Log::info('Clock-in successful.', ['timestamp' => $timestamp]);
     
             return response()->json([
                 'status' => 'success',
                 'message' => 'You have successfully clocked in at ' . Carbon::parse($timestamp)->format('M d, Y h:i A'),
             ]);
         }
     
         if ($action == 'clockout') {
             if (!$existingRecord || !$existingRecord->clock_in) {
                 Log::warning('Clock-out attempt rejected. No clock-in record found.');
                 return response()->json([
                     'status' => 'error',
                     'message' => 'You must clock in before you can clock out.',
                 ], 400);
             }
     
             if ($existingRecord->clock_out) {
                 Log::warning('Clock-out attempt rejected. Already clocked out.', [
                     'clock_out_time' => $existingRecord->clock_out,
                 ]);
                 return response()->json([
                     'status' => 'error',
                     'message' => 'You have already clocked out today at ' . Carbon::parse($existingRecord->clock_out)->format('M d, Y h:i A'),
                 ], 400);
             }
     
             $timestamp = Carbon::now();
             Log::info('Clocking out.', ['timestamp' => $timestamp]);
     
             $existingRecord->update([
                 'clock_out' => $timestamp,
                 'captured_image_clockout' => $imageName,
                 'captured_location_clockout' => $locationData,
             ]);
     
             Log::info('Clock-out successful.', ['timestamp' => $timestamp]);
     
             return response()->json([
                 'status' => 'success',
                 'message' => 'You have successfully clocked out at ' . Carbon::parse($timestamp)->format('M d, Y h:i A'),
             ]);
         }
     
         Log::error('Invalid action specified.', ['action' => $action]);
         return response()->json([
             'status' => 'error',
             'message' => 'Invalid action. Please specify either "clockin" or "clockout".',
         ], 400);
     }
     


    public function savePhoto($capture_image, $isImageCaptured)
    {
        // Start logging
        Log::info('savePhoto method called.', ['isImageCaptured' => $isImageCaptured]);
    
        if (!$isImageCaptured || !$capture_image) {
            Log::warning('Image capture failed. No image captured or isImageCaptured is false.', [
                'isImageCaptured' => $isImageCaptured,
                'capture_image' => $capture_image,
            ]);
            return null;
        }
    
        // Ensure that the uploaded file is an image
        if (!$capture_image->isValid() || !$capture_image->isFile()) {
            Log::error('Invalid file upload.', [
                'isValid' => $capture_image->isValid(),
                'isFile' => $capture_image->isFile(),
                'fileInfo' => $capture_image,
            ]);
            return null; // Return null if the uploaded file is invalid
        }
    
        try {
            // Define the image name and storage path
            $imageName = Auth::user()->employee_id . '_' . time() . '.' . $capture_image->getClientOriginalExtension();
            Log::info('Image name generated.', ['imageName' => $imageName]);
    
            // Store the image in the 'public/clockinout' directory
            $capture_image->storeAs('public/clockinout', $imageName);
            Log::info('Image successfully stored.', ['path' => 'public/clockinout/' . $imageName]);
    
            return $imageName; // Return the saved image name
        } catch (\Exception $e) {
            Log::error('Error saving the image.', [
                'exceptionMessage' => $e->getMessage(),
                'exceptionTrace' => $e->getTraceAsString(),
            ]);
            return null;
        }
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
    
            if (!$locationData) {
                return 'running in local environment';
            }
    
            if (!empty($locationData['loc'])) {
                $location = explode(',', $locationData['loc']);
                $latitude = $location[0];
                $longitude = $location[1];
    
                return "{$latitude}, {$longitude}";
            }
        }

        // Return an error message if the response is unsuccessful
        return 'Unable to retrieve location data. Please check your network or API configuration.';
    }

}