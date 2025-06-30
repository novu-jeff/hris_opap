<?php

namespace App\Livewire\Admin\Reports\Philhealth;

use App\Models\EmployeeInformation;
use App\Services\ContributionsService;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $selected_id;
    protected $listeners = ['remove']; 
    protected $paginationTheme = 'bootstrap';
    public $entries = 9999999;
    public $search = '';
    public $year;
    protected $contributionsService;

    public $total_employee_share = 0;
    public $total_employer_share = 0;
    public $total_contribution = 0;
    public $employee_count = 0;

    public function mount()
    {
        $this->year = now()->year;
    }

    public function render()
    {
        $contributionsService = app(\App\Services\ContributionsService::class);

        $model = EmployeeInformation::with('account', 'personal');

        if (!empty($this->search)) {
            $this->resetPage();

            $model->where(function ($q) {
                $q->where('employee_no', 'like', '%' . $this->search . '%')
                    ->orWhereHas('personal', function ($subQuery) {
                        $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                    });
            });
        }

        $records = tap($model->paginate($this->entries))->each(function ($record) use ($contributionsService) {
            $record->total = 0;
            $record->employee_share = 0;
            $record->employer_share = 0;

            if ($record->monthly_rate) {
                $record->monthly_rate = $contributionsService->computeSalary($record->monthly_rate);
                $contribution = $contributionsService->computePhilHealth($record->monthly_rate);

                $record->total = $contribution['total'];
                $record->employee_share = $contribution['employee_share'];
                $record->employer_share = $contribution['employer_share'];

                $this->total_employee_share += $record->employee_share;
                $this->total_employer_share += $record->employer_share;
                $this->total_contribution += $record->total;
            }
        });

        $this->employee_count = $records->count();

        return view('livewire.admin.reports.philhealth.index', [
            'records' => $records
        ]);
    }

}
