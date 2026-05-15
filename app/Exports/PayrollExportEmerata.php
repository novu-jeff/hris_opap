<?php

namespace App\Exports;

use App\Models\PayrollEmeRata;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PayrollExportEmerata implements FromCollection, WithEvents
{
    protected $payroll;
    protected $filterSalaryMethod;
    protected $searchName;
    protected $supervisingOfficer;
    protected $preparedByName;
    protected $preparedByPosition;
    protected $certifiedByName;
    protected $certifiedByPosition;

    public function __construct(PayrollEmeRata $payroll, $filterSalaryMethod = '', $searchName = '', $supervisingOfficer = null, $chiefadministrativeOfficer = null)
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
    ->orderBy('position')
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

        /*
        TEMPLATE HEADER
        */


$rows->push([""]); // row 3
$rows->push([""]); // row 3
$rows->push([""]); // row 3
$rows->push([""]); // row 3
$rows->push([""]); // row 3
$rows->push([""]); // row 3
$rows->push([""]); // row 3


    
    
        $counter = 1;

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

                $salaryGrade = $item->salary_grade
                    ?? optional($item->information?->positions)->salary_grade
                    ?? '';
                    $positionWithSalaryGrade = $salaryGrade !== ''
                    ? strtoupper("{$item->position} (SG{$salaryGrade})")
                    : strtoupper($item->position);
                $rows->push([
                    $counter,                    // A
                    strtoupper($item->name),     // B
                    '', '', '', '', '',          // C-G filler
                
                    $positionWithSalaryGrade, // H
                    '', '', '', '', '',          // I-M filler
                
                    '', $item->ra ?? 0,              // N
                    '',                      // O-P filler
                
                                // Q
                    '','', $item->ta ?? 0, '',                  // R-T filler
                
                    '',      // U
                    '', $item->net_amount ?? 0, '',                  // V-X filler
                
                    '', ''                       // Y-Z signature
                ]);
        
                $counter++;
            }
        }
        
    }   
        
            /*
            GRAND TOTAL
            */
            $rows->push([
                '',
                '',
                'TOTAL',
                '', '', '', '',
            
                '', '', '', '', '', '',
            
                '',
                $items->sum('ra'), '',
            
                '',
                '', $items->sum('ta'), '',
            
                '',
                '',$items->sum('net_amount'), '',
            
                '', ''
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
    
                /*
                ==================================================
                EXACT GOVERNMENT TEMPLATE LAYOUT
                RA/TA PAYROLL FORM
                ==================================================
                */
    
                // GLOBAL FONT
                $sheet->getStyle("A1:Z{$highestRow}")
                    ->getFont()
                    ->setName('Arial')
                    ->setSize(10);
    
                // MAIN TITLES
                $sheet->mergeCells('A1:Z1');
                $sheet->mergeCells('A2:Z2');
    
                $sheet->setCellValue('A1', 'PAYROLL');
                $sheet->setCellValue(
                    'A2',
                    'RA/TA FOR THE MONTH OF ' . strtoupper(Carbon::parse($this->payroll->payroll_date)->format('F Y'))
                );
    
                $sheet->getStyle('A1:Z2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A1:Z2')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
    
                // ENTITY + FUND CLUSTER
                $sheet->mergeCells('A3:M3');
                $sheet->mergeCells('A4:M4');
    
                $sheet->setCellValue('A3', 'Entity Name: Office of the Presidential Adviser on Peace, Reconciliation and Unity (OPAPRU)');
                $sheet->setCellValue('A4', 'Fund Cluster:');
    
                // PAYROLL NUMBER BLOCK
                $sheet->mergeCells('W3:Z3');
                $sheet->mergeCells('W4:Z4');
    
                $sheet->setCellValue('W3', 'Payroll No.: __________');
                $sheet->setCellValue('W4', 'Sheet ___ of ___ sheets');
    
                // ACKNOWLEDGEMENT TEXT
                $sheet->mergeCells('A5:Z5');
                $sheet->setCellValue(
                    'A5',
                    'We acknowledge receipt of the sum shown opposite our names as full compensation for services rendered for the period stated.'
                );
    
                // TABLE HEADER
                $headerRow = 7;
    
                $headers = [
                    'A7' => 'NO.',
                    'B7' => 'NAME',
                    'H7' => 'POSITION',
                    'N7' => 'RA',
                    'R7' => 'TA',
                    'V7' => 'NET AMOUNT DUE',
                    'Y7' => 'SIGNATURE OF PAYEE',
                ];
    
                foreach ($headers as $cell => $value) {
                    $sheet->setCellValue($cell, $value);
                }
    
                // HEADER MERGES
                $sheet->mergeCells('B7:G7');
                $sheet->mergeCells('H7:M7');
                $sheet->mergeCells('N7:P7');
                $sheet->mergeCells('R7:T7');
                $sheet->mergeCells('V7:X7');
                $sheet->mergeCells('Y7:Z7');
    
                $sheet->getStyle('A7:Z7')->getFont()->setBold(true);
                $sheet->getStyle('A7:Z7')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

                    for ($row = 1; $row <= $highestRow + 5; $row++) {

                        
                        
                        $value = $sheet->getCell("C{$row}")->getValue()
                                ?: $sheet->getCell("B{$row}")->getValue()
                                ?: $sheet->getCell("A{$row}")->getValue();
        
                        if (!$value) continue;
        
                        /* SECTION ROW */
                        if (str_contains($value, 'SECTION:')) {
        
                            $sheet->mergeCells("A{$row}:X{$row}");
        
                            $sheet->getStyle("A{$row}")
                                ->getFont()->setBold(true)->setSize(10);
        
                            $sheet->getStyle("A{$row}")
                                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
                            $sheet->getStyle("A{$row}")
                                ->getFill()->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('D9EAD3');
                        }
        
                        /* DEPARTMENT ROW */
                        if (str_contains($value, 'DEPARTMENT')) {
                            $sheet->mergeCells("A{$row}:X{$row}");
        
                            $sheet->getStyle("A{$row}")
                                ->getFont()->setBold(true)->setSize(10);
        
                            $sheet->getStyle("A{$row}")
                                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
                            $sheet->getStyle("A{$row}")
                                ->getFill()->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('D9E1F2');
                        }
        
                        
                        if (str_contains($value, 'TOTAL')) {

                            /*
                            =========================================
                            TOTAL ROW HIGHLIGHT
                            Same as first screenshot
                            =========================================
                            */
                        
                            // Bold + font size
                            $sheet->getStyle("A{$row}:X{$row}")
                                ->getFont()
                                ->setBold(true)
                                ->setSize(11);
                        
                            // Yellow highlight fill
                            $sheet->getStyle("A{$row}:X{$row}")
                                ->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setRGB('FFF2CC'); // light yellow
                        
                            // Thick top + bottom border
                            $sheet->getStyle("A{$row}:X{$row}")
                                ->getBorders()
                                ->getTop()
                                ->setBorderStyle(Border::BORDER_THICK);
                        
                            $sheet->getStyle("A{$row}:T{$row}")
                                ->getBorders()
                                ->getBottom()
                                ->setBorderStyle(Border::BORDER_THICK);
                        
                            // Right align amount columns
                            foreach (['O', 'S', 'W'] as $col) {
                                $sheet->getStyle("{$col}{$row}")
                                    ->getAlignment()
                                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            }
                        }
                        
                        
                    }    
    
                // BORDERS
                $sheet->getStyle("A7:Z{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
                
                    $sheet->getStyle("A8:Z{$highestRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);  
                    
                    foreach (['N', 'Q', 'U'] as $col) {
                        $sheet->getStyle("{$col}8:{$col}{$highestRow}")
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
    
               // FOOTER BOXES
                /*
REPLACE ONLY YOUR FOOTER BOXES SECTION
(Current problem is the signature/footer layout)

FROM:
 // FOOTER BOXES
 $footer = $highestRow + 4;

TO:
 use this exact version below
*/

$footer = $highestRow + 2;

/*
==================================================
TOP SIGNATURE BLOCK
A | CERTIFIED | C APPROVED FOR PAYMENT
MATCH FIRST SCREENSHOT EXACTLY
==================================================
*/

// A
$sheet->mergeCells("A{$footer}:H{$footer}");
$sheet->setCellValue("A{$footer}", 'A  PREPARED BY:');

// middle certified
$sheet->mergeCells("I{$footer}:P{$footer}");
$sheet->setCellValue(
    "I{$footer}",
    'CERTIFIED: Services duly rendered as stated.'
);

// C
$sheet->mergeCells("Q{$footer}:V{$footer}");
$sheet->setCellValue(
    "Q{$footer}",
    'C  APPROVED FOR PAYMENT:'
);

/*
==================================================
NAMES
==================================================
*/

$nameRow = $footer + 2;
$signameRow = $footer + 1;

$sheet->mergeCells("A{$signameRow}:H{$signameRow}");
$sheet->mergeCells("A{$nameRow}:H{$nameRow}");
$sheet->setCellValue(
    "A{$nameRow}",
    strtoupper($this->preparedByName ?: 'BEA MILAN A. CLERIGO')
);

$sheet->mergeCells("I{$signameRow}:P{$signameRow}");
$sheet->mergeCells("I{$nameRow}:P{$nameRow}");
$sheet->setCellValue(
    "I{$nameRow}",
    strtoupper($this->certifiedByName ?: 'DIR. FRANCISCO F. MENDOZA, JR.')
);

$sheet->mergeCells("Q{$signameRow}:V{$signameRow}");
$sheet->mergeCells("Q{$nameRow}:V{$nameRow}");
$sheet->setCellValue(
    "Q{$nameRow}",
    'PA ARNUFO R. PAJARILLO, JR.'
);

/*
==================================================
POSITIONS + DATE
==================================================
*/

$positionRow = $nameRow + 1;

$sheet->mergeCells("A{$positionRow}:F{$positionRow}");
$sheet->setCellValue(
    "A{$positionRow}",
    strtoupper($this->preparedByPosition ?: 'SAO-HRMS')
);

$sheet->mergeCells("G{$positionRow}:H{$positionRow}");
$sheet->setCellValue("G{$positionRow}", 'DATE');

$sheet->mergeCells("I{$positionRow}:P{$positionRow}");
$sheet->setCellValue(
    "I{$positionRow}",
    strtoupper($this->certifiedByPosition ?: 'DIRECTOR IV - HRMS')
);

$sheet->mergeCells("Q{$positionRow}:V{$positionRow}");
$sheet->setCellValue(
    "Q{$positionRow}",
    'Presidential Assistant for Internal Management Cluster'
);

/*
==================================================
LOWER BOXES
B | D | E
==================================================
*/

$lower = $positionRow + 2;

/* B */
$sheet->mergeCells("A{$lower}:P{$lower}");
$sheet->setCellValue(
    "A{$lower}",
    'B  CERTIFIED: Supporting documents complete and proper; and cash available in the amount of'
);

/* D */
$sheet->mergeCells("Q{$lower}:U{$lower}");
$sheet->setCellValue(
    "Q{$lower}",
    'D  CERTIFIED: Each employee whose name appears on the payroll has'
);

/* E */
$sheet->mergeCells("V{$lower}:V{$lower}");
$sheet->setCellValue(
    "V{$lower}",
    'E'
);

/*
==================================================
BOTTOM NAMES
==================================================
*/

$bottomNameRow = $lower + 3;
$sigbottomNameRow = $lower + 2;

$sheet->mergeCells("A{$sigbottomNameRow}:P{$sigbottomNameRow}");
$sheet->mergeCells("A{$bottomNameRow}:P{$bottomNameRow}");
$sheet->setCellValue(
    "A{$bottomNameRow}",
    'JENNIE CLAIRE L. MORDENO'
);

$sheet->mergeCells("Q{$sigbottomNameRow}:U{$sigbottomNameRow}");
$sheet->mergeCells("Q{$bottomNameRow}:U{$bottomNameRow}");
$sheet->setCellValue(
    "Q{$bottomNameRow}",
    'ALEX C. ORENDAIN'
);

/*
==================================================
BOTTOM POSITIONS
==================================================
*/

$bottomPositionRow = $bottomNameRow + 1;

$sheet->mergeCells("A{$bottomPositionRow}:M{$bottomPositionRow}");
$sheet->setCellValue(
    "A{$bottomPositionRow}",
    'Officer-in-Charge, Director IV-FMS'
);

$sheet->mergeCells("N{$bottomPositionRow}:P{$bottomPositionRow}");
$sheet->setCellValue(
    "N{$bottomPositionRow}",
    'Date'
);

$sheet->mergeCells("Q{$bottomPositionRow}:U{$bottomPositionRow}");
$sheet->setCellValue(
    "Q{$bottomPositionRow}",
    'Administrative Officer V'
);

/*
==================================================
RIGHT SMALL BOX (E)
==================================================
*/

$sheet->setCellValue("V" . ($lower + 1), 'ORS/BURS No.:');
$sheet->setCellValue("V" . ($lower + 2), 'Date:');
$sheet->setCellValue("V" . ($lower + 3), 'JEV No.:');
$sheet->setCellValue("V" . ($lower + 4), 'Date:');

/*
==================================================
BORDERS
==================================================
*/

$sheet->getStyle("A{$footer}:V" . ($lower + 5))
    ->getBorders()
    ->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN);

$sheet->getStyle("A{$footer}:V" . ($lower + 5))
    ->getAlignment()
    ->setVertical(Alignment::VERTICAL_CENTER);
                

                $moneyColumns = [
                    'N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC'
                ];
    
                foreach ($moneyColumns as $col) {
                    $sheet->getStyle("{$col}" . ($headerRow + 1) . ":{$col}{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                }
                       
    
                // PAGE SETUP
                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(PageSetup::PAPERSIZE_LEGAL)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);
    
                $sheet->freezePane('A8');
            }
        ];
    }
    
    
    }
    