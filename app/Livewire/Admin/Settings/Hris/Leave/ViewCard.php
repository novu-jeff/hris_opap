<?php

namespace App\Livewire\Admin\Settings\Hris\Leave;

use App\Models\EmployeeInformation;
use App\Models\EmployeeLeaveCard;
use DateTime;
use Livewire\Component;

class ViewCard extends Component
{

    public $id;
    public $employee_no;
    public $action;
    public $records;

    public function mount() {

        $this->loadRecords();

    }

    public function loadRecords() {

        if(empty($this->id) || empty($this->employee_no) || empty($this->action)) {
            return redirect()->route('leave.show', ['leave' => $this->id]);
        }

        $employee = EmployeeInformation::where('employee_no', $this->employee_no)->first();

        if(!$employee) {
            
            return redirect()->route('leave.show', ['leave' => $this->id]);

        }

        $records = EmployeeLeaveCard::where('employee_no', $this->employee_no)
            ->get();

        $sortedRecords = collect($records)
            ->groupBy('year') 
            ->map(function ($items) {
                return $items->sortBy(function ($item) {
                    return DateTime::createFromFormat('F', $item['period'])->format('m');
                })->values(); 
            })
            ->sortKeys();
    
        $this->records = $sortedRecords;

    }

    public function render()
    {
        return view('livewire.admin.settings.hris.leave.view-card');
    }
}
