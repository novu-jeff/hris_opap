<?php

namespace App\Livewire\Home;

use App\Models\InterviewItemsResponses;
use App\Models\JobApplicants;
use App\Models\JobApplicantsInterview;
use App\Models\JobApplicantsOffer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class SignedOffer extends Component
{

    use WithFileUploads;

    public $user_id;
    public $job_id;
    public $record = [];
    public $offer;
    public $preview_offer;

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $id = Auth::guard('applicants')->user()->id;
        $this->user_id = $id;
        
        $record = JobApplicants::with('job', 'offer')
            ->where('user_id', $id)
            ->where('job_id', $this->job_id)
            ->where('status', 'placement')
            ->first();

        if(!$record) {
            return redirect()->route('home.profile.index');
        }

        $this->record = $record;

    }

    protected function rules() {
        return [
            'offer' => 'required|file|mimes:doc,docx,docs,pdf'
        ];
    }
    
    protected $messages = [
        'offer.required' => 'The file upload is required.',
        'offer.file' => 'The uploaded item must be a file.',
        'offer.mimes' => 'The file must be a document of type: doc, docx, docs, pdf.',
    ];
    
    public function updated($propertyName) {
        

        if ($propertyName == 'offer') {

            if (isset($this->offer)) {
                
                $file = $this->offer;

                if ($file instanceof \Illuminate\Http\UploadedFile) {

                    $extension = strtolower($file->getClientOriginalExtension());

                    if (in_array($extension, ['pdf'])) {
                        $filename = $file->store('public/temp'); 
                        $url = Storage::url($filename); 
    
                        return $this->preview_offer = $url;
                    } 

                    $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Oops!', 
                        'isRemoveRowDT' => false,
                        'message' => 'Attachment must be PDF.'
                    ]);
                  

                } else {    
                    $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Oops!', 
                        'isRemoveRowDT' => false,
                        'message' => 'Error: Invalid File'
                    ]);
                }
            }
        }
        
    }

    public function save(bool $isNotify = true) {

        $this->validate();

        if($isNotify) {
            return $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to submit this reponse?',
                'message' => 'Please be informed that once submitted, you\re not be able to edit your responses.',
                'action' => 'save'
            ]);
        }
        
       DB::beginTransaction();
       
        try {

            $attachment = $this->offer;
            $extension = $attachment->getClientOriginalExtension(); 
            $filename = 'signed_offer_' . str_replace(' ', '_', $this->record->job->position 
                . '_' . time()) 
                . '.' . $extension;
            $attachment->storeAs('public/applicant/users/' . $this->user_id .'/' . $this->job_id . '/offers', strtolower($filename));


            JobApplicantsOffer::where('id', $this->record->offer->id)
                ->update([
                    'signed_attachment' => strtolower($filename)
                ]);

            JobApplicants::where('id', $this->record->id)
                ->where('job_id', $this->record->job_id)
                ->update([
                    'isSignedJobOffer' => true
                ]);

            DB::commit();

            $this->loadRecords();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Response Saved!', 
                'showAlert' => true,
                'message' => 'Your response has been recorded and will be viewed by the HR team.'
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

    public function go_back() {
        session()->put('target', [
            'page' => 'profile',
            'tab' => 'placement',
            'accordion' => '',
        ]);
        return redirect()->route('home.profile.index');
    }

    public function render()
    {
        return view('livewire.home.signed-offer');
    }
}
