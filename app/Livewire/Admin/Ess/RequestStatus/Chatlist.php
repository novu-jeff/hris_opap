<?php

namespace App\Livewire\Admin\Ess\RequestStatus;

use App\Models\EmployeeInformation;
use App\Models\Message;
use Carbon\Carbon;
use Livewire\Component;

class Chatlist extends Component
{

    public $selected_id;
    public $unseen;
    public $employees;

    public function mount() {
        $this->loadRecords();
        $this->select();
    }

    public function loadRecords() {
        $this->employees = EmployeeInformation::with('personal', 'account', 'positions', 'messages')->get()->map(function ($employee) {
            $employee->unseen_count = $employee->messages->where('isSeen', 0)->count();
            return $employee;
        });
    }

    public function select(int $id = null) {

        if(is_null($id)) {
            $this->dispatch('selected', $id)->to(Chatbox::class);
            $id = $this->employees->first()->id ?? null;
        } 

        $this->selected_id = $id;
        $this->makeSeen();
        $this->dispatch('selected', $id)->to(Chatbox::class);

    }

    public function makeSeen() {
        return Message::where('from_id', $this->selected_id)
            ->where('to_id', 0)
            ->update([
                'isSeen' => true,
                'seen_timestamp' => Carbon::now()
        ]);
    }

    public function render()
    {
        return view('livewire.admin.ess.request-status.chatlist');
    }
}
