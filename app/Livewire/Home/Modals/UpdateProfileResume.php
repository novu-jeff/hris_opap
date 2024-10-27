<?php

namespace App\Livewire\Home\Modals;

use App\Livewire\Home\Profile;
use App\Models\ApplicantUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateProfileResume extends Component
{

    use WithFileUploads;

    public $user_id;
    public $resume;
    public $resume_preview;
    protected $listeners = ['remove_resume', 'save_resume'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $id = Auth::guard('applicants')->user()->id;
        $record = ApplicantUsers::where('id', $id)->first();

        $this->user_id = $id;        
        $this->resume = $record->resume;
        $this->resume_preview = $record->resume ? Storage::url('public/applicant/users/' . $this->user_id .'/' . $record->resume) : null;
    }

    public function updated($propertyName) {

        if ($propertyName === 'resume') {
            
            if (isset($this->resume)) {
                
                $file = $this->resume;

                if ($file instanceof \Illuminate\Http\UploadedFile) {

                    $extension = strtolower($file->getClientOriginalExtension());

                    if (in_array($extension, ['pdf'])) {
                        $filename = $file->store('public/temp'); 
                        $url = Storage::url($filename); 

                        return $this->resume_preview = $url;
                    } 

                    $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Oops!', 
                        'isRemoveRowDT' => false,
                        'message' => 'Profile image must be an image.'
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

    public function rules() {
        return [
            'resume' => 'required|file|mimes:doc,docs,pdf'
        ];
    }

    public function remove_resume(bool $isNotify = true) {

        if($isNotify) {
            return $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to remove your attached resume?',
                'message' => 'It\'s highly recommended for applicants to have a resume uploaded.',
                'action' => 'remove_resume'
            ]);
        }

        try {

            $record = ApplicantUsers::where('id', $this->user_id)
                ->first();

            $path = 'public/applicant/users/'.$record->id;
            $filepath = $path . '/' . $record->resume;
            
            if(Storage::exists($filepath)) {
                Storage::delete($filepath);
            } 
            
            $record->resume = null;

            $record->save();

            $this->loadRecords();

            $this->dispatch('loadRecords')->to('home.profile');
        
            return $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Resume Removed', 
                'message' => 'We are encouraging you to have a resume uploaded!',
            ]);

        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Error occured: ' . $e->getMessage()
            ]);
        }
    }

    public function save_resume(bool $isNotify = true) {
        
        $this->validate();

        if($isNotify) {
            return $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to save this resume?',
                'message' => 'Your resume offers valuable insights into your skills, helping us understand you better.',
                'action' => 'save_resume'
            ]);
        }

        try {

            $record = ApplicantUsers::where('id', $this->user_id)
                ->first();

            $path = 'public/applicant/users/'.$record->id;
            $filepath = $path . '/' . $record->resume;
            
            if(Storage::exists($filepath)) {
                Storage::delete($filepath);
            } 

            $file = $this->resume;
            $extension = $file->getClientOriginalExtension(); 
            $filename = 'applicant_resume_' . time() . '.' . $extension;

            $record->resume = $filename;
            $record->save();

            $file->storeAs($path, $filename);

            $this->dispatch('loadRecords')->to('home.profile');

            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Resume Updated', 
                'message' => 'You have now updated your resume, this might help you get hired!',
            ]);

        } catch (\Exception $e) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Error occured: ' . $e->getMessage()
            ]);
        }


    }

    public function render()
    {
        return view('livewire.home.modals.update-profile-resume');
    }
}
