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
            ->groupBy('year') // Group by year
            ->map(function ($items, $year) use ($records) {
                // Get the last item for the current year
                $lastItem = $items->last();
        
                // Calculate the remaining balance using the last item in the current year
                $prevBal = [
                    'vl' => (float)($lastItem['vl_bal'] ?? 0),
                    'sl' => (float)($lastItem['sl_bal'] ?? 0),
                ];
        
                // Get the last record of the previous year
                $previousYearRecord = $records->where('year', $year - 1)->last();
        
                // If no previous year record is found, set previous_bal to 0
                if ($previousYearRecord) {
                    $prevBal['vl'] = (float)($previousYearRecord['vl_bal'] ?? 0);
                    $prevBal['sl'] = (float)($previousYearRecord['sl_bal'] ?? 0);
                } else {
                    // Set to 0 if no previous year exists
                    $prevBal['vl'] = 0;
                    $prevBal['sl'] = 0;
                }
        
                // Sort items by period (month order)
                $sortedItems = $items->sortBy(function ($item) {
                    return DateTime::createFromFormat('F', $item['period'])->format('m');
                })->values();
        
                return [
                    'previous_bal' => $prevBal, // Use previous year's last record for previous_bal
                    'items' => $sortedItems // Return the sorted items
                ];
            })
            ->sortKeys();
        
    
        $this->records = $sortedRecords;

    }

    public function render()
    {
        return view('livewire.admin.settings.hris.leave.view-card');
    }
}
