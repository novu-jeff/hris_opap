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

    public function __construct(SalaryPayroll $payroll, $filterSalaryMethod = '', $searchName = '', $supervisingOfficer = null, $chiefadministrativeOfficer = null)
    {
        $this->payroll = $payroll;
        $this->filterSalaryMethod = $filterSalaryMethod;
        $this->searchName = $searchName;
        $this->supervisingOfficer = $supervisingOfficer;
        $this->preparedByName = $supervisingOfficer['full_name'] ?: 'Prepared By';
        $this->preparedByPosition = $supervisingOfficer['position_name'] ?: '';
        $this->certifiedByName = $chiefadministrativeOfficer['full_name'] ?: 'Certified Correct By';
        $this->certifiedByPosition = $chiefadministrativeOfficer['position_name'] ?: '';
    }

    /**
     * BUILD EXCEL DATA
     */
    public function collection()
{
    $items = $this->payroll
        ->items()
        ->with('information.section.department')
        ->get();

    // Group by department first, then by section (nested output order)
    $groupedByDepartment = $items
        ->sortBy(function ($item) {
            $department = optional($item->information->section?->department);
            $code = $department->code ?? '';

            // Custom department ordering:
            // - `EO` first
            // - then `P1`, `P2`, `P3`, ... in numeric order
            if ($code === 'EO') return 0;

            if (preg_match('/^P(\d+)/', $code, $matches)) {
                return 1 + (int) $matches[1];
            }

            return 9999;
        })
        ->groupBy(function ($item) {
            $department = optional($item->information->section?->department);

            return $department->name
                ? ($department->code
                    ? "{$department->code} - {$department->name}"
                    : $department->name)
                : 'None';
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

    // Running employee count across the entire report
    $employeeCount = 1;

    foreach ($groupedByDepartment as $department => $departmentEmployees) {
        // DEPARTMENT LABEL ROW
        $rows->push(["DEPARTMENT {$department}"]);

        // Group within department by section
        $groupedBySection = $departmentEmployees
            ->sortBy(fn ($item) => optional($item->information->section)->name)
            ->groupBy(function ($item) {
                $section = optional($item->information->section);

                return $section->name
                    ? "{$section->code} - {$section->name}"
                    : 'NO SECTION';
            });

        foreach ($groupedBySection as $section => $employees) {
            // SECTION LABEL ROW
            $rows->push(["SECTION: {$section}"]);
            foreach ($employees as $item) {
                $rows->push([
                    "{$employeeCount}. {$item->name}",
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

                $employeeCount++;
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

        // ✅ DEPARTMENT SUB TOTAL
        $rows->push([
            "SUB-TOTAL for {$department}",'',
            $departmentEmployees->sum('basic_salary'),
            $departmentEmployees->sum('pera'),
            $departmentEmployees->sum('gross_amount_earned'),
            $departmentEmployees->sum('rlip'),
            $departmentEmployees->sum('hdmf'),
            $departmentEmployees->sum('philhealth'),
            $departmentEmployees->sum('consoloan'),
            $departmentEmployees->sum('emergency_loan'),
            $departmentEmployees->sum('plreg'),
            $departmentEmployees->sum('mpl'),
            $departmentEmployees->sum('mpl_lite'),
            $departmentEmployees->sum('cpl'),
            $departmentEmployees->sum('mp2'),
            $departmentEmployees->sum('mplstlms'),
            $departmentEmployees->sum('cir375_cir449'),
            $departmentEmployees->sum('w_tax'),
            $departmentEmployees->sum('uca'),
            $departmentEmployees->sum('aut'),
            $departmentEmployees->sum('total_deductions'),
            $departmentEmployees->sum('net_amount'),
            $departmentEmployees->sum('dbp'),
            $departmentEmployees->sum('kawani'),
            $departmentEmployees->sum('lbp_payroll_account'),
            $departmentEmployees->sum('net_first_half'),
            $departmentEmployees->sum('net_second_half'),
        ]);

        $rows->push([]); // spacer between departments
        $rows->push([]); // extra spacer (visual separation)
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

           $sheet->freezePane('H7');

            /* ================= BIGGER DATA FONT ================= */

            // All rows below header = employee rows
            $sheet->getStyle("A" . ($headerRow + 1) . ":{$highestColumn}{$highestRow}")
                ->getFont()->setSize(12);

            $sheet->getStyle("A6:{$highestColumn}6")
            ->getAlignment()
            ->setWrapText(true);  
            
            $sheet->getStyle("A6:{$highestColumn}6")
    ->getFont()->setSize(11); // instead of 15

            /* ================= SIGNATORIES ================= */

          /* ================= SIGNATORIES (IMAGE FORMAT) ================= */

            $preparedByName = $this->preparedByName;
            $preparedByPosition = $this->preparedByPosition;

            $certifiedByName = $this->certifiedByName;
            $certifiedByPosition = $this->certifiedByPosition;

            $approvedByName = $this->approvedByName ?? 'PA ARNUFO R. PAJARILLO';
            $approvedByPosition = $this->approvedByPosition ?? 'Presidential Assistant for Internal Management Cluster';

            $secondCertifiedName = $this->secondCertifiedName ?? 'CHARLIEZ JANE R. SORIANO';
            $secondCertifiedPosition = $this->secondCertifiedPosition ?? 'OIC, DIRECTOR IV-FMS';

            $secondCertifiedByName = $this->secondCertifiedByName ?? 'ALEX C. ORENDAIN';
            $secondCertifiedByPosition = $this->secondCertifiedByPosition ?? 'Administrative Officer V';

            $startRow = $sheet->getHighestRow() + 3;

            /* ===== ROW B MUST BE DEFINED EARLY ===== */
            $rowB = $startRow + 5;


            /* ===== ROW A ===== */
            $sheet->mergeCells("A{$startRow}:F" . ($startRow));   // Prepared
            $sheet->mergeCells("G{$startRow}:I" . ($startRow));   // Certified
            $sheet->mergeCells("K{$startRow}:R" . ($startRow));   // Approved

            // FORCE ROW HEIGHT (VERY IMPORTANT)
            $sheet->getRowDimension($startRow)->setRowHeight(22);
            $sheet->getRowDimension($startRow + 1)->setRowHeight(22);
            $sheet->getRowDimension($startRow + 2)->setRowHeight(26);
            $sheet->getRowDimension($startRow + 3)->setRowHeight(22);

            $sheet->getRowDimension($rowB)->setRowHeight(22);
            $sheet->getRowDimension($rowB + 1)->setRowHeight(22);
            $sheet->getRowDimension($rowB + 2)->setRowHeight(26);
            $sheet->getRowDimension($rowB + 3)->setRowHeight(22);

            // ENABLE TEXT WRAP
            $sheet->getStyle("A{$startRow}:R" . ($rowB + 3))
                ->getAlignment()
                ->setWrapText(true);


            $sheet->setCellValue("A{$startRow}", "A  PREPARED BY:");
            $sheet->setCellValue("G{$startRow}", "CERTIFIED: Services duly rendered as stated.");
            $sheet->setCellValue("K{$startRow}", "APPROVED FOR PAYMENT:");

            $sheet->mergeCells("A" . ($startRow + 4) . ":B" . ($startRow + 4)); // Prepared
            $sheet->mergeCells("A" . ($startRow + 5) . ":B" . ($startRow + 5)); // Prepared

            $sheet->setCellValue("A" . ($startRow + 4), $preparedByName ?: ' ');
            $sheet->setCellValue("A" . ($startRow + 5), $preparedByPosition ?: '');

            $sheet->mergeCells("G" . ($startRow + 4) . ":I" . ($startRow + 4)); // Certified
            $sheet->mergeCells("G" . ($startRow + 5) . ":I" . ($startRow + 5)); // Certified

            $sheet->setCellValue("G" . ($startRow + 4), $certifiedByName ?: '');
            $sheet->setCellValue("G" . ($startRow + 5), $certifiedByPosition ?: '');

            $sheet->mergeCells("K" . ($startRow + 4) . ":N" . ($startRow + 4)); // Certified
            $sheet->mergeCells("K" . ($startRow + 5) . ":N" . ($startRow + 5)); // Certified

            $sheet->setCellValue("K" . ($startRow + 4), $approvedByName ?: '');
            $sheet->setCellValue("K" . ($startRow + 5), $approvedByPosition ?: '');

            /* ===== ROW B ===== */
            $rowB = $startRow + 7;

            $sheet->mergeCells("A{$rowB}:J" . ($rowB));
            $sheet->mergeCells("K{$rowB}:R" . ($rowB));
            

            $sheet->setCellValue("A{$rowB}", "B  CERTIFIED: Supporting documents complete and proper.");
            $sheet->setCellValue("K{$rowB}", "CERTIFIED: Each employee whose name appears on the payroll hass been paid the amount as indicated opposite his/her name.");
           


            $sheet->mergeCells("A" . ($rowB + 3) . ":B" . ($rowB + 3)); // Prepared
            $sheet->mergeCells("A" . ($rowB + 4) . ":B" . ($rowB + 4)); // Prepared

            $sheet->setCellValue("A" . ($rowB + 3), $secondCertifiedName);
            $sheet->setCellValue("A" . ($rowB + 4), $secondCertifiedPosition);

            $sheet->mergeCells("K" . ($rowB + 3) . ":M" . ($rowB + 3)); // Prepared
            $sheet->mergeCells("K" . ($rowB + 4) . ":M" . ($rowB + 4)); // Prepared

            $sheet->setCellValue("K" . ($rowB + 3), $secondCertifiedByName);
            $sheet->setCellValue("K" . ($rowB + 4), $secondCertifiedPosition);

            $sheet->mergeCells("O" . ($rowB + 3) . ":P" . ($rowB + 3)); // Prepared
            $sheet->mergeCells("O" . ($rowB + 4) . ":P" . ($rowB + 4)); // Prepared

            $sheet->setCellValue("O" . ($rowB + 3), "______________");
            $sheet->setCellValue("O" . ($rowB + 4), "Date");

            /* ===== ALIGNMENT ===== */
           

         $sheet->getStyle("G" . ($startRow) . ":H" . ($startRow))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle("A" . ($startRow + 4) . ":F" . ($startRow + 5))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

         $sheet->getStyle("G" . ($startRow + 4) . ":I" . ($startRow + 5))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);   
            
         $sheet->getStyle("J" . ($startRow + 4) . ":R" . ($startRow + 5))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);    

    
        $sheet->getStyle("A" . ($rowB + 3) . ":I" . ($rowB + 4))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); 
            
        $sheet->getStyle("J" . ($rowB + 3) . ":R" . ($rowB + 4))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);     


            /* ===== BORDERS ===== */
            $blocks = [
                "A{$startRow}:F" . ($startRow + 5),
                "G{$startRow}:J" . ($startRow + 5),
                "K{$startRow}:R" . ($startRow + 5),
                "A{$rowB}:J" . ($rowB + 5),
                "K{$rowB}:R" . ($rowB + 5),
            ];

            foreach ($blocks as $range) {
                $sheet->getStyle($range)
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle(Border::BORDER_THIN);
            }

            /* ===== FONT STYLE ===== */
            $sheet->getStyle("A{$startRow}:R{$startRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$rowB}:R{$rowB}")->getFont()->setBold(true);

            $sheet->getStyle("A" . ($startRow + 4))->getFont()->setBold(true);
            $sheet->getStyle("G" . ($startRow + 4))->getFont()->setBold(true);
            $sheet->getStyle("K" . ($startRow + 4))->getFont()->setBold(true);
            $sheet->getStyle("A" . ($rowB + 3))->getFont()->setBold(true);
            $sheet->getStyle("J" . ($rowB + 3))->getFont()->setBold(true);
            $sheet->getStyle("K" . ($rowB + 3))->getFont()->setBold(true);


            /* ================= AUTO WIDTH ================= */

            /*foreach (range(1, Coordinate::columnIndexFromString($highestColumn)) as $col) {
                $letter = Coordinate::stringFromColumnIndex($col);
                $sheet->getColumnDimension($letter)->setAutoSize(true);
            }*/

            // ===== FIX COLUMN WIDTHS (PREVENT HEADER STRETCHING) =====
$fixedWidths = [
    'A' => 20,  // NAME
    'B' => 20,  // POSITION
    'C' => 20,
    'D' => 12,
    'E' => 28,
    'F' => 12,
    'G' => 12,
    'H' => 18,
    'I' => 18,
    'J' => 14,
    'K' => 12,
    'L' => 12,
    'M' => 12,
    'N' => 12,  // CPL
    'O' => 14,
    'P' => 16,
    'Q' => 22,
    'R' => 14,
    'S' => 14,
    'T' => 20,
    'U' => 16,
    'V' => 16,
    'W' => 14,
    'X' => 14,
    'Y' => 18,
    'Z' => 14,
    'AA' => 14,
    ];

foreach ($fixedWidths as $col => $width) {
    $sheet->getColumnDimension($col)->setWidth($width);
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

                /* DEPARTMENT ROW */
                if (str_contains($value, 'DEPARTMENT')) {
                    $sheet->mergeCells("A{$row}:{$highestColumn}{$row}");

                    $sheet->getStyle("A{$row}")
                        ->getFont()->setBold(true)->setSize(13);

                    $sheet->getStyle("A{$row}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    $sheet->getStyle("A{$row}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('D9E1F2');
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
            elseif (str_contains($value, 'SUB-TOTAL')) {

                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getFont()->setBold(true);

                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('d37eb1'); // yellow
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

            $sheet->getPageSetup()->setScale(90); // or 85 if still wide

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
