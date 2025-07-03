<?php

namespace App\Livewire\Admin\Reports\Pagibig;

use App\Models\EmployeeInformation;
use App\Models\Sections;
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

        $query = EmployeeInformation::with('account', 'personal', 'section');

        if (!empty($this->search)) {
            $this->resetPage();
            $query->where(function ($q) {
                $q->where('employee_no', 'like', '%' . $this->search . '%')
                  ->orWhereHas('personal', function ($subQuery) {
                      $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                  });
            });
        }

        if (!empty($this->selectedSection)) {
            $query->whereHas('section', function ($q) {
                $q->where('id', $this->selectedSection);
            });
        }

        $paginated = $query->paginate($this->entries);

        // Reset global totals
        $this->total_employee_share = 0;
        $this->total_employer_share = 0;
        $this->total_contribution = 0;

        // Compute individual contributions
        $records = tap($paginated)->each(function ($record) use ($contributionsService) {
            $record->employee_share = 0;
            $record->employer_share = 0;
            $record->total = 0;

            if ($record->monthly_rate) {
                $msc = $contributionsService->computeSalary($record->monthly_rate);
                $contribution = $contributionsService->computePagibig($msc);

                $record->employee_share = $contribution['employee_share'];
                $record->employer_share = $contribution['employer_share'];
                $record->total = $contribution['total'];

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

        return view('livewire.admin.reports.pagibig.index', [
            'groupedRecords' => $groupedRecords,
            'paginator' => $records,
            'sections' => $this->sections,
        ]);
    }
}
