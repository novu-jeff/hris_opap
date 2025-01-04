<?php

namespace App\Livewire\Admin\Ess\RequestStatus;

use App\Models\EmployeeInformation;
use App\Models\Message;
use Livewire\Component;
use Livewire\WithPagination;

class Chatlist extends Component
{
    use WithPagination;

    public $selected_id;
    public $unseen;
    public $entries = 10;
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->select();
    }

    public function loadRecords()
    {
        return EmployeeInformation::with('messages')
            ->get()
            ->map(function ($employee) {
                $employee->unseen_count = $employee->messages->where('isSeen', 0)->count();
                return $employee;
            });
    }

    public function select(string $employee_no = null)
    {

        $employee_no = $employee_no ?? session('selected_employee_no') ?? $this->loadRecords()->first()->employee_no ?? '';

        if (!is_null($employee_no)) {
            $this->selected_id = $employee_no;
            $this->makeSeen();

            session(['selected_employee_no' => $employee_no]);
            $this->dispatch('selected', $employee_no)->to(Chatbox::class);
        } else {
        }
    }

    public function makeSeen()
    {
        if ($this->selected_id) {
            Message::where('from_id', $this->selected_id)
                ->where('to_id', 0)
                ->update(['isSeen' => true]);
        }
    }

    public function render()
    {
        $employees = EmployeeInformation::with(['personal', 'account', 'positions', 'messages'])
            ->paginate($this->entries);

        return view('livewire.admin.ess.request-status.chatlist', compact('employees'));
    }
}
