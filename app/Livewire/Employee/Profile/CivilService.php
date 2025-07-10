<?php

namespace App\Livewire\Employee\Profile;

use App\Models\EmployeeAccount;
use App\Models\EmployeeCivilService;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use App\Models\EmployeeUpdateCivilService;
use App\Notifications\Notifications;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CivilService extends Component
{
    public $employee_id;
    public $employee_no;
    public $originalData;
    public $records;

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $this->employee_no = Auth::user()->employee_no;
        $this->employee_id = Auth::user()->id;

        $updated = EmployeeUpdateCivilService::where('employee_no', $this->employee_no)
            ->get()
            ->toArray() ?? [];
        $stored = EmployeeCivilService::where('employee_no', $this->employee_no)
            ->get()
            ->toArray() ?? [];

        $data = $updated ?? $stored;


        $this->originalData = $data;
        $this->records = $data;

    }

      public function addRecord() {
        if (isset($this->defaultFields)) {
            $this->records[] = $this->defaultFields;
        }
    }

    public function removeRecord($index) {
        if (isset($this->records[$index])) {
            unset($this->records[$index]);
            $this->records = array_values($this->records);
        }
    }

    private $defaultFields = [
        'certification' => '',
        'rating' => '',
        'date_exam' => '',
        'place_exam' => '',
        'license_no' => '',
        'date_validity' => '',
        'documents' => ''
    ];

    protected function rules(?string $employee_no = null) {
        return [
            'records.*.certification' => 'required|string|max:255',
            'records.*.rating' => 'required|string|max:255',
            'records.*.date_exam' => 'required|string|max:255',
            'records.*.place_exam' => 'required|string|max:255',
            'records.*.license_no' => 'nullable|string|max:255',
            'records.*.date_validity' => 'nullable|date',
            'records.*.documents' => 'nullable|mimes:jpg,png,jpeg,pdf',
            'records.*.documents' => function ($attribute, $value, $fail) {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                $files = is_array($value) ? $value : [$value];
                foreach ($files as $file) {
                    if ($file instanceof TemporaryUploadedFile) {
                        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                            $fail("The document must be a JPEG, PNG, or PDF file.");
                        }
                    } 
                }
            },
        ];
    }

    protected function messages() {
        return [
            'records.*.certification.required' => '* required',
            'records.*.rating.required' => '* required',
            'records.*.date_exam.required' => '* required',
            'records.*.place_exam.required' => '* required',
            'records.*.license_no.required' => '* required',
            'records.*.date_validity.required' => '* required',
            'records.*.date_validity.date' => '* required',
        ];
    }

    public function hasChanges()
    {
        $originalData = $this->originalData ?? [];
        $records = $this->records;

        foreach ($records as $key => $newValue) {
            if (!array_key_exists($key, $originalData)) {
                return true;
            }

            $oldValue = $originalData[$key];

            if ($newValue !== $oldValue) {
                return true;
            }
        }

        return false;
    }

    public function setErrorActiveTabAccordions(array $errorKeys) {
        $this->dispatch('scrollToError', $errorKeys);
    }

    private function uploadFile($employee_no, $identifier, $path, $file)
    {
        if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $record = EmployeePersonal::with([
                'children', 'employment_history', 'civil_service', 'trainings', 'others', 'skills'
            ])->where('employee_no', $employee_no)->first();
        
            if ($record) {
                if (!empty($record->$identifier)) {
                    Storage::disk('public')->delete("$path/{$record->$identifier}");
                }
        
                if ($identifier === 'documents') {
                    foreach (['children', 'employment_history', 'civil_service', 'trainings', 'others', 'skills'] as $relation) {
                        if ($record->$relation && !empty($record->$relation->$identifier)) {
                            Storage::disk('public')->delete("$path/{$record->$relation->$identifier}");
                        }
                    }
                }
            }
            
            $filename = uniqid(time()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs($path, $filename, 'public');
        
            return $filename;
        }
        
        $record = EmployeePersonal::where('employee_no', $employee_no)->first();
        return $record->$identifier ?? null;
        
    }

    public function save(bool $isNotify = true) {

        if(!$this->hasChanges()) {
            return $this->dispatch('alert', [
                'status' => 'info',
                'title' => 'Please be informed!',
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'Unable to save because no changes were made, feel free to edit or update your informations first before saving. Thank you!'
            ]);
        }

        $employee_no = $this->employee_no;
        $record = EmployeeInformation::where('employee_no', $employee_no)->first();
        
        if (!$record) {
            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops',
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'Error: Saving a non-existent employee!'
            ]);
        }

        try {
            $this->validate($this->rules($employee_no));
        } catch (ValidationException $e) {
            $this->setErrorActiveTabAccordions($e->validator->errors()->keys());
            throw $e;
        }

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Yes, I am sure that all the information I have provided is accurate and true. This ensures that there will be no issues as we proceed.';
            $action = 'save';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

            return;

        } else {
            
            DB::beginTransaction();

            try {

                $data = $this->records;

                $path = 'documents/' . $employee_no;

                $existingIds = EmployeeUpdateCivilService::where('employee_no', $employee_no)
                    ->pluck('id')
                    ->toArray();

                $dataIds = array_column($data, 'id');
                $missingIds = array_diff($existingIds, $dataIds);

                if (!empty($missingIds)) {
                    EmployeeUpdateCivilService::whereIn('id', $missingIds)->delete();
                }

                foreach ($data as $item) {
                    if (isset($item['id'])) {
                        if($item['documents'] instanceof TemporaryUploadedFile) {
                            $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                            $record = EmployeeUpdateCivilService::where('id', $item['id'])
                                ->where('employee_no', $employee_no)
                                ->first();
                            if ($record) {
                                $record->fill([
                                    'employee_no' => $employee_no,
                                    'certification' => $item['certification'],
                                    'rating' => $item['rating'],
                                    'date_exam' => $item['date_exam'],
                                    'place_exam' => $item['place_exam'],
                                    'license_no' => $item['license_no'],
                                    'date_validity' => $item['date_validity'],
                                    'documents' => $documents
                                ])->save();
                            } else {
                                EmployeeUpdateCivilService::create([
                                    'employee_no' => $employee_no,
                                    'certification' => $item['certification'],
                                    'rating' => $item['rating'],
                                    'date_exam' => $item['date_exam'],
                                    'place_exam' => $item['place_exam'],
                                    'license_no' => $item['license_no'],
                                    'date_validity' => $item['date_validity'],
                                    'documents' => $documents
                                ]);
                            }
                        } else {
                            $record = EmployeeUpdateCivilService::where('id', $item['id'])
                                ->where('employee_no', $employee_no)
                                ->first();
                            if ($record) {
                                $record->fill([
                                    'employee_no' => $employee_no,
                                    'certification' => $item['certification'],
                                    'rating' => $item['rating'],
                                    'date_exam' => $item['date_exam'],
                                    'place_exam' => $item['place_exam'],
                                    'license_no' => $item['license_no'],
                                    'date_validity' => $item['date_validity'],
                                ])->save();
                            } else {
                                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                                EmployeeUpdateCivilService::create([
                                    'employee_no' => $employee_no,
                                    'certification' => $item['certification'],
                                    'rating' => $item['rating'],
                                    'date_exam' => $item['date_exam'],
                                    'place_exam' => $item['place_exam'],
                                    'license_no' => $item['license_no'],
                                    'date_validity' => $item['date_validity'],
                                    'documents' => $documents
                                ]);
                            }
                        }
                    } else {
                        $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                        EmployeeUpdateCivilService::create([
                            'employee_no' => $employee_no,
                            'certification' => $item['certification'],
                            'rating' => $item['rating'],
                            'date_exam' => $item['date_exam'],
                            'place_exam' => $item['place_exam'],
                            'license_no' => $item['license_no'],
                            'date_validity' => $item['date_validity'],
                            'documents' => $documents
                        ]);
                    }
                }
                        
                DB::commit();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'isRemoveRowDT' => false,
                    'isReloadDT' => false,
                    'message' => 'You\'re profile is now in pending for HR\'s approval. We\'ll sent you a notification once approved. Thank you!',
                ]);

                $user = EmployeeAccount::find($this->employee_id);
                $message = 'Employee <strong>' . $this->employee_no . '</strong> has submitted his/her updated <strong>profile information</strong>.';
                $redirect = route('ess.approval-profile.edit', ['approval' => $user->employee_no]);
                $user->notify(new Notifications('info', $message, $redirect, 'admin'));

                return;
                
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->dispatch('alert', [
                    'status' => 'error',
                    'title' => 'Oops!',
                    'isRemoveRowDT' => true,
                    'showAlert' => true,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }

    }

    public function render()
    {
        return view('livewire.employee.profile.civil-service');
    }
}
