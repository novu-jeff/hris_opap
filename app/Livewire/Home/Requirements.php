<?php

namespace App\Livewire\Home;

use App\Models\InterviewItemsResponses;
use App\Models\JobApplicants;
use App\Models\JobApplicantsInterview;
use App\Models\JobApplicantsOffer;
use App\Models\JobApplicantsRequirements;
use App\Models\JobRequirements;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Str;

class Requirements extends Component
{

    use WithFileUploads;

    public $user_id;
    public $job_id;
    public $record = [];
    public $requirements;
    public $responses;
    public $previews = [];


    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
        $this->loadRequirements(true);
    }

    public function loadRecords() {

        $id = Auth::guard('applicants')->user()->id;
        $this->user_id = $id;
        
        $record = JobApplicants::with('job', 'requirements')
            ->where('user_id', $id)
            ->where('job_id', $this->job_id)
            ->where('status', 'onboarding')
            ->first();

        if(!$record) {
            return redirect()->route('home.profile.index');
        }

        $this->record = $record;
        
        if(!$record->requirements->isEmpty()) {
            foreach($record->requirements as $index => $requirements) {
                $this->responses[$index] = [
                    'type' => $requirements->requirement_id ,
                    'document' => $requirements->attachment
                ];
                $this->previews[$index] = [
                    'type' => file_type($requirements->attachment),
                    'url' => Storage::url('applicant/users/' . $this->user_id .'/' . $this->job_id . '/requirements/' . $requirements->attachment),
                ];
            }
        } else {
            $this->responses = [];
        }

    }

    public function loadRequirements(bool $isMount = false) {
        $allRequirements = JobRequirements::all();
        $selectedIds = collect($this->responses)->pluck('type')->filter()->toArray() ?? [];
       
        if(!$isMount) {
            $this->requirements = $allRequirements->whereNotIn('id', $selectedIds)->values(); 
        } else {
            $this->requirements = $allRequirements;
        }

    }

    public function add_item() {
        $this->loadRequirements(false);
        $this->responses[] = [
            'type' => '',
            'document' => ''
        ];
    }

    public function remove_item(int $index) {
        if (isset($this->responses[$index])) {
            $response = $this->responses[$index];
    
            if (isset($response['document']) && !($response['document'] instanceof \Illuminate\Http\UploadedFile)) {
                $filename = $response['document']; 
    
                $path = 'public/applicant/users/' . $this->user_id . '/' . $this->job_id . '/requirements/' . $filename;
    
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
    
                JobApplicantsRequirements::where('job_applicants_id', $this->job_id)
                    ->where('requirement_id', $response['type'])
                    ->delete();
            }
    
            unset($this->responses[$index]);
            unset($this->previews[$index]);
        }
    }
    
   

    public function updated($propertyName) {

        if (Str::endsWith($propertyName, '.type')) {
            $this->loadRequirements(false);
        }

        if (Str::endsWith($propertyName, '.document')) {
            
            $parts = explode('.', $propertyName);
            $index = $parts[1] ?? null;

            if ($index !== null && isset($this->responses[$index]['document'])) {
                
                $file = $this->responses[$index]['document'];

                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $extension = strtolower($file->getClientOriginalExtension());

                    if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                        $this->previews[$index] = [
                            'type' => 'image',
                            'url' => $file->temporaryUrl(),
                        ];
                    } elseif ($extension === 'pdf') {
                        $filename = $file->store('public/temp'); 
                        $url = Storage::url($filename); 

                        $this->previews[$index] = [
                            'type' => 'pdf',
                            'url' => $url,
                        ];
                    }
                } else {    
                    $this->validate();
                }
            }
        }
    }


    protected function rules() {
        $rules = [
            'responses' => 'required|array',
            'responses.*.type' => 'required|exists:job_requirements,id',
        ];
    
        foreach ($this->responses as $index => $response) {
            if (is_null($response['document'])) {
                $rules['responses.' . $index . '.document'] = 'required|file|mimes:jpg,jpeg,png,doc,docx,docs,pdf';
            } 
        }
    
        return $rules;
    }
    
    protected $messages = [
        'responses.required' => 'The response is required.',
        
        'responses.*.type.required' => 'Requirement type is required.',
        'responses.*.type.exists' => 'Requirement type does not exists',

        'responses.*.document.required' => 'Requirement document is required.',
        'responses.*.document.file' => 'Requirement document is invalid.',
        'responses.*.document.mimes' => 'Requirement document only accepts: jpg, jpeg, png, doc, docx, docs, pdf.',
    ];
    
    public function save(bool $isNotify = true) {

        if($isNotify) {
            return $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to submit your requirements?',
                'message' => 'Please be informed that after submission you can be able to edit or update your requirements.',
                'action' => 'save'
            ]);
        }
        
       DB::beginTransaction();
       
        try {

            foreach ($this->responses as $index => $response) {

                $attachment = $response['document'];
                $requirementId = $response['type'];
    
                if ($attachment instanceof \Illuminate\Http\UploadedFile) {

                    $extension = $attachment->getClientOriginalExtension();
                    $filename = 'requirement_' . Str::uuid() . '_' . str_replace(' ', '_', $this->record->job->position . '_' . time()) . '.' . $extension;
                
                    $path = 'public/applicant/users/' . $this->user_id . '/' . $this->job_id . '/requirements';
                    
                    $attachment->storeAs($path, strtolower($filename));
                
                    $existingRecord = JobApplicantsRequirements::where('job_applicants_id', $this->job_id)
                    ->where('requirement_id', $requirementId)
                    ->first();

                    if ($existingRecord && $existingRecord->attachment) {
                        $previousPath = 'public/applicant/users/' . $this->user_id . '/' . $this->job_id . '/requirements/' . $existingRecord->attachment;
                        Storage::delete($previousPath); 
                    }

                    JobApplicantsRequirements::updateOrInsert(
                        [
                            'job_applicants_id' => $this->job_id,
                            'requirement_id' => $requirementId
                        ],
                        [
                            'attachment' => strtolower($filename),
                        ]
                    );
                } else {
                    JobApplicantsRequirements::updateOrInsert(
                        [
                            'job_applicants_id' => $this->job_id,
                            'requirement_id' => $requirementId
                        ],
                        [
                            'attachment' => $attachment 
                        ]
                    );
                }
            }
            
            

            DB::commit();


            $this->reset('responses');

            $this->loadRecords();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Requirements Saved!', 
                'showAlert' => true,
                'message' => 'Your requirements has been saved and will be viewed by the HR team.'
            ]);

        } catch (\Exception $e) {
            
            DB::rollBack();

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!', 
                'showAlert' => true,
                'message' => 'Error occured: ' . $e->getMessage()
            ]);
        }

    }

    public function render()
    {
        return view('livewire.home.requirements');
    }
}
