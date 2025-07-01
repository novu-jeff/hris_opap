<?php

namespace App\Livewire\Admin\Reports\Philhealth;

use App\Models\EmployeeInformation;
use App\Models\Sections;
use App\Services\ContributionsService;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $entries = 9999999;
    public $search = '';
    public $year;

    public $sections;
    public $selectedSection = '';

    protected $contributionsService;

    public $total_employee_share = 0;
    public $total_employer_share = 0;
    public $total_contribution = 0;
    public $employee_count = 0;

    public function mount()
    {
        $this->year = now()->year;
        $this->sections = Sections::orderBy('name')->get();
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

        if (!empty($this->selectedSection)) {
            $model->whereHas('section', function ($q) {
                $q->where('id', $this->selectedSection);
            });
        }

        $paginated = $model->paginate($this->entries);

        // Reset global totals
        $this->total_employee_share = 0;
        $this->total_employer_share = 0;
        $this->total_contribution = 0;

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

         // Group by section with subtotals
        $groupedRecords = collect();

        $records->getCollection()
            ->groupBy(fn($record) => $record->section->name ?? 'NO DEPARTMENT')
            ->each(function ($group, $sectionName) use (&$groupedRecords) {
                $groupedRecords->push([
                    'section' => $sectionName,
                    'records' => $group,
                    'subtotal_employee_share' => $group->sum('employee_share'),
                    'subtotal_employer_share' => $group->sum('employer_share'),
                    'subtotal_total' => $group->sum('total'),
                ]);
            });

        return view('livewire.admin.reports.philhealth.index', [
            'groupedRecords' => $groupedRecords,
            'paginator' => $records,
            'sections' => $this->sections,
        ]);
    }

}
