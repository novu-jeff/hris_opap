<?php

namespace App\Livewire\Admin\Reports\DailyTimeRecord;

use App\Models\EmployeeAccount;
use App\Models\EmployeeClockInOut;
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
        $model = EmployeeClockInOut::with('information.personal');
    
        if ($this->search) {
            $this->resetPage();
    
            $model->where(function ($query) {
                $query->orWhere(function ($subQuery) {
                    // Check if the search term matches a month or year
                    $subQuery->whereRaw('MONTHNAME(created_at) like ?', ['%' . $this->search . '%'])
                             ->orWhereRaw('YEAR(created_at) like ?', ['%' . $this->search . '%']);
                });
            });
        }

        $paginatedRecords = $model->latest()->paginate($this->entries);
        
        $records = $paginatedRecords->getCollection()->groupBy(function ($record) {
                return Carbon::parse($record->created_at)->format('F, Y');
            })->map(function ($group, $monthYear) {
                $splitDate = explode(', ', $monthYear);
                return [
                    'month' => $splitDate[0],
                    'year' => $splitDate[1],
                    'records' => $group,
                ];
            })->values();
    
        return view('livewire.admin.reports.daily-time-record.index', [
            'records' => $records,
        ]);
    }
    
}
