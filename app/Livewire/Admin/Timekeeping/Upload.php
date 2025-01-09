<?php

namespace App\Livewire\Admin\Timekeeping;

use App\Models\EmployeeClockInOut;
use App\Models\EmployeeInformation;
use App\Models\ShiftSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Upload extends Component
{

    use WithFileUploads;

    public $file;
    public $upload_preview;
    public object $records;
    public bool $isParsing, $isUploading = false;

    public function updatedFile() {

        if ($this->file) {
            $this->upload_preview;
            $file = $this->file;

            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $extension = strtolower($file->getClientOriginalExtension());

                if (in_array($extension, ['csv'])) {
                    try {

                        $files = Storage::files('public/temp/files');

                        Storage::delete($files); 

                        $fileName = uniqid() . '.' . $extension;

                        $file->storeAs('public/temp/files', $fileName);

                        $this->upload_preview = asset('storage/temp/files/' . $fileName);

                        $this->isParsing = false;

                    } catch (\Exception $e) {
                        $this->addError('file', 'There was an error saving the file to temporary storage.');
                        $this->isParsing = false;
                    }
                } else {
                    $this->addError('file', 'The file must be in csv format.');
                }
            }

            $this->file = null;
        } else {
            $this->isParsing = true;
        }

    }

    public function upload_file() {

        if (Gate::denies('write timelogs')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        if(!($this->upload_preview)) {
            return $this->dispatch('alert', [
                'status' => 'warning',
                'title' => 'Please be informed',
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'File logs are required!',
            ]);
        }

        $this->isUploading = false;
    
        DB::beginTransaction();
    
        try {
            // Correct file path using the Storage facade
            $relativePath = str_replace(asset('storage/'), '', $this->upload_preview);
            $absolutePath = storage_path('app/public/' . $relativePath);
    
            // Check if the file exists in the storage
            if (!Storage::exists('public/' . $relativePath)) {
                throw new \Exception('File does not exist in storage.');
            }
            
            if (($handle = fopen($absolutePath, 'r')) !== false) {
                $csvData = [];
                
                // Get the headers (first row) and add them to the data array
                $headers = fgetcsv($handle);
                $requiredHeaders = [
                    "biometricdtrid",
                    "bsdno",
                    "isindtr",
                    "logdatetime",
                    "nfcdeviceid",
                    "type",
                    "ismanual",
                ];
            
            
                // Ensure all required headers are present
                $missingHeaders = array_diff($requiredHeaders, $headers);
                if (!empty($missingHeaders)) {
                    // Handle the missing headers (e.g., throw an exception or return an error)
                    throw new \Exception('Uploading an invalid csv file for logs!');
                }
                
                while (($row = fgetcsv($handle)) !== false) {
                    $csvData[] = array_combine($headers, $row);
                }
            
                fclose($handle);
            
                $formattedData = [];

                foreach ($csvData as $record) {
                    // Convert the logdatetime to a date format (you can adjust based on your needs)
                    $logDate = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $record['logdatetime'])->format('d/m/Y');
                    
                    // Split the time of the log into AM and PM
                    $logTime = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $record['logdatetime'])->format('h:i A');
                    $timePeriod = (int) \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $record['logdatetime'])->format('H') < 12 ? 'am' : 'pm';
                    
                    // Create the nested structure based on the date and bsdno
                    if (!isset($formattedData[$logDate])) {
                        $formattedData[$logDate] = [];
                    }
                
                    if (!isset($formattedData[$logDate][$record['bsdno']])) {
                        $formattedData[$logDate][$record['bsdno']] = [
                            'biometricdtrid' => $record['biometricdtrid'],
                            'logdatetime' => $record['logdatetime'],
                            'clock_in_am' => null,
                            'clock_out_am' => null,
                            'clock_in_pm' => null,
                            'clock_out_pm' => null,
                            'bsdno' => $record['bsdno'],
                            'isindtr' => $record['isindtr'],
                            'nfcdeviceid' => $record['nfcdeviceid'],
                            'type' => $record['type'],
                            'ismanual' => $record['ismanual'],
                            'times' => [] // Store the times for sorting
                        ];
                    }
                    
                    // Store the times in the 'times' array (will sort later)
                    $formattedData[$logDate][$record['bsdno']]['times'][] = [
                        'time' => $logTime,
                        'timePeriod' => $timePeriod
                    ];
                }

                
                foreach ($formattedData as &$dateData) {
                    foreach ($dateData as &$recordData) {
                        $shift = $this->employeeShift($recordData['bsdno']);
                        
                        // If no shift is found, assign default values (or skip, depending on your business logic)
                        if (!$shift) {
                            // You can choose to use a default shift or just proceed with null values for break times
                            $breaktime_from = null;
                            $breaktime_to = null;
                        } else {
                            $breaktime_from = Carbon::parse($shift->break_out);
                            $breaktime_to = Carbon::parse($shift->break_in);
                        }
                
                        // Sort times by actual time
                        usort($recordData['times'], function ($a, $b) {
                            return strtotime($a['time']) - strtotime($b['time']);
                        });
                
                        // Check how many recorded times we have
                        $timesCount = count($recordData['times']);
                
                        // Initialize an empty array for remarks
                        $remarks = [];
                
                        // Only check for duplicates if there are more than 4 records in the 'times' array
                        if ($timesCount > 4) {
                            // Check for duplicates in the 'times' array, but allow valid cases (e.g., clock_out_am and clock_in_pm being the same)
                            $uniqueTimes = [];
                            foreach ($recordData['times'] as $key => $timeRecord) {
                                // Allow duplicate times only if they are consecutive
                                if (!in_array($timeRecord['time'], $uniqueTimes) || 
                                    (isset($recordData['times'][$key - 1]) && $recordData['times'][$key - 1]['time'] === $timeRecord['time'])) {
                                    $uniqueTimes[] = $timeRecord['time'];
                                } else {
                                    // Remove the truly duplicate time record
                                    unset($recordData['times'][$key]);
                                }
                            }
                            // Re-index the array after removing duplicates
                            $recordData['times'] = array_values($recordData['times']);
                            // Update timesCount after duplicates are removed
                            $timesCount = count($recordData['times']);
                        }                        
                
                        // Handle 4 times (standard in and out times)
                        if ($timesCount == 4) {
                            $recordData['clock_in_am'] = $recordData['times'][0]['time'];
                            $recordData['clock_out_am'] = $recordData['times'][1]['time'];
                            $recordData['clock_in_pm'] = $recordData['times'][2]['time'];
                            $recordData['clock_out_pm'] = $recordData['times'][3]['time'];
                
                            // Check for missing times and add remarks
                            if (!$recordData['clock_in_am']) {
                                $remarks[] = 'Missing clock in for AM';
                            }
                            if (!$recordData['clock_out_am']) {
                                $remarks[] = 'Missing clock out for AM';
                            }
                            if (!$recordData['clock_in_pm']) {
                                $remarks[] = 'Missing clock in for PM';
                            }
                            if (!$recordData['clock_out_pm']) {
                                $remarks[] = 'Missing clock out for PM';
                            }
                        }
                
                        // Handle 3 times, consider break time logic
                        elseif ($timesCount == 3) {
                            $earliestTime = $recordData['times'][0]['time'];
                            $latestTime = $recordData['times'][2]['time'];
                            $middleTime = $recordData['times'][1]['time'];
                
                            // Earliest time goes to clock_in_am
                            $recordData['clock_in_am'] = $earliestTime;
                
                            // Latest time goes to clock_out_pm
                            $recordData['clock_out_pm'] = $latestTime;
                
                            // Handle middle time based on its position (before or after break)
                            if ($breaktime_from && strtotime($middleTime) < strtotime($breaktime_from)) {
                                $recordData['clock_out_am'] = $middleTime; // Before break, assign to clock_out_am
                            } elseif ($breaktime_to && strtotime($middleTime) >= strtotime($breaktime_to)) {
                                $recordData['clock_in_pm'] = $middleTime; // After break, assign to clock_in_pm
                            } else {
                                // If between breaktime, assign it as clock_out_am or clock_out_pm
                                if (!$recordData['clock_out_am']) {
                                    $recordData['clock_out_am'] = $middleTime; // If no clock_out_am, use it
                                } else {
                                    $recordData['clock_out_pm'] = $middleTime; // Otherwise, use it for clock_out_pm
                                }
                            }
                
                            // Check for missing times and add remarks
                            if (!$recordData['clock_in_am']) {
                                $remarks[] = 'Missing clock in for AM';
                            }
                            if (!$recordData['clock_out_am']) {
                                $remarks[] = 'Missing clock out for AM';
                            }
                            if (!$recordData['clock_in_pm']) {
                                $remarks[] = 'Missing clock in for PM';
                            }
                            if (!$recordData['clock_out_pm']) {
                                $remarks[] = 'Missing clock out for PM';
                            }
                        }
                
                        // Handle 2 times (one clock in and one clock out)
                        elseif ($timesCount == 2) {
                            $firstTime = $recordData['times'][0]['time'];
                            $secondTime = $recordData['times'][1]['time'];
                
                            // Check if the first time is before break time
                            if (strtotime($firstTime) < strtotime($breaktime_from)) {
                                // Assign the first time to clock_in_am
                                $recordData['clock_in_am'] = $firstTime;
                            }
                
                            // Check if the second time is during break time
                            if (strtotime($secondTime) >= strtotime($breaktime_from) && strtotime($secondTime) <= strtotime($breaktime_to)) {
                                // If the second record is during break time, assign to clock_out_pm
                                $recordData['clock_out_pm'] = $secondTime;
                            } elseif (strtotime($secondTime) > strtotime($breaktime_to)) {
                                // If the second record is after break time, assign to clock_in_pm
                                $recordData['clock_in_pm'] = $secondTime;
                
                                // Since we have a second clock time after the break, leave clock_out_am as null
                                $recordData['clock_out_am'] = null;
                
                                // Assign the second time to clock_out_pm
                                $recordData['clock_out_pm'] = $secondTime;
                
                                // If clock_in_pm and clock_out_pm are the same, set clock_in_pm to null
                                if ($recordData['clock_in_pm'] === $recordData['clock_out_pm']) {
                                    $recordData['clock_in_pm'] = null;
                                }
                            }
                
                            // Check for missing times and add remarks
                            if (!$recordData['clock_in_am']) {
                                $remarks[] = 'Missing clock in for AM';
                            }
                            if (!$recordData['clock_out_am']) {
                                $remarks[] = 'Missing clock out for AM';
                            }
                            if (!$recordData['clock_in_pm']) {
                                $remarks[] = 'Missing clock in for PM';
                            }
                            if (!$recordData['clock_out_pm']) {
                                $remarks[] = 'Missing clock out for PM';
                            }
                        }
                
                        // Handle case if we only have 1 recorded time
                        elseif ($timesCount == 1) {
                            $singleTime = $recordData['times'][0]['time'];
                
                            // Check if the single time is before break time
                            if (strtotime($singleTime) < strtotime($breaktime_from)) {
                                // If before break time, assign to clock_in_am
                                $recordData['clock_in_am'] = $singleTime;
                            } elseif (strtotime($singleTime) > strtotime($breaktime_to)) {
                                // If after break time, assign to clock_in_pm
                                $recordData['clock_in_pm'] = $singleTime;
                            }
                
                            // Check for missing times and add remarks
                            if (!$recordData['clock_in_am']) {
                                $remarks[] = 'Missing clock in for AM';
                            }
                            if (!$recordData['clock_out_am']) {
                                $remarks[] = 'Missing clock out for AM';
                            }
                            if (!$recordData['clock_in_pm']) {
                                $remarks[] = 'Missing clock in for PM';
                            }
                            if (!$recordData['clock_out_pm']) {
                                $remarks[] = 'Missing clock out for PM';
                            }
                        }
                
                        // Implode remarks into a single sentence and assign it to 'remarks' field
                        if (!empty($remarks)) {
                            $recordData['remarks'] = implode(' and ', $remarks);
                        }
                    }
                }
                   
                // Sort the dates and records
                $formattedData = array_map(function ($dateData) {
                    ksort($dateData);
                    return $dateData;
                }, $formattedData);
                
                $insertedCount = 0;

                foreach ($formattedData as $index => $data) {
                    foreach ($data as $item) {
                        // Skip if clock_in_am or clock_out_pm is null
                        if (empty($item['clock_in_am']) || empty($item['clock_out_pm'])) {
                            continue; // Skip to the next iteration
                        }
                
                        // Parse the logdatetime
                        $timestamp = Carbon::createFromFormat('d/m/Y H:i:s', $item['logdatetime'])->timestamp;
                        $date = Carbon::createFromTimestamp($timestamp)->format('Y-m-d H:i:s');
                        
                        // Parse the clock-in time and calculate the expected clock-out time (9 hours later)
                        $clockInTime = Carbon::createFromFormat('h:i A', $item['clock_in_am']);
                        $expectedClockOut = $clockInTime->copy()->addHours(8);
                
                        // Parse actual clock-out time
                        $actualClockOutTime = Carbon::createFromFormat('h:i A', $item['clock_out_pm']);
                        
                        $minsOT = 0;
                        $regMins = 0;
                
                        $regMins = $actualClockOutTime->diffInMinutes($clockInTime);

                        // Calculate overtime if the actual clock-out exceeds the expected clock-out
                        if ($actualClockOutTime->gt($expectedClockOut)) {
                            $minsOT = $actualClockOutTime->diffInMinutes($expectedClockOut);                            
                            $regMins = $regMins - $minsOT;
                        } else {
                            // No overtime, calculate regular minutes only
                            $regMins = $clockInTime->diffInMinutes($actualClockOutTime);
                        }
                
                        // Insert or update record in the database
                        $insertion = EmployeeClockInOut::updateOrInsert(
                            [
                                'biometricdtrid' => $item['biometricdtrid'] ?? null,
                            ],
                            [
                                'origin' => 'biometrics',
                                'biometricdtrid' => $item['biometricdtrid'] ?? null,
                                'clock_in_am' => $item['clock_in_am'] ?? null,
                                'clock_out_am' => $item['clock_out_am'] ?? null,
                                'clock_in_pm' => $item['clock_in_pm'] ?? null,
                                'clock_out_pm' => $item['clock_out_pm'] ?? null,
                                'captured_image_clockin' => null,
                                'captured_image_clockout' => null,
                                'captured_location_clockin' => null,
                                'captured_location_clockout' => null,
                                'bsd_no' => $item['bsdno'] ?? null,
                                'isindtr' => !empty($item['isindtr']) ? (bool) $item['isindtr'] : null,
                                'nfcdeviceid' => $item['nfcdeviceid'] ?? null,
                                'type' => $item['type'] ?? 0,
                                'ismanual' => !empty($item['ismanual']) ? (bool) $item['ismanual'] : null,
                                'created_at' => $date,
                                'updated_at' => $date,
                            ]
                        );
                
                        if ($insertion) {
                            $insertedCount++;
                        }
                    }
                }
                              
            }

            DB::commit();

            $formattedDate = Carbon::parse($date)->format('F Y');

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Yey!', 
                'isRemoveRowDT' => false,
                'redirect' => route('timekeeping.upload'),
                'message' => 'Total of ' . rtrim(number_format($insertedCount, 2), '.00') . ' records has been added to time logs for the month of ' . $formattedDate 
            ]);
            
        } catch (\Exception $e) {
            
            DB::rollBack();
    
            logger()->error('Error uploading file: ' . $e->getMessage());
    
            // Dispatch error message to frontend
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'isRemoveRowDT' => true,
                'showAlert' => true,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        } finally {
            // Ensure `isUploading` is set to false
            $this->isUploading = false;
        }
    }

    public function employeeShift($bsd_no) {
        $shift = EmployeeInformation::select('shift_id')->where('bsd_no', $bsd_no)->first();
    
        // Check if shift_id is null
        if (is_null($shift) || is_null($shift->shift_id)) {
            return null;
        }
    
        // Find the shift schedule record
        $record = ShiftSchedule::find($shift->shift_id);
    
        // Check if the record is null
        if (is_null($record)) {
            return null;
        }
    
        return $record;
    }

    public function render() {
        return view('livewire.admin.timekeeping.upload');
    }
}
