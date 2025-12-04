<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnseenMessagesBadge extends Component
{
    public $unseenMessages = 0;

    protected $listeners = ['refreshUnseenMessages' => 'loadUnseenMessages'];

    public function mount()
    {
        $this->loadUnseenMessages();
    }

    public function loadUnseenMessages()
    {
        $employeeId = Auth::user()->employee_id;

        $this->unseenMessages = DB::table('messages')
            ->where('to_role', 'employee')
            ->where('to_id', $employeeId)
            ->where('isSeen', 0)
            ->count();
    }

    public function render()
    {
        return view('livewire.employee.unseen-messages-badge');
    }
}
