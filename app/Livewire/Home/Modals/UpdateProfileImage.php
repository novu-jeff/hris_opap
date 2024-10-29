<?php

namespace App\Livewire\Home\Modals;

use App\Livewire\Home\Profile;
use App\Models\ApplicantUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateProfileImage extends Component
{

    use WithFileUploads;

    public $user_id;
    public $profile;
    public $profile_preview;
    protected $listeners = ['remove_profile', 'save_profile'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $id = Auth::guard('applicant')->user()->id;
        $record = ApplicantUsers::where('id', $id)->first();
        $folder = strtolower($record->firstname . '_' . $record->lastname . '_' . $record->id);
        
        $this->user_id = $id;
        $this->profile = $record->image;
        $this->profile_preview = $record->image ? 
            Storage::url('public/users/applicant/' . $folder .'/' . $record->image) : null;
    }

    public function updated($propertyName) {

        if ($propertyName === 'profile') {
            
            if (isset($this->profile)) {
                
                $file = $this->profile;

                if ($file instanceof \Illuminate\Http\UploadedFile) {

                    $extension = strtolower($file->getClientOriginalExtension());

                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $filename = $file->store('public/temp'); 
                        $url = Storage::url($filename); 

                        return $this->profile_preview = $url;
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
            'profile' => 'required|image|mimes:jpg,jpeg,png,gif'
        ];
    }

    public function remove_profile(bool $isNotify = true) {

        if($isNotify) {
            return $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to remove your profile?',
                'message' => 'It\'s highly recommended for applicants to have a profile image.',
                'action' => 'remove_profile'
            ]);
        }

        try {

            $record = ApplicantUsers::where('id', $this->user_id)
                ->first();

            $folder = strtolower($record->firstname . '_' . $record->lastname . '_' . $record->id);
            $path = 'users/applicant/' . $folder;
            $filepath = $path . '/' . $record->image;

            if(Storage::disk('public')->exists($filepath)) {
                Storage::disk('public')->delete($filepath);
            } 

            $record->image = null;

            $record->save();

            $this->loadRecords();

            $this->dispatch('loadRecords')->to('home.profile');
            
            return $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Profile Image Removed', 
                'message' => 'We are encouraging you to have a profile image!',
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

    public function save_profile(bool $isNotify = true) {
        
        $this->validate();

        if($isNotify) {
            return $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to save profile?',
                'message' => 'Saving a profile will help us to recognize you more.',
                'action' => 'save_profile'
            ]);
        }

        try {

            $record = ApplicantUsers::where('id', $this->user_id)
                ->first();

            $folder = strtolower($record->firstname . '_' . $record->lastname . '_' . $record->id);
            $path = 'users/applicant/' . $folder;
            $filepath = $path . '/' . $record->profile;
            

            
            if(Storage::disk('public')->exists($filepath)) {
                Storage::disk('public')->delete($filepath);
            } 

            $file = $this->profile;
            $extension = $file->getClientOriginalExtension(); 
            $filename = 'applicant_profile_' . time() . '.' . $extension;

            $record->image = $filename;

            $record->save();

            $file->storeAs($path, $filename, 'public');

            $this->loadRecords();

            $this->dispatch('loadRecords')->to('home.profile');

            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Profile Image Updated',
                'message' => 'Your profile image has been updated!',
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
        return view('livewire.home.modals.update-profile-image');
    }
}
