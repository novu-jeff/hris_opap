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
        ->with('information.section.department', 'information.positions')
        ->get();

        if ($this->filterSalaryMethod) {
            $items = $items->filter(function ($item) {
                return $item->information
                    && strcasecmp(
                        $item->information->salary_method,
                        $this->filterSalaryMethod
                    ) === 0;
            });
        }

         // --- Filter by Employee Name ---
        if ($this->searchName) {
            $items = $items->filter(function ($item) {
                return str_contains(
                    strtolower($item->name),
                    strtolower($this->searchName)
                );
            });
        }

       

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
        'W/TAX','UCA','DISALLOWANCE','AUT','TOTAL DED','NET AMOUNT','DBP BRANCH',
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

            // ✅ SORT BY SALARY GRADE (HIGHEST → LOWEST)
                $employees = $employees->sortByDesc(function ($item) {
                    return $item->salary_grade
                        ?? optional($item->information?->positions)->salary_grade
                        ?? 0;
                });
            // SECTION LABEL ROW

            $rows->push(["SECTION: {$section}"]);
            foreach ($employees as $item) {
                // Salary grade is stored on `positions` through `employee_information` relationship.
                $salaryGrade = $item->salary_grade
                    ?? optional($item->information?->positions)->salary_grade
                    ?? '';
                $positionWithSalaryGrade = $salaryGrade !== ''
                    ? "{$item->position} (SG{$salaryGrade})"
                    : $item->position;
                $rows->push([
                    "{$employeeCount}. {$item->name}",
                    $positionWithSalaryGrade,
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
                    $item->disallowance ?? 0,
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
                $employees->sum('disallowance'),
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
            $departmentEmployees->sum('disallowance'),
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
        $rows->push([' ']); // extra spacer (visual separation)
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
        $items->sum('disallowance'),
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

            $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
            ->getFont()
            ->setName('Calibri')   // Excel default
            ->setSize(11);

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

            $sheet->insertNewRowBefore(1, 6);

            $sheet->setCellValue('A1', 'Republic of the Philippines');
            $sheet->setCellValue('A2', 'OFFICE OF THE PRESIDENTIAL ADVISER ON PEACE, RECONCILIATION AND UNITY');
            $sheet->setCellValue('A3', 'PAYROLL');
            $sheet->setCellValue('A4', 'For the Period: ' . $formattedCutoff);
            $sheet->setCellValue('A5', 'Payroll Date: ' . Carbon::parse($this->payroll->payroll_date)->format('F d, Y'));

            foreach (range(1,5) as $row) {
                $sheet->mergeCells("A{$row}:{$highestColumn}{$row}");
            }

            /* ALIGNMENT */
            $sheet->getStyle("A1:A5")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setVertical(Alignment::VERTICAL_CENTER);

            /* FONT SIZES (MATCH TEMPLATE HIERARCHY) */
            $sheet->getStyle('A1')->getFont()->setSize(11);
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A4:A5')->getFont()->setSize(11);

            /* ROW HEIGHTS (IMPORTANT FOR PIXEL MATCH) */
            $sheet->getRowDimension(1)->setRowHeight(18);
            $sheet->getRowDimension(2)->setRowHeight(18);
            $sheet->getRowDimension(3)->setRowHeight(22);
            $sheet->getRowDimension(4)->setRowHeight(18);
            $sheet->getRowDimension(5)->setRowHeight(18);

            /* ================= TABLE HEADER STYLE ================= */

            $headerRow = 7;

            $sheet->getStyle("A{$headerRow}:{$highestColumn}{$headerRow}")
                ->getFont()->setBold(true)->setSize(10);

            $sheet->getStyle("A{$headerRow}:{$highestColumn}{$headerRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);

            /* EXACT HEIGHT */
            $sheet->getRowDimension($headerRow)->setRowHeight(32);

            /* LIGHT GRAY HEADER (NOT BLUE) */
            $sheet->getStyle("A{$headerRow}:{$highestColumn}{$headerRow}")
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D9D9D9');

            /* ================= FREEZE HEADER ================= */

           $sheet->freezePane('H8');

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

            $certifiedByName = 'DIR. FRANCISCO F. MENDOZA,  JR';
            $certifiedByPosition = 'HEAD, HRMS';

            $approvedByName = $this->approvedByName ?? 'PA ARNUFO R. PAJARILLO';
            $approvedByPosition = $this->approvedByPosition ?? 'Presidential Assistant for Internal Management Cluster';

            $secondCertifiedName = $this->secondCertifiedName ?? 'JENNIE CLAIRE L. MORDENO';
            $secondCertifiedPosition = $this->secondCertifiedPosition ?? 'OIC, DIRECTOR IV-FMS';

            $secondCertifiedByName = $this->secondCertifiedByName ?? 'ALEX C. ORENDAIN';
            $secondCertifiedByPosition = 'Administrative Officer V';

            $startRow = $sheet->getHighestRow() + 3;

            /* ===== ROW B MUST BE DEFINED EARLY ===== */
            $rowB = $startRow + 5;


            /* ===== ROW A ===== */
            $sheet->mergeCells("A{$startRow}:D" . ($startRow));   // Prepared
            $sheet->mergeCells("E{$startRow}:G" . ($startRow));   // Certified
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


            $sheet->setCellValue("A{$startRow}", "A: PREPARED BY:");
            $sheet->setCellValue("E{$startRow}", "CERTIFIED: Services duly rendered as stated.");
            $sheet->setCellValue("K{$startRow}", "C: APPROVED FOR PAYMENT:");

            $sheet->mergeCells("A" . ($startRow + 4) . ":B" . ($startRow + 4)); // Prepared
            $sheet->mergeCells("A" . ($startRow + 5) . ":B" . ($startRow + 5)); // Prepared

            $sheet->setCellValue("A" . ($startRow + 4), $preparedByName ?: ' ');
            $sheet->setCellValue("A" . ($startRow + 5), $preparedByPosition ?: '');

            $sheet->mergeCells("C" . ($startRow + 4) . ":D" . ($startRow + 4)); 
            $sheet->mergeCells("C" . ($startRow + 5). ":D" . ($startRow + 5));  

            $sheet->setCellValue("C" . ($startRow + 4), "______________");
            $sheet->setCellValue("C" . ($startRow + 5), "Date");

            $sheet->mergeCells("E" . ($startRow + 4) . ":G" . ($startRow + 4)); // Certified
            $sheet->mergeCells("E" . ($startRow + 5) . ":G" . ($startRow + 5)); // Certified

            $sheet->setCellValue("E" . ($startRow + 4), $certifiedByName ?: '');
            $sheet->setCellValue("E" . ($startRow + 5), $certifiedByPosition ?: '');
 

            $sheet->setCellValue("I" . ($startRow + 4), "______________");
            $sheet->setCellValue("I" . ($startRow + 5), "Date");

            $sheet->mergeCells("K" . ($startRow + 4) . ":N" . ($startRow + 4)); // Certified
            $sheet->mergeCells("K" . ($startRow + 5) . ":N" . ($startRow + 5)); // Certified

            $sheet->setCellValue("K" . ($startRow + 4), $approvedByName ?: '');
            $sheet->setCellValue("K" . ($startRow + 5), $approvedByPosition ?: '');

            $sheet->setCellValue("P" . ($startRow + 4), "______________");
            $sheet->setCellValue("P" . ($startRow + 5), "Date");

            /* ===== ROW B ===== */
            $rowB = $startRow + 7;

            $sheet->mergeCells("A{$rowB}:D" . ($rowB));
            $sheet->mergeCells("E{$rowB}:I" . ($rowB));
            

            $sheet->setCellValue("A{$rowB}", "B:  CERTIFIED: Supporting documents complete and proper; and cash available in the amount of ");
            $sheet->setCellValue("A" . ($rowB + 1), "₱ __________.");
            $sheet->setCellValue("E{$rowB}", "D: CERTIFIED: Each employee whose name appears on the payroll hass been paid the amount as indicated opposite his/her name.");
           


            $sheet->mergeCells("A" . ($rowB + 4) . ":B" . ($rowB + 4)); // Prepared
            $sheet->mergeCells("A" . ($rowB + 5) . ":B" . ($rowB + 5)); // Prepared

            $sheet->setCellValue("A" . ($rowB + 4), $secondCertifiedName);
            $sheet->setCellValue("A" . ($rowB + 5), $secondCertifiedPosition);

            $sheet->mergeCells("E" . ($rowB + 4) . ":G" . ($rowB + 4)); 
            $sheet->mergeCells("E" . ($rowB + 5) . ":G" . ($rowB + 5)); 

           // Prepared

            $sheet->setCellValue("E" . ($rowB + 4), $secondCertifiedByName);
            $sheet->setCellValue("E" . ($rowB + 5), $secondCertifiedByPosition);

            $sheet->mergeCells("L" . ($rowB + 1) . ":N" . ($rowB + 1)); 
            $sheet->mergeCells("L" . ($rowB + 2) . ":N" . ($rowB + 2)); 
            $sheet->mergeCells("L" . ($rowB + 3) . ":N" . ($rowB + 3)); // Prepared
            $sheet->mergeCells("L" . ($rowB + 4) . ":N" . ($rowB + 4)); 

            $sheet->setCellValue("L" . ($rowB + 1), "ORS/BURS No.:________________");
            $sheet->setCellValue("L" . ($rowB + 2), "DATE:________________");
            $sheet->setCellValue("L" . ($rowB + 3), "JEV No.:________________");
            $sheet->setCellValue("L" . ($rowB + 4), "DATE:________________");


            $sheet->mergeCells("C" . ($rowB + 4) . ":D" . ($rowB + 4)); 
            $sheet->mergeCells("C" . ($rowB + 5). ":D" . ($rowB + 5));  

            $sheet->setCellValue("C" . ($rowB + 4), "______________");
            $sheet->setCellValue("C" . ($rowB + 5), "Date");

            $sheet->setCellValue("I" . ($rowB + 4), "______________");
            $sheet->setCellValue("I" . ($rowB + 5), "Date");

            /* ===== ALIGNMENT ===== */
           
          
         $sheet->getStyle("E" . ($startRow) . ":I" . ($startRow))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A" . ($startRow + 4) . ":E" . ($startRow + 5))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

         $sheet->getStyle("E" . ($startRow + 4) . ":G" . ($startRow + 5))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);   
            
         $sheet->getStyle("J" . ($startRow + 4) . ":R" . ($startRow + 5))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);  
            
        $sheet->getStyle("C" . ($startRow + 5) . ":D" . ($startRow + 5))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);  
        $sheet->getStyle("C" . ($startRow + 5))->getFont()->setBold(true);  
        
        $sheet->getStyle("C" . ($rowB + 5) . ":D" . ($rowB + 5))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);  
        $sheet->getStyle("C" . ($rowB + 5))->getFont()->setBold(true); 

        $sheet->getStyle("I" . ($startRow + 5))
        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);  
    $sheet->getStyle("I" . ($startRow + 5))->getFont()->setBold(true); 

    $sheet->getStyle("P" . ($startRow + 5))
        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);  
    $sheet->getStyle("P" . ($startRow + 5))->getFont()->setBold(true); 

    
        $sheet->getStyle("A" . ($rowB + 4) . ":I" . ($rowB + 5))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); 
            
     
            
        $sheet->getStyle("I" . ($rowB + 5))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);  
        $sheet->getStyle("I" . ($rowB + 5))->getFont()->setBold(true);     


            /* ===== BORDERS ===== */
            $blocks = [
                "A{$startRow}:D" . ($startRow + 5),
                "E{$startRow}:J" . ($startRow + 5),
                "K{$startRow}:R" . ($startRow + 5),
                "A{$rowB}:D" . ($rowB + 5),
                "E{$rowB}:J" . ($rowB + 5),
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
            $sheet->getStyle("E" . ($startRow + 4))->getFont()->setBold(true);
            $sheet->getStyle("K" . ($startRow + 4))->getFont()->setBold(true);
            $sheet->getStyle("A" . ($rowB + 4))->getFont()->setBold(true);
            $sheet->getStyle("J" . ($rowB + 3))->getFont()->setBold(true);
            $sheet->getStyle("E" . ($rowB + 4))->getFont()->setBold(true);


            /* ================= AUTO WIDTH ================= */

            /*foreach (range(1, Coordinate::columnIndexFromString($highestColumn)) as $col) {
                $letter = Coordinate::stringFromColumnIndex($col);
                $sheet->getColumnDimension($letter)->setAutoSize(true);
            }*/

            // ===== FIX COLUMN WIDTHS (PREVENT HEADER STRETCHING) =====
$fixedWidths = [
    'A' => 30,  // NAME
    'B' => 35,  // POSITION
    'C' => 15,
    'D' => 12,
    'E' => 18,
    'F' => 12,
    'G' => 12,
    'H' => 14,
    'I' => 14,
    'J' => 14,
    'K' => 12,
    'L' => 12,
    'M' => 12,
    'N' => 12,  // CPL
    'O' => 14,
    'P' => 16,
    'Q' => 18,
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

                // Solid bottom border for SUB-TOTAL rows
                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getBorders()->getBottom()
                    ->setBorderStyle(Border::BORDER_THICK);
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
