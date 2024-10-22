<?php

namespace App\Livewire\Home\Modals;

use App\Models\ApplicantSkills;
use App\Models\SkillList;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateProfileSkills extends Component
{

    public $user_id;
    public $skills;
    public $record;
    protected $listeners = ['populateField'];

    public function mount() {
        $id = Auth::guard('applicants')->user()->id;
        $record = ApplicantSkills::where('user_id', $id)->get();
        $this->user_id = $id;
        $this->record = SkillList::all();
        $this->skills = $record->pluck('skill_id')->toArray();
    }

    public function rules() {
        return [
            'skills.*' => 'nullable|exists:skills_list,id'
        ];
    }

    public function save() {
        
        $this->validate();

        $record = ApplicantSkills::where('user_id', $this->user_id);

        if(!$record) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Unable to update skills, no user found'
            ]);
        }

        $record->delete();

        if(!empty($this->skills)) {

            foreach($this->skills as $skill) {
                ApplicantSkills::create([
                    'user_id' => $this->user_id,
                    'skill_id' => $skill
                ]);
            }
        }

        return $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'success',
            'title' => 'Skills Updated',
            'message' => 'Your skills has been successfully updated!',
            'isReloadDT' => true
        ]);

    }

    public function populateField($data) {
        $this->skills = $data;
    }

    public function render()
    {
        return view('livewire.home.modals.update-profile-skills');
    }
}
