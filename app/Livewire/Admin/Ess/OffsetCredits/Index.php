<?php

namespace App\Livewire\Admin\Ess\OffsetCredits;

use App\Models\EmployeeOffsetCredit;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'remove'
    ];

    public $selected_id;

    public $entries = 10;

    public $search = '';

    public function remove(bool $isNotify = true, ?int $id = null)
    {
        if ($isNotify) {

            $this->selected_id = $id;

            $this->dispatch('showConfirmation', [

                'title' => 'Delete Offset Credit?',

                'message' => 'This offset credit will be permanently removed.',

                'action' => 'remove'

            ]);

            return;
        }

        $credit = EmployeeOffsetCredit::find($this->selected_id);

        if (!$credit) {

            return $this->dispatch('alert', [

                'status' => 'error',

                'title' => 'Oops',

                'message' => 'Offset credit not found.'

            ]);

        }

        $credit->delete();

        $this->dispatch('alert', [

            'status' => 'success',

            'title' => 'Success',

            'message' => 'Offset credit deleted successfully.'

        ]);
    }

    public function render()
    {
        $query = EmployeeOffsetCredit::with([
            'employee.personal',
            'employee.section'
        ]);

        if ($this->search) {

            $query->whereHas('employee.personal', function ($q) {

                $q->whereRaw(
                    "CONCAT(firstname,' ',lastname) LIKE ?",
                    ["%{$this->search}%"]
                );

            });

        }

        return view(
            'livewire.admin.ess.offset-credits.index',
            [

                'records' => $query
                    ->latest()
                    ->paginate($this->entries)

            ]
        );
    }
}