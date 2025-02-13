<?php

namespace App\Livewire\Admin\Reports\DailyTimeRecord;

use App\Models\EmployeeAccount;
use App\Models\EmployeeClockInOut;
use App\Models\EmployeeTimelogs;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';


    public function render()
    {
        // Initialize the query builder for EmployeeClockInOut with related information
        $model = EmployeeTimelogs::with('employee.personal');

        // If search is provided, apply the search condition
        if ($this->search) {
            $model->where(function ($query) {
                $query->whereRaw('MONTHNAME(created_at) like ?', ['%' . $this->search . '%'])
                    ->orWhereRaw('YEAR(created_at) like ?', ['%' . $this->search . '%']);
            });
        }

        // Fetch the records (no pagination)
        $records = $model->latest()->get();

        // Group the records by month and year
        $groupedRecords = $records->groupBy(function ($record) {
            return Carbon::parse($record->created_at)->format('F, Y');
        })->map(function ($group, $monthYear) {
            // Extract month and year for each group
            [$month, $year] = explode(', ', $monthYear);
            return [
                'month' => $month,
                'year' => $year,
                'records' => $group,
            ];
        })->values(); // Re-index the collection after grouping

        // Return the grouped records to the view
        return view('livewire.admin.reports.daily-time-record.index', [
            'records' => $groupedRecords,  // Pass only the grouped records
        ]);
    }


    
}
