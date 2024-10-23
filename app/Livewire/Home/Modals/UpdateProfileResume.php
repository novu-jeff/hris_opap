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
    protected $listeners = ['loadRecords'];

    public function mount() {
        $id = Auth::guard('applicants')->user()->id;
        $this->user_id = $id;
    }

    public function rules() {
        return [
            'resume' => 'required|file|mimes:doc,docs,pdf'
        ];
    }

    public function save() {
        
        $this->validate();


        try {

            $record = ApplicantUsers::where('id', $this->user_id)
                ->first();

            if(!$record) {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!',
                    'message' => 'Unable to update resume, no user found'
                ]);
            }


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

            $this->dispatch('loadRecords')->to(Profile::class);

            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Resume Updated', 
                'message' => 'You have now updated your resume, this might help you get hired!',
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

    public function render()
    {
        return view('livewire.home.modals.update-profile-resume');
    }
}
