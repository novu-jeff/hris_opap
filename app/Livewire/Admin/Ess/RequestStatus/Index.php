<?php

namespace App\Livewire\Admin\Ess\RequestStatus;

use App\Models\EmployeeInformation;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $selected_id;
    public $unseen;
    public $entries = 10;
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $model = EmployeeInformation::query()
            ->leftJoin('messages', function ($join) {
                $join->on('employee_information.employee_no', '=', 'messages.from_id')
                    ->orOn('employee_information.employee_no', '=', 'messages.to_id');
            })
            ->select(
                'employee_information.*',
                DB::raw('MAX(messages.created_at) as latest_message_date')
            )
            ->groupBy('employee_information.id') // Grouping by primary key of EmployeeInformation
            ->orderBy('latest_message_date', 'DESC'); // Sorting by the latest message date
    
        
        if ($this->search) {
            $this->resetPage();
        
            $model = $model->where(function ($query) {
                $query->where('employee_no', 'like', '%' . $this->search . '%')
                    ->orWhereHas('personal', function ($subQuery) {
                        $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                    });
            });
        }
        
        $records = $model->paginate($this->entries);
        
        return view('livewire.admin.ess.request-status.index', [
            'records' => $records
        ]);

    }
}
