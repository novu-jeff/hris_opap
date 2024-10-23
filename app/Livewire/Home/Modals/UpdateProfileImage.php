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
    public $record;

    public function mount() {
        $id = Auth::guard('applicants')->user()->id;
        $record = ApplicantUsers::where('id', $id)->first();
        $this->user_id = $id;
        $this->record = $record->image;
    }

    public function rules() {
        return [
            'profile' => 'required|image|mimes:jpg,jpeg,png'
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
                    'message' => 'Unable to update profile, no user found'
                ]);
            }


            $path = 'public/applicant/users/'.$record->id;
            $filepath = $path . '/' . $record->profile;
            
            if(Storage::exists($filepath)) {
                Storage::delete($filepath);
            } 

            $file = $this->profile;
            $extension = $file->getClientOriginalExtension(); 
            $filename = 'applicant_profile_' . time() . '.' . $extension;

            $record->image = $filename;
            $record->save();

            $file->storeAs($path, $filename);

            $this->dispatch('loadRecords')->to('home.profile');

            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Profile Updated',
                'message' => 'Your profile has been successfully updated!',
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

    public function remove_profile() {

        $record = ApplicantUsers::where('id', $this->user_id)
                ->first();

        if(!$record) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Unable to remove profile, no user found'
            ]);
        }


        $path = 'public/applicant/users/'.$record->id;
        $filepath = $path . '/' . $record->profile;
        
        if(Storage::exists($filepath)) {
            Storage::delete($filepath);
        } 

        $record->image = null;
        $record->save();

        $this->dispatch('loadRecords')->to('home.profile');
        
        return $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'success',
            'title' => 'Profile Removed', 
            'message' => 'You have now updated your resume, this might help you get hired!',
        ]);
    }

    public function render()
    {
        return view('livewire.home.modals.update-profile-image');
    }
}
