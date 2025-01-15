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

        if (!$this->upload_preview) {
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

            $relativePath = str_replace(asset('storage/'), '', $this->upload_preview);
            $absolutePath = storage_path('app/public/' . $relativePath);
    
            if (!Storage::exists('public/' . $relativePath)) {
                throw new \Exception('File does not exist in storage.');
            }
            
            if (($handle = fopen($absolutePath, 'r')) !== false) {
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
            
                $this->checkIfValid($headers, $requiredHeaders);
                
                $csvData = [];
                while (($row = fgetcsv($handle)) !== false) {
                    $csvData[] = array_combine($headers, $row);
                }
                fclose($handle);
            
                $formattedData = $this->formatCsvData($csvData);
                $insertedCount = $this->insertFormattedData($formattedData);
            }

            DB::commit();

            $date = array_key_first($formattedData);
            $date = Carbon::createFromFormat('d/m/Y', $date)->format('F Y'); 
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
        
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'isRemoveRowDT' => true,
                'showAlert' => true,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        } finally {
            $this->isUploading = false;
        }
    }

    private function formatCsvData(array $csvData): array {
        $formattedData = [];

        foreach ($csvData as $record) {
            $logDate = Carbon::createFromFormat('d/m/Y H:i:s', $record['logdatetime'])->format('d/m/Y');
            $logTime = Carbon::createFromFormat('d/m/Y H:i:s', $record['logdatetime'])->format('h:i A');
            $timePeriod = (int) Carbon::createFromFormat('d/m/Y H:i:s', $record['logdatetime'])->format('H') < 12 ? 'am' : 'pm';

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
                    'times' => []
                ];
            }

            $formattedData[$logDate][$record['bsdno']]['times'][] = [
                'time' => $logTime,
                'timePeriod' => $timePeriod
            ];
        }

        foreach ($formattedData as &$dateData) {
            foreach ($dateData as &$recordData) {
                $shift = $this->employeeShift($recordData['bsdno']);
                $breaktime_from = $shift ? Carbon::parse($shift->break_out) : null;
                $breaktime_to = $shift ? Carbon::parse($shift->break_in) : null;

                usort($recordData['times'], fn($a, $b) => strtotime($a['time']) - strtotime($b['time']));
                $timesCount = count($recordData['times']);

                if ($timesCount > 4) {
                    $uniqueTimes = [];
                    foreach ($recordData['times'] as $key => $timeRecord) {
                        if (!in_array($timeRecord['time'], $uniqueTimes) || 
                            (isset($recordData['times'][$key - 1]) && $recordData['times'][$key - 1]['time'] === $timeRecord['time'])) {
                            $uniqueTimes[] = $timeRecord['time'];
                        } else {
                            unset($recordData['times'][$key]);
                        }
                    }
                    $recordData['times'] = array_values($recordData['times']);
                    $timesCount = count($recordData['times']);
                }

                if ($timesCount == 4) {
                    $recordData['clock_in_am'] = $recordData['times'][0]['time'];
                    $recordData['clock_out_am'] = $recordData['times'][1]['time'];
                    $recordData['clock_in_pm'] = $recordData['times'][2]['time'];
                    $recordData['clock_out_pm'] = $recordData['times'][3]['time'];
                } elseif ($timesCount == 3) {
                    $earliestTime = $recordData['times'][0]['time'];
                    $latestTime = $recordData['times'][2]['time'];
                    $middleTime = $recordData['times'][1]['time'];

                    $recordData['clock_in_am'] = $earliestTime;
                    $recordData['clock_out_pm'] = $latestTime;

                    if ($breaktime_from && strtotime($middleTime) < strtotime($breaktime_from)) {
                        $recordData['clock_out_am'] = $middleTime;
                    } elseif ($breaktime_to && strtotime($middleTime) >= strtotime($breaktime_to)) {
                        $recordData['clock_in_pm'] = $middleTime;
                    } else {
                        if (!$recordData['clock_out_am']) {
                            $recordData['clock_out_am'] = $middleTime;
                        } else {
                            $recordData['clock_out_pm'] = $middleTime;
                        }
                    }
                } elseif ($timesCount == 2) {
                    $firstTime = $recordData['times'][0]['time'];
                    $secondTime = $recordData['times'][1]['time'];

                    if (strtotime($firstTime) < strtotime($breaktime_from)) {
                        $recordData['clock_in_am'] = $firstTime;
                    }

                    if (strtotime($secondTime) >= strtotime($breaktime_from) && strtotime($secondTime) <= strtotime($breaktime_to)) {
                        $recordData['clock_out_pm'] = $secondTime;
                    } elseif (strtotime($secondTime) > strtotime($breaktime_to)) {
                        $recordData['clock_in_pm'] = $secondTime;
                        $recordData['clock_out_am'] = null;
                        $recordData['clock_out_pm'] = $secondTime;

                        if ($recordData['clock_in_pm'] === $recordData['clock_out_pm']) {
                            $recordData['clock_in_pm'] = null;
                        }
                    }
                } elseif ($timesCount == 1) {
                    $singleTime = $recordData['times'][0]['time'];

                    if (strtotime($singleTime) < strtotime($breaktime_from)) {
                        $recordData['clock_in_am'] = $singleTime;
                    } elseif (strtotime($singleTime) > strtotime($breaktime_to)) {
                        $recordData['clock_in_pm'] = $singleTime;
                    }
                }
            }
        }

        return array_map(fn($dateData) => ksort($dateData) ? $dateData : $dateData, $formattedData);
    }

    private function insertFormattedData(array $formattedData): int {
        $insertedCount = 0;

        foreach ($formattedData as $index => $data) {
            foreach ($data as $item) {
                if (empty($item['clock_in_am']) || empty($item['clock_out_pm'])) {
                    continue;
                }

                $timestamp = Carbon::createFromFormat('d/m/Y H:i:s', $item['logdatetime'])->timestamp;
                $date = Carbon::createFromTimestamp($timestamp)->format('Y-m-d H:i:s');
                $clockInTime = Carbon::createFromFormat('h:i A', $item['clock_in_am']);
                $expectedClockOut = $clockInTime->copy()->addHours(8);
                $actualClockOutTime = Carbon::createFromFormat('h:i A', $item['clock_out_pm']);

                $minsOT = 0;
                $regMins = $actualClockOutTime->diffInMinutes($clockInTime);

                if ($actualClockOutTime->gt($expectedClockOut)) {
                    $minsOT = $actualClockOutTime->diffInMinutes($expectedClockOut);
                    $regMins -= $minsOT;
                }

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

        return $insertedCount;
    }

    public function checkIfValid($headers, $requiredHeaders) {
        $missingHeaders = array_diff($requiredHeaders, $headers);
        if (!empty($missingHeaders)) {
            throw new \Exception('The following required headers are missing: ' . implode(', ', $missingHeaders));
        }
    }

    public function employeeShift($bsd_no) {
        $shift = EmployeeInformation::select('shift_id')->where('bsd_no', $bsd_no)->first();
    
        if (is_null($shift) || is_null($shift->shift_id)) {
            return null;
        }
    
        return ShiftSchedule::find($shift->shift_id);
    }

    public function render() {
        return view('livewire.admin.timekeeping.upload');
    }
}
