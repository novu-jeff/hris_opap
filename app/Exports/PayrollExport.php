<?php

namespace App\Exports;

use App\Models\SalaryPayroll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PayrollExport implements FromCollection, WithHeadings, WithEvents
{
    protected $payroll;
    protected $filterSalaryMethod;

    public function __construct(SalaryPayroll $payroll, $filterSalaryMethod = '')
    {
        $this->payroll = $payroll;
        $this->filterSalaryMethod = $filterSalaryMethod;
    }

    public function collection()
    {
        $items = $this->payroll->items;

        if ($this->filterSalaryMethod) {
            // Filter by salary method from employee_information
            $items = $items->filter(fn($item) =>
                $item->information &&
                strcasecmp($item->information->salary_method, $this->filterSalaryMethod) === 0
            );
        }

        return $items->map(fn($item) => [
            'Employee No' => $item->employee_no,
            'Name' => $item->name,
            'Position' => $item->position,
            'Salary Method' => $item->information?->salary_method ?? 'N/A', // NEW
            'Basic Salary' => $item->basic_salary,
            'Pera' => $item->pera ?? 0,
            'Gross Amount' => $item->gross_amount_earned,
            'Rlip' => $item->rlip ?? 0,
            'HDMF' => $item->hdmf ?? 0,
            'Philhealth' => $item->philhealth ?? 0,
            'Consoloan' => $item->consoloan ?? 0,
            'Emergency Loan' => $item->emergency_loan ?? 0,
            'Plreg' => $item->plreg ?? 0,
            'MPL' => $item->mpl ?? 0,
            'MPL lite' => $item->mpl_lite ?? 0,
            'CPL' => $item->cpl ?? 0,
            'MP2' => $item->mp2 ?? 0,
            'MPLSTLMS' => $item->mplstlms ?? 0,
            'cir375_cir449' => $item->cir375_cir449 ?? 0,
            'Tax' => $item->w_tax ?? 0,
            'UCA' => $item->uca ?? 0,
            'AUT' => $item->aut ?? 0,
            'Total Deductions' => $item->total_deductions ?? 0,
            'Net Amount' => $item->net_amount ?? 0,
            'DBP' => $item->dbp ?? 0,
            'KAWANI' => $item->kawani ?? 0,
            'LBP Payroll Account' => $item->lbp_payroll_account ?? 0,
            'Net First Half' => $item->net_first_half ?? 0,
            'Net Second Half' => $item->net_second_half ?? 0,
        ]);
    }

    public function headings(): array
    {
        return [
            'Employee No', 'Name', 'Position', 'Salary Method', 'Basic Salary', 'Pera', 'Gross Amount', 'Rlip', 'HDMF', 'Philhealth', 'Consoloan',
            'Emergency Loan', 'Plreg', 'MPL', 'MPL lite', 'CPL', 'MP2', 'MPLSTLMS', 'cir375_cir449', 'Tax', 'UCA', 'AUT',
            'Total Deductions', 'Net Amount', 'DBP', 'KAWANI', 'LBP Payroll Account', 'Net First Half', 'Net Second Half'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $items = $this->payroll->items;

                if ($this->filterSalaryMethod) {
                    $items = $items->filter(fn($item) =>
                        $item->information &&
                        strcasecmp($item->information->salary_method, $this->filterSalaryMethod) === 0
                    );
                }

                $employmentTypes = $items->map(fn($item) => $item->information?->employment_type?->name)->unique()->implode(', ');

                $totals = [
                    'basic_salary' => $items->sum('basic_salary'),
                    'gross' => $items->sum('gross_amount_earned'),
                    'total_deductions' => $items->sum('total_deductions'),
                    'net_amount' => $items->sum('net_amount'),
                    'lbp_payroll_account' => $items->sum('lbp_payroll_account'),
                ];

                // --- Insert summary rows ---
                $sheet->insertNewRowBefore(1, 6);

                $sheet->setCellValue('A1', 'No. of Employees: ' . $items->pluck('employee_no')->unique()->count());
                $sheet->setCellValue('A2', 'Payroll Period: ' . \Carbon\Carbon::parse($this->payroll->payroll_date)->format('M d, Y'));
                $sheet->setCellValue('A3', 'Cut-off Period: ' . $this->payroll->cut_off_period);
                $sheet->setCellValue('A4', 'Employee Type: ' . $employmentTypes);
                $sheet->setCellValue('A5', 'Basic Amount: ' . $totals['basic_salary']);
                $sheet->setCellValue('B5', 'Total Deductions: ' . $totals['total_deductions']);
                $sheet->setCellValue('A6', 'Gross Amount: ' . $totals['gross']);
                $sheet->setCellValue('B6', 'Net Amount: ' . $totals['net_amount']);
                $sheet->setCellValue('C6', 'LBP Payroll Account: ' . $totals['lbp_payroll_account']);

                // --- Style summary rows ---
                $sheet->getStyle('A1:C6')->getFont()->setBold(true);
                $sheet->getStyle('A1:C6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A5:C6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF99'); // light yellow

                // --- Style table header (row 7 after inserting summary) ---
                $sheet->getStyle('A7:AC7')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $sheet->getStyle('A7:AC7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0073e6'); // blue
                $sheet->getStyle('A7:AC7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // --- Auto width ---
                // Auto-size columns A to AC correctly
            $highestColumn = 'AD'; // last column in your sheet
            $columnIndex = 0;
            do {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
                $columnIndex++;
            } while ($colLetter !== $highestColumn);

                // --- Borders ---
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A7:AC{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }
        ];
    }
}
