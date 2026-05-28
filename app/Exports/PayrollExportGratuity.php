<?php

namespace App\Exports;

use App\Models\PayrollGratuity;
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

class PayrollExportGratuity implements FromCollection, WithEvents
{
    protected $payroll;
    protected $filterSalaryMethod;
    protected $searchName;
    protected $supervisingOfficer;
    protected $preparedByName;
    protected $preparedByPosition;
    protected $certifiedByName;
    protected $certifiedByPosition;

    public function __construct(PayrollGratuity $payroll, $filterSalaryMethod = '', $searchName = '', $supervisingOfficer = null, $chiefadministrativeOfficer = null)
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
        ->with(
            'information.section.department',
            'information.positions'
        )
        ->orderBy('position')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | FILTER SALARY METHOD
    |--------------------------------------------------------------------------
    */

    if ($this->filterSalaryMethod) {

        $items = $items->filter(function ($item) {

            return $item->information
                && strcasecmp(
                    $item->information->salary_method,
                    $this->filterSalaryMethod
                ) === 0;

        });

    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH FILTER
    |--------------------------------------------------------------------------
    */

    if ($this->searchName) {

        $items = $items->filter(function ($item) {

            return str_contains(
                strtolower($item->name),
                strtolower($this->searchName)
            );

        });

    }

    /*
    |--------------------------------------------------------------------------
    | GROUP BY DEPARTMENT
    |--------------------------------------------------------------------------
    */

    $groupedByDepartment = $items

        ->sortBy(function ($item) {

            $department =
                optional(
                    $item->information
                        ->section
                        ?->department
                );

            $code = $department->code ?? '';

            if ($code === 'EO') {
                return 0;
            }

            if (
                preg_match(
                    '/^P(\d+)/',
                    $code,
                    $matches
                )
            ) {

                return 1 + (int) $matches[1];

            }

            return 9999;

        })

        ->groupBy(function ($item) {

            $department =
                optional(
                    $item->information
                        ->section
                        ?->department
                );

            return $department->name

                ? (
                    $department->code

                    ? "{$department->code} - {$department->name}"

                    : $department->name
                )

                : 'NONE';

        });

    /*
    |--------------------------------------------------------------------------
    | ROWS
    |--------------------------------------------------------------------------
    */

    $rows = collect();

    /*
    |--------------------------------------------------------------------------
    | TEMPLATE SPACING
    |--------------------------------------------------------------------------
    */

    for ($i = 1; $i <= 7; $i++) {

        $rows->push([""]);

    }

    /*
    |--------------------------------------------------------------------------
    | COUNTER
    |--------------------------------------------------------------------------
    */

    $counter = 1;

    /*
    |--------------------------------------------------------------------------
    | DEPARTMENT LOOP
    |--------------------------------------------------------------------------
    */

    foreach (
        $groupedByDepartment
        as $department => $departmentEmployees
    ) {

        /*
        |--------------------------------------------------------------------------
        | DEPARTMENT LABEL
        |--------------------------------------------------------------------------
        */

        $rows->push([
            "DEPARTMENT {$department}"
        ]);

        /*
        |--------------------------------------------------------------------------
        | GROUP BY SECTION
        |--------------------------------------------------------------------------
        */

        $groupedBySection =
            $departmentEmployees

                ->sortBy(
                    fn ($item) =>
                    optional(
                        $item->information->section
                    )->name
                )

                ->groupBy(function ($item) {

                    $section =
                        optional(
                            $item->information->section
                        );

                    return $section->name

                        ? "{$section->code} - {$section->name}"

                        : 'NO SECTION';

                });

        /*
        |--------------------------------------------------------------------------
        | SECTION LOOP
        |--------------------------------------------------------------------------
        */

        foreach (
            $groupedBySection
            as $section => $employees
        ) {

            /*
            |--------------------------------------------------------------------------
            | SECTION LABEL
            |--------------------------------------------------------------------------
            */

            $rows->push([
                "SECTION: {$section}"
            ]);

            /*
            |--------------------------------------------------------------------------
            | SORT BY SG
            |--------------------------------------------------------------------------
            */

            $employees = $employees->sortByDesc(
                function ($item) {

                    return $item->salary_grade
                        ?? optional(
                            $item->information?->positions
                        )->salary_grade
                        ?? 0;

                }
            );

            /*
            |--------------------------------------------------------------------------
            | EMPLOYEE LOOP
            |--------------------------------------------------------------------------
            */

            foreach ($employees as $item) {

                $salaryGrade =
                    $item->salary_grade
                    ?? optional(
                        $item->information?->positions
                    )->salary_grade
                    ?? '';

                $positionWithSalaryGrade =
                    $salaryGrade !== ''

                    ? strtoupper(
                        "{$item->position} (SG{$salaryGrade})"
                    )

                    : strtoupper($item->position);

                /*
                |--------------------------------------------------------------------------
                | EMPLOYEE ROW
                |--------------------------------------------------------------------------
                */

                $rows->push([

                    $counter,

                    strtoupper($item->name),

                    '', '', '', '', '',

                    $positionWithSalaryGrade,

                    '', '', '',

                    '',

                    !empty($item->date_hired)
                        ? Carbon::parse($item->date_hired)->format('m/d/Y')
                        : '',

                    Carbon::parse(
                        $this->payroll->payroll_date
                    )->format('m/d/Y'),
                    $item->gratuity_pay ?? 0,

                    $item->tax ?? 0,

                    $item->net_amount ?? 0,

                ]);

                $counter++;
            }

            /*
            |--------------------------------------------------------------------------
            | SECTION SUBTOTAL ROW
            |--------------------------------------------------------------------------
            */

            $rows->push([

                '',
                '',
                'SECTION SUBTOTAL',

                '', '', '', '', '', '', '', '', '',

                '',
                '',
                $employees->sum('gratuity_pay'),

                $employees->sum('tax'),

                $employees->sum('net_amount'),

            ]);

        }
    }

    /*
    |--------------------------------------------------------------------------
    | GRAND TOTAL ROW
    |--------------------------------------------------------------------------
    */

    $rows->push([

        '',
        '',
        'GRAND TOTAL',

        '', '', '', '', '', '', '', '', '',

        '',
        '',
        $items->sum('gratuity_pay'),

        $items->sum('tax'),

        $items->sum('net_amount'),

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
                $sheet->getStyle("A1:T{$highestRow}")
                    ->getFont()
                    ->setName('Arial')
                    ->setSize(10);
    
                // MAIN TITLES
                $sheet->mergeCells('A1:T1');
                $sheet->mergeCells('A2:T2');
    
                $sheet->setCellValue('A1', 'PAYROLL');
                $semesterLabel = strtoupper(
                    str_replace(
                        '_',
                        ' ',
                        $this->payroll->semester
                    )
                );

                $sheet->setCellValue(
                    'A2',
                    'GRATUITY PAYROLL - ' .
                    Carbon::parse(
                        $this->payroll->payroll_date
                    )->format('F d, Y')
                );
                
    
                $sheet->getStyle('A1:T2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A1:T2')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
    
                // ENTITY + FUND CLUSTER
                $sheet->mergeCells('A3:M3');
                $sheet->mergeCells('A4:M4');
    
                $sheet->setCellValue('A3', 'Entity Name: Office of the Presidential Adviser on Peace, Reconciliation and Unity (OPAPRU)');
                $sheet->setCellValue('A4', '');
    
                // PAYROLL NUMBER BLOCK
                $sheet->mergeCells('U3:W3');
                $sheet->mergeCells('U4:W4');
    
                $sheet->setCellValue('U3', '');
                $sheet->setCellValue('U4', '');
    
                // ACKNOWLEDGEMENT TEXT
                $sheet->mergeCells('A5:T5');
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
                    'M7' => 'DATE HIRED',
                    'N7' => 'PAYROLL DATE',
                    'O7' => 'GRATUITY PAY',
                    'P7' => 'TAX',
                    'Q7' => 'NET AMOUNT',
                
                ];
                
                
    
                foreach ($headers as $cell => $value) {
                    $sheet->setCellValue($cell, $value);
                }
    
                // HEADER MERGES
                $sheet->mergeCells('B7:G7');
                $sheet->mergeCells('H7:L7');
    
                $sheet->getStyle('A7:W7')->getFont()->setBold(true);
                $sheet->getStyle('A7:W7')->getAlignment()
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
        
                            $sheet->mergeCells("A{$row}:Q{$row}");
        
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
                            $sheet->mergeCells("A{$row}:Q{$row}");
        
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
                            $sheet->getStyle("A{$row}:Q{$row}")
                                ->getFont()
                                ->setBold(true)
                                ->setSize(11);
                        
                            // Yellow highlight fill
                            $sheet->getStyle("A{$row}:Q{$row}")
                                ->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setRGB('FFF2CC'); // light yellow
                        
                            // Thick top + bottom border
                            $sheet->getStyle("A{$row}:Q{$row}")
                                ->getBorders()
                                ->getTop()
                                ->setBorderStyle(Border::BORDER_THICK);
                        
                            $sheet->getStyle("A{$row}:Q{$row}")
                                ->getBorders()
                                ->getBottom()
                                ->setBorderStyle(Border::BORDER_THICK);
                        
                            // Right align amount columns
                            foreach (['M','N','O','P','Q','R','S','T','U','V'] as $col) {
                                $sheet->getStyle("{$col}{$row}")
                                    ->getAlignment()
                                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            }
                        }
                        
                    }    
    
                // BORDERS
                $sheet->getStyle("A7:Q{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
                
                    $sheet->getStyle("A8:Q{$highestRow}")
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
                    'M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC'
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
    