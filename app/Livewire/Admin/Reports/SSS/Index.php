<?php

namespace App\Livewire\Admin\Reports\SSS;

use App\Models\EmployeeInformation;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $entries = 9999999;
    public $search = '';
    public $year;

    public $total_employee_share = 0;
    public $total_employer_share = 0;
    public $total_contribution = 0;
    public $total_ec = 0;
    public $employee_count = 0;

    public function mount()
    {
        $this->year = now()->year;
    }

    public function render()
    {
        $contributionsService = app(\App\Services\ContributionsService::class);

        $query = EmployeeInformation::with('account', 'personal');

        if (!empty($this->search)) {
            $this->resetPage();
            $query->where(function ($q) {
                $q->where('employee_no', 'like', '%' . $this->search . '%')
                  ->orWhereHas('personal', function ($subQuery) {
                      $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                  });
            });
        }

        $paginated = $query->paginate($this->entries);

        // Reset totals before accumulation
        $this->total_employee_share = 0;
        $this->total_employer_share = 0;
        $this->total_contribution = 0;
        $this->total_ec = 0;

        $records = tap($paginated)->each(function ($record) use ($contributionsService) {
            $record->employee_share = 0;
            $record->employer_share = 0;
            $record->total = 0;
            $record->msc = 0;
            $record->ec = 0;
            $record->status = 'Employed';

             # Check if employee resigned on or before current month
            if ($record->date_resignation || $record->monthly_rate == 0) {
                $resigned = \Carbon\Carbon::parse($record->date_resignation);
                $current = now();

                # If resigned before or during the current month and year
                if ($resigned->year < $current->year || ($resigned->year == $current->year && $resigned->month <= $current->month) || $record->monthly_rate == 0) {
                    $record->monthly_rate = 0;
                    $record->status = 'No earnings';
                }
            }

            if ($record->monthly_rate) {
                $msc = $contributionsService->computeSalary($record->monthly_rate);
                $contribution = $contributionsService->computeSSS($msc);

                $record->employee_share = $contribution['employee_share'];
                $record->employer_share = $contribution['employer_share'];
                $record->ec = $contribution['ec'];
                $record->total = $contribution['total'];
                $record->msc = $contribution['msc'];

                $this->total_employee_share += $record->employee_share;
                $this->total_employer_share += $record->employer_share;
                $this->total_ec += $record->ec;
                $this->total_contribution += $record->total;
            }
        });

        // dd($records);
        $this->employee_count = $records->count();

        return view('livewire.admin.reports.s-s-s.index', [
            'records' => $records
        ]);
    }
}
