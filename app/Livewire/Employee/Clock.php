<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeTimelogs;
use App\Models\EmployeeInformation;
use App\Models\AccomplishmentType;
use App\Services\ClockInOutService;
use App\Services\DailyTimeRecordService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Clock extends Component
{


    use WithFileUploads;

    public $bsd_emp_identical;
    public $employee_id;
    public $employee_no;
    public $gps_location;
    public $isFaceDetected;
    public $isToHide = false;
    public $status;
    public $entry;
    public $imageCaptured;
    public $isForcedOut = false;
    /** Web clock-in disabled: biometric row already exists today in mysql2.attendances */
    public bool $hideClockInDueToExternalLog = false;

    /** When true, Clock In requires GPS within CLOCK_GEOFENCE_* center + radius */
    public bool $geofenceActive = false;

    public ?float $geofenceLat = null;

    public ?float $geofenceLng = null;

    public float $geofenceRadiusM = 200;

    public $accomplishment;
    public $logs = [];
    public $manipulate_timestamp = '07:00';
    public $upload_accomplishment;

    public $accomplishment_type;
    public $accomplishment_details;

   /* public $accomplishmentOptions = [
        'System Development',
        'Bug Fixing',
        'Technical Support',
        'Client Support',
        'Data Encoding',
        'Testing / QA',
        'Deployment',
        'Documentation',
        'Meeting',
        'Training',
        'Research',
        'Field Work',
        'Monitoring',
        'Maintenance',
        'Others'
    ];*/

    public $accomplishmentOptions = [];

    protected $listeners = [
        'getLocation',
        'imageCaptured',
        'triggerClockForced',
        'saveAccomplishment'
    ];

    protected $rules = [
        'upload_accomplishment' => 'nullable|file|mimes:pdf|max:5120', // 5MB max
        'accomplishment_type' => 'required',
    ];

    protected $validationAttributes = [
    ];

    public function mount(): void
    {
        $this->accomplishmentOptions = AccomplishmentType::where('is_active', 1)
        ->orderBy('accomplishment_name')
        ->pluck('accomplishment_name')
        ->toArray();
       
        $this->bsd_emp_identical = config('app.bsd_emp_identical');
        $this->employee_no = Auth::user()->employee_no;
       // dd($this->bsd_emp_identical);

        $employee = EmployeeInformation::where('employee_no', $this->employee_no)->first();
        if (!$employee) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Employee not found.'
            ]);
            return;
        }

        $this->employee_id = $this->bsd_emp_identical ? $employee->employee_no : $employee->bsd_no;

        $this->hideClockInDueToExternalLog = EmployeeTimelogs::hasExternalAttendanceOnDate($this->employee_no);

        $this->toggleStatus();
    }

    public function loadRecords()
    {

      
        $this->employee_no = Auth::user()->employee_no;
        $this->bsd_emp_identical = config('app.bsd_emp_identical');
        
    }

   public function getLocation($lng, $lat, $isToHide = false)
{
    // Log coordinates received from JS
    // \Log::info('GPS Location Received:', ['lng' => $lng, 'lat' => $lat, 'isToHide' => $isToHide]);

    $this->isToHide = $isToHide;
    $accessToken = env('MAPBOX_API');
    $this->gps_location = null; // default

    // Default fallback place string
    $fallbackPlace = "Lat: {$lat}, Lng: {$lng}";

    try {
        $url = "https://api.mapbox.com/geocoding/v5/mapbox.places/{$lng},{$lat}.json";
        $response = Http::get($url, ['access_token' => $accessToken]);

        if ($response->successful()) {
            $decoded = $response->json();
            \Log::info('Mapbox response:', $decoded);

            $feature = $decoded['features'][0] ?? null;
            $placeName = $feature['place_name'] ?? $fallbackPlace;

            $this->gps_location = [
                'place' => $placeName,
                'coordinates' => [
                    'lng' => $lng,
                    'lat' => $lat
                ]
            ];

            // Dispatch map to JS
           $this->dispatch('loadMap', [
                'token' => $accessToken,
                'lng' => $lng,
                'lat' => $lat,
                'place' =>  $gps_location['place_name'] ?? null
            ]);
        } else {
            // Mapbox failed — fallback to coordinates
            $this->gps_location = [
                'place' => $fallbackPlace,
                'coordinates' => [
                    'lng' => $lng,
                    'lat' => $lat
                ]
            ];
            // \Log::warning('Mapbox API request failed', ['status' => $response->status()]);
        }
    } catch (\Exception $e) {
        // Network or other errors — fallback to coordinates
        $this->gps_location = [
            'place' => $fallbackPlace,
            'coordinates' => [
                'lng' => $lng,
                'lat' => $lat
            ]
        ];
        \Log::error('Mapbox API exception: ' . $e->getMessage());
    }
}


    public function showLogs()
    {
       $this->logs = $this->getLogs();
        $this->dispatch('showModal', ['modal' => 'logs_modal']);
    }

    private function getLogs()
    {
        $records = EmployeeTimelogs::with('employee.personal')
            ->where('employee_id', $this->employee_id)
            ->whereMonth('timestamp', now()->month)
            ->whereYear('timestamp', now()->year)
            ->get();

        return $records
            ->groupBy(fn($record) => optional(Carbon::parse($record->timestamp))->format('j/n/Y') . '|' . ($record->employee_id ?? 'undefined'))
            ->filter()
            ->map(function ($logs, $key) {
                [$date, $employee_id] = explode('|', $key);
                $logs = $logs->sortBy('timestamp')->values();

                $formatLog = fn($log) => [
                    'time' => optional(Carbon::parse($log->timestamp))->format('H:i:s'),
                    'captured_image' => $log->captured_image,
                    'captured_location' => $log->captured_location,
                    'accomplishment' => $log->accomplishment,
                    'accomplishment_type' => $log->accomplishment_type,
                    'accomplishment_details' => $log->accomplishment_details,
                ];

                $baseData = [
                    'date' => $date,
                    'bsd_no' => $employee_id,
                    'employee' => optional($logs->first())->employee,
                    'origin' => optional($logs->first())->origin,
                ];

                $count = $logs->count();
                $lastHasAccomplishment = !empty(optional($logs->last())->accomplishment);

                if ($count === 2 && $lastHasAccomplishment) {
                    return array_merge($baseData, ['logs' => [
                        $formatLog($logs[0]),
                        [],
                        [],
                        $formatLog($logs[1]),
                    ]]);
                }

                if ($count === 3 && $lastHasAccomplishment) {
                    return array_merge($baseData, ['logs' => [
                        $formatLog($logs[0]),
                        $formatLog($logs[1]),
                        [],
                        $formatLog($logs[2]),
                    ]]);
                }

                return array_merge($baseData, ['logs' => $logs->map($formatLog)->values()->all()]);
            })
            ->sortByDesc(fn($item) => Carbon::createFromFormat('j/n/Y', $item['date']))
            ->values();
    }

   
    public function toggleStatus()
    {
        $service = app(DailyTimeRecordService::class);
        $shiftSchedule = $service->getShiftSchedule($this->employee_no);
        $isFlexibleInOut = ($shiftSchedule->shift_duration ?? '') === 'flexible-in-out';
        $hasBreaktime = !$isFlexibleInOut && ($shiftSchedule->is_breaktime_required ?? false);

        $timestamp = now()->format('Y-m-d');
        $bsd_no = $this->bsd_emp_identical ? $this->employee_no : $service->getBsdNo($this->employee_no);

        $model = EmployeeTimelogs::where('employee_id', $bsd_no)->where('timestamp', 'LIKE', "{$timestamp}%");
        $clockRecords = $model->get();
        $entry = $model->count();

        $hasAccomplishment = $clockRecords->contains(fn($record) => !empty($record->accomplishment));
        $this->entry = $entry;

        if ($hasAccomplishment) {
            $this->status = 'Done';
            return;
        }

        if ($hasBreaktime) {
            $this->status = match ($entry) {
                0 => 'Clock In',
                1 => 'Lunch Out',
                2 => 'Lunch In',
                3 => 'Clock Out',
                default => 'Done',
            };
        } else {
            $this->status = match ($entry) {
                0 => 'Clock In',
                1 => 'Clock Out',
                default => 'Done',
            };
        }

        $this->dispatch('loadDefaults');
    }

    private function requiresAccomplishment(): bool
    {
        return $this->status === 'Clock Out' || $this->isForcedOut;
    }

   public function triggerClock()
    {
        if ($this->hideClockInDueToExternalLog && $this->status === 'Clock In') {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'info',
                'title' => 'Already checked in',
                'message' => 'Your attendance was already recorded today (e.g. biometric device).',
            ]);

            return;
        }

        if($this->status == 'Done') {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'info',
                'title' => 'Please be informed',
                'message' => 'You\'ve completed today\'s work.',
            ]);
            return;
        }

        if (!$this->isFaceDetected) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'info',
                'title' => 'No Face Detected',
                'message' => 'No face detected. Please ensure your face is visible to the camera.',
            ]);
            return;
        }

       /* if (is_null($this->gps_location)) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'info',
                'title' => 'No Location Detected',
                'message' => 'No location detected. Please make sure to enable your location or GPS.',
            ]);
            return;
        }*/

        if (empty($this->imageCaptured)) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Image Missing',
                'message' => 'No image was captured.',
            ]);

            return;
        }

      /*  if ($this->requiresAccomplishment() && empty($this->accomplishment)) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Missing File',
                'message' => 'Please upload an accomplishment report before clocking out.',
            ]);
            return;
        }*/

        if (
            $this->accomplishment_type === 'Upload Accomplishment Report'
            && !$this->upload_accomplishment
        ) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Required',
                'message' => 'Please upload an accomplishment report.',
            ]);
        
            return;
        }

        if (
            ($this->status === 'Clock Out' || $this->isForcedOut)
            && empty($this->accomplishment_type)
        ) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Accomplishment Required',
                'message' => 'Please select your accomplishment before clocking out.',
            ]);
        
            return;
        }

        if (
            $this->accomplishment_type === 'Others'
            && empty($this->accomplishment_details)
        ) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Required',
                'message' => 'Please specify your accomplishment.',
            ]);
        
            return;
        }

        if (
            $this->accomplishment_type === 'Upload Accomplishment Report'
            && $this->upload_accomplishment
        ) {
            $file = $this->upload_accomplishment;
        
            $fileName = $this->employee_no . '_' . time() . '.' .
                $file->getClientOriginalExtension();
        
            $file->storeAs(
                'accomplishments',
                $fileName,
                'public'
            );
        
            $this->accomplishment = $fileName;
        }

        $service = app(ClockInOutService::class);

        $toProcess = [
            'timestamp' => Carbon::now(),
            'captured_image' => $this->imageCaptured,
            'captured_location' => $this->gps_location,
            'accomplishment' => $this->accomplishment ?? null,
            'accomplishment_type' => $this->accomplishment_type,
            'accomplishment_details' => $this->accomplishment_details,
        ];

        //dd($toProcess);

        $response = $service->process($this->entry, $toProcess, $this->employee_no);

        $this->dispatch('alert', [
            'showAlert' => true,
            'status' => $response['alert'],
            'title' => $response['title'],
            'message' => $response['message'],
        ]);

        $this->toggleStatus();
        
    }

    public function delete()
    {
        $date = now()->format('Y-m-d');
        EmployeeTimelogs::where('timestamp', 'like', "%{$date}%")->delete();
        $this->toggleStatus();
    }

    public function imageCaptured($imageData, $isFaceDetected = false, $isForcedOut = false)
    {
        $this->imageCaptured = $imageData;
        $this->isFaceDetected = $isFaceDetected;
        $this->isForcedOut = $isForcedOut;
    }

    public function updatedUploadAccomplishment()
    {
        
        $this->validateOnly('upload_accomplishment');
    }


    public function saveAccomplishment()
{
    

    $this->resetErrorBag();


    $this->validate();

    // Safety check
    if (!$this->upload_accomplishment) {
        $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'error',
            'title' => 'Missing File',
            'message' => 'Please upload an accomplishment report.',
        ]);
        return;
    }

    // Store the file
    $file = $this->upload_accomplishment;
    $fileName = $this->employee_no . '_' . time() . '.' . $file->getClientOriginalExtension();
    $path = $file->storeAs('accomplishments', $fileName, 'public');

    if (!$path || !Storage::disk('public')->exists($path)) {
        $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'error',
            'title' => 'Upload Failed',
            'message' => 'Accomplishment report could not be saved. Please check storage permissions or try again.',
        ]);
        return;
    }

    // Save the filename to DB or property
    $this->accomplishment = $fileName;

    // Optional: reset the property after upload if you want to allow re-upload
    $this->upload_accomplishment = null;

    // Trigger clock or next step
    $this->triggerClock();

    $this->dispatch('alert', [
        'showAlert' => true,
        'status' => 'success',
        'title' => 'Success',
        'message' => 'Accomplishment report uploaded successfully.',
    ]);
}

    public function render()
    {
        return view('livewire.employee.clock');
    }
}
