<?php

namespace App\Exports;

use App\Models\SalaryPayroll;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class PayrollExport implements FromCollection, WithEvents
{
    protected $payroll;
    protected $filterSalaryMethod;
    protected $searchName;
    protected $supervisingOfficer;
    protected $preparedByName;
    protected $preparedByPosition;
    protected $certifiedByName;
    protected $certifiedByPosition;

    public function __construct(SalaryPayroll $payroll, $filterSalaryMethod = '', $searchName = '', $supervisingOfficer = null)
    {
        $this->payroll = $payroll;
        $this->filterSalaryMethod = $filterSalaryMethod;
        $this->searchName = $searchName;
        $this->supervisingOfficer = $supervisingOfficer;
        $this->preparedByName = $supervisingOfficer['full_name'] ?: 'Prepared By';
        $this->preparedByPosition = $supervisingOfficer['position_name'] ?: '';
    }

    /**
     * BUILD EXCEL DATA
     */
    public function collection()
{
    $items = $this->payroll
        ->items()
        ->with('information.section')
        ->get();

    // Group by section
    $grouped = $items
        ->sortBy(fn($item) => optional($item->information->section)->name)
        ->groupBy(function ($item) {
            $section = optional($item->information->section);
            return $section->name
                ? "{$section->code} - {$section->name}"
                : 'NO SECTION';
        });

    $rows = collect();

    // ✅ PUSH HEADER ONCE
    $headers = [
        'NAME','POSITION','BASIC SALARY','PERA','GROSS AMOUNT EARNED',
        'RLIP','HDMF','PHILHEALTH','CONSOLOAN','EMERGYLN','PLREG',
        'MPL','MPL LITE','CPL','MP2','MPL STLMS','CIR375, CIR449',
        'W/TAX','UCA','AUT','TOTAL DED','NET AMOUNT','DBP BRANCH',
        'KAWANI','LBP PAYROLL ACCOUNT','1st Half','2nd Half',
    ];

    $rows->push($headers);

    foreach ($grouped as $section => $employees) {

        // SECTION LABEL ROW
        $rows->push(["SECTION: {$section}"]);

        foreach ($employees as $item) {
            $rows->push([
                $item->name,
                $item->position,
                $item->basic_salary,
                $item->pera ?? 0,
                $item->gross_amount_earned,
                $item->rlip ?? 0,
                $item->hdmf ?? 0,
                $item->philhealth ?? 0,
                $item->consoloan ?? 0,
                $item->emergency_loan ?? 0,
                $item->plreg ?? 0,
                $item->mpl ?? 0,
                $item->mpl_lite ?? 0,
                $item->cpl ?? 0,
                $item->mp2 ?? 0,
                $item->mplstlms ?? 0,
                $item->cir375_cir449 ?? 0,
                $item->w_tax ?? 0,
                $item->uca ?? 0,
                $item->aut ?? 0,
                $item->total_deductions ?? 0,
                $item->net_amount ?? 0,
                $item->dbp ?? '',
                $item->kawani ?? '',
                $item->lbp_payroll_account ?? '',
                $item->net_first_half ?? 0,
                $item->net_second_half ?? 0,
            ]);
        }

        // ✅ SECTION TOTAL
        $rows->push([
            'SECTION TOTAL','',
            $employees->sum('basic_salary'),
            $employees->sum('pera'),
            $employees->sum('gross_amount_earned'),
            $employees->sum('rlip'),
            $employees->sum('hdmf'),
            $employees->sum('philhealth'),
            $employees->sum('consoloan'),
            $employees->sum('emergency_loan'),
            $employees->sum('plreg'),
            $employees->sum('mpl'),
            $employees->sum('mpl_lite'),
            $employees->sum('cpl'),
            $employees->sum('mp2'),
            $employees->sum('mplstlms'),
            $employees->sum('cir375_cir449'),
            $employees->sum('w_tax'),
            $employees->sum('uca'),
            $employees->sum('aut'),
            $employees->sum('total_deductions'),
            $employees->sum('net_amount'),
            $employees->sum('dbp'),
            $employees->sum('kawani'),
            $employees->sum('lbp_payroll_account'),
            $employees->sum('net_first_half'),
            $employees->sum('net_second_half'),
        ]);

        $rows->push([]); // spacer
    }

    // ✅ GRAND TOTAL
    $rows->push([
        'GRAND TOTAL','',
        $items->sum('basic_salary'),
        $items->sum('pera'),
        $items->sum('gross_amount_earned'),
        $items->sum('rlip'),
        $items->sum('hdmf'),
        $items->sum('philhealth'),
        $items->sum('consoloan'),
        $items->sum('emergency_loan'),
        $items->sum('plreg'),
        $items->sum('mpl'),
        $items->sum('mpl_lite'),
        $items->sum('cpl'),
        $items->sum('mp2'),
        $items->sum('mplstlms'),
        $items->sum('cir375_cir449'),
        $items->sum('w_tax'),
        $items->sum('uca'),
        $items->sum('aut'),
        $items->sum('total_deductions'),
        $items->sum('net_amount'),
        $items->sum('dbp'),
        $items->sum('kawani'),
        $items->sum('lbp_payroll_account'),
        $items->sum('net_first_half'),
        $items->sum('net_second_half'),
    ]);

    return new Collection($rows);
}


    /**
     * STYLE EXCEL
     */
  public function registerEvents(): array
{
    return [

        AfterSheet::class => function (AfterSheet $event) {

            $sheet = $event->sheet->getDelegate();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            /* ================= FORMAT CUTOFF ================= */

            $cutoff = $this->payroll->cut_off_period;
            $formattedCutoff = $cutoff;

            if ($cutoff && str_contains($cutoff, ' to ')) {
                [$start, $end] = explode(' to ', $cutoff);

                $formattedCutoff =
                    Carbon::parse($start)->format('M d, Y') .
                    ' to ' .
                    Carbon::parse($end)->format('M d, Y');
            }

            /* ================= BIG REPORT HEADER ================= */

            $sheet->insertNewRowBefore(1, 5);

            $sheet->setCellValue('A1', 'PAYROLL REPORT');
            $sheet->setCellValue('A2', 'For the Period: ' . $formattedCutoff);
            $sheet->setCellValue('A3', 'Payroll Date: ' .
                Carbon::parse($this->payroll->payroll_date)->format('M d, Y'));
            $sheet->setCellValue('A4', 'Generated on: ' . now()->format('M d, Y h:i A'));

            $sheet->mergeCells("A1:{$highestColumn}1");
            $sheet->mergeCells("A2:{$highestColumn}2");
            $sheet->mergeCells("A3:{$highestColumn}3");
            $sheet->mergeCells("A4:{$highestColumn}4");

            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(24);
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A3:A4')->getFont()->setSize(12);

            $sheet->getStyle("A1:{$highestColumn}4")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $sheet->getStyle("A1:{$highestColumn}4")
                ->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E7F1FF');

            /* ================= TABLE HEADER STYLE ================= */

            $headerRow = 6; // after inserting 5 rows

            $sheet->getStyle("A{$headerRow}:{$highestColumn}{$headerRow}")
                ->getFont()->setBold(true)->setSize(15);

            $sheet->getStyle("A{$headerRow}:{$highestColumn}{$headerRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle("A{$headerRow}:{$highestColumn}{$headerRow}")
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('BDD7EE');

            $sheet->getRowDimension($headerRow)->setRowHeight(28);

            /* ================= FREEZE HEADER ================= */

           $sheet->freezePane('C7');

            /* ================= BIGGER DATA FONT ================= */

            // All rows below header = employee rows
            $sheet->getStyle("A" . ($headerRow + 1) . ":{$highestColumn}{$highestRow}")
                ->getFont()->setSize(12);

            /* ================= SIGNATORIES ================= */

           $preparedByName = $this->preparedByName;
            $preparedByPosition = $this->preparedByPosition;

            $certifiedByName = 'MARIA SANTOS';
            $certifiedByPosition = 'Finance Manager';

            $footerRow = $sheet->getHighestRow() + 3;

            $sheet->setCellValue("A{$footerRow}", 'Prepared by:');
            $sheet->setCellValue("F{$footerRow}", 'Certified Correct by:');

            $sheet->setCellValue("A" . ($footerRow + 2), $preparedByName);
            $sheet->setCellValue("F" . ($footerRow + 2), $certifiedByName);

            $sheet->setCellValue("A" . ($footerRow + 3), $preparedByPosition);
            $sheet->setCellValue("F" . ($footerRow + 3), $certifiedByPosition);

            $sheet->getStyle("A{$footerRow}")->getFont()->setBold(true);
            $sheet->getStyle("F{$footerRow}")->getFont()->setBold(true);
            $sheet->getStyle("A" . ($footerRow + 2))->getFont()->setBold(true);
            $sheet->getStyle("F" . ($footerRow + 2))->getFont()->setBold(true);

            /* ================= AUTO WIDTH ================= */

            foreach (range(1, Coordinate::columnIndexFromString($highestColumn)) as $col) {
                $letter = Coordinate::stringFromColumnIndex($col);
                $sheet->getColumnDimension($letter)->setAutoSize(true);
            }

            /* ================= STYLE LOOP ================= */

            for ($row = 1; $row <= $highestRow + 5; $row++) {

                $value = $sheet->getCell("B{$row}")->getValue()
                    ?? $sheet->getCell("A{$row}")->getValue();

                if (!$value) continue;

                /* SECTION ROW */
                if (str_contains($value, 'SECTION:')) {

                    $sheet->mergeCells("A{$row}:{$highestColumn}{$row}");

                    $sheet->getStyle("A{$row}")
                        ->getFont()->setBold(true)->setSize(13);

                    $sheet->getStyle("A{$row}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    $sheet->getStyle("A{$row}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('D9EAD3');
                }

               
                        /* GRAND TOTAL (SPECIAL COLOR) */
            if (str_contains($value, 'GRAND TOTAL')) {

                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getFont()->setBold(true)->setSize(13);

                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F4CCCC'); // light red

                // DOUBLE BORDER
                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getBorders()->getOutline()
                    ->setBorderStyle(Border::BORDER_DOUBLE);    
                    }

            /* SECTION TOTAL */
            elseif (str_contains($value, 'TOTAL')) {

                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getFont()->setBold(true);

                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFF2CC'); // yellow
            }
            }

            /* ================= BORDERS ================= */
            


            $sheet->getStyle("A5:{$highestColumn}{$highestRow}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            $highestRow = $sheet->getHighestRow();   
            
            for ($row = 1; $row <= $highestRow + 5; $row++) {

                $value = $sheet->getCell("B{$row}")->getValue()
                    ?? $sheet->getCell("A{$row}")->getValue();

                if (!$value) continue;

                /* SECTION ROW */
                if (str_contains($value, 'SECTION:')) {

                    $sheet->mergeCells("A{$row}:{$highestColumn}{$row}");

                    $sheet->getStyle("A{$row}")
                        ->getFont()->setBold(true)->setSize(13);

                    $sheet->getStyle("A{$row}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    $sheet->getStyle("A{$row}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('D9EAD3');
                }

               
                        /* GRAND TOTAL (SPECIAL COLOR) */
            if (str_contains($value, 'GRAND TOTAL')) {

                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getFont()->setBold(true)->setSize(13);

                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F4CCCC'); // light red

                // DOUBLE BORDER
                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getBorders()->getOutline()
                    ->setBorderStyle(Border::BORDER_DOUBLE);    
                    }

            /* SECTION TOTAL */
            elseif (str_contains($value, 'TOTAL')) {

                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getFont()->setBold(true);

                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFF2CC'); // yellow
            }
            }

            // Bold NET AMOUNT column only (column V)
            $sheet->getStyle("V{$headerRow}:V{$highestRow}")
                ->getFont()->setBold(true);   
                
            $moneyColumns = [
                'C','D','E','F','G','H','I','J','K','L','M',
                'N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB'
            ];

            foreach ($moneyColumns as $col) {
                $sheet->getStyle("{$col}" . ($headerRow + 1) . ":{$col}{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');
            }
    


            /* ================= MONEY RIGHT ALIGN ================= */

            $sheet->getStyle("E5:I{$highestRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

           /* ================= PRINT LAYOUT (LEGAL) ================= */

            $sheet->getPageSetup()
                ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LEGAL)
                ->setFitToWidth(1)
                ->setFitToHeight(0);

            // Repeat header row when printing
            $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, $headerRow);

            // Center horizontally
            $sheet->getPageSetup()->setHorizontalCentered(true);

            // Margins (tighter for legal)
            $sheet->getPageMargins()
                ->setTop(0.5)
                ->setBottom(0.5)
                ->setLeft(0.3)
                ->setRight(0.3);

                
        }
    ];
}


}
