<?php

namespace App\Exports;

use App\Models\BonusPayroll;
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

class PayrollExportPremium implements FromCollection, WithEvents
{
    protected $payroll;
    protected $filterSalaryMethod;
    protected $searchName;
    protected $supervisingOfficer;
    protected $preparedByName;
    protected $preparedByPosition;
    protected $certifiedByName;
    protected $certifiedByPosition;

    public function __construct(BonusPayroll $payroll, $filterSalaryMethod = '', $searchName = '', $supervisingOfficer = null, $chiefadministrativeOfficer = null)
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
                | SEMESTER MONTHS
                |--------------------------------------------------------------------------
                */

                $monthlyColumns = [];

                /*
                |--------------------------------------------------------------------------
                | FIRST SEM
                |--------------------------------------------------------------------------
                */

                if (
                    $this->payroll->semester
                    == 'first_semester'
                ) {

                    $monthlyColumns = [

                        $item->january_amount ?? 0,
                        $item->february_amount ?? 0,
                        $item->march_amount ?? 0,
                        $item->april_amount ?? 0,
                        $item->may_amount ?? 0,
                        $item->june_amount ?? 0,

                    ];

                }

                /*
                |--------------------------------------------------------------------------
                | SECOND SEM
                |--------------------------------------------------------------------------
                */

                if (
                    $this->payroll->semester
                    == 'second_semester'
                ) {

                    $monthlyColumns = [

                        $item->july_amount ?? 0,
                        $item->august_amount ?? 0,
                        $item->september_amount ?? 0,
                        $item->october_amount ?? 0,
                        $item->november_amount ?? 0,
                        $item->december_amount ?? 0,

                    ];

                }

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

                    $item->basic_salary,

                    ...$monthlyColumns,

                    $item->total_amount ?? 0,

                    $item->tax ?? 0,

                    $item->net_amount ?? 0,

                ]);

                $counter++;
            }

            /*
            |--------------------------------------------------------------------------
            | SECTION SUBTOTALS
            |--------------------------------------------------------------------------
            */

            $sectionMonthlyTotals = [];

            /*
            |--------------------------------------------------------------------------
            | FIRST SEM
            |--------------------------------------------------------------------------
            */

            if (
                $this->payroll->semester
                == 'first_semester'
            ) {

                $sectionMonthlyTotals = [

                    $employees->sum('january_amount'),
                    $employees->sum('february_amount'),
                    $employees->sum('march_amount'),
                    $employees->sum('april_amount'),
                    $employees->sum('may_amount'),
                    $employees->sum('june_amount'),

                ];

            }

            /*
            |--------------------------------------------------------------------------
            | SECOND SEM
            |--------------------------------------------------------------------------
            */

            if (
                $this->payroll->semester
                == 'second_semester'
            ) {

                $sectionMonthlyTotals = [

                    $employees->sum('july_amount'),
                    $employees->sum('august_amount'),
                    $employees->sum('september_amount'),
                    $employees->sum('october_amount'),
                    $employees->sum('november_amount'),
                    $employees->sum('december_amount'),

                ];

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

                '', '', '', '', '', '', '', '', '', $employees->sum('basic_salary'),

                ...$sectionMonthlyTotals,

                $employees->sum('total_amount'),

                $employees->sum('tax'),

                $employees->sum('net_amount'),

            ]);

        }
    }

    /*
    |--------------------------------------------------------------------------
    | GRAND TOTALS
    |--------------------------------------------------------------------------
    */

    $grandMonthlyTotals = [];

    /*
    |--------------------------------------------------------------------------
    | FIRST SEM
    |--------------------------------------------------------------------------
    */

    if (
        $this->payroll->semester
        == 'first_semester'
    ) {

        $grandMonthlyTotals = [

            $items->sum('january_amount'),
            $items->sum('february_amount'),
            $items->sum('march_amount'),
            $items->sum('april_amount'),
            $items->sum('may_amount'),
            $items->sum('june_amount'),

        ];

    }

    /*
    |--------------------------------------------------------------------------
    | SECOND SEM
    |--------------------------------------------------------------------------
    */

    if (
        $this->payroll->semester
        == 'second_semester'
    ) {

        $grandMonthlyTotals = [

            $items->sum('july_amount'),
            $items->sum('august_amount'),
            $items->sum('september_amount'),
            $items->sum('october_amount'),
            $items->sum('november_amount'),
            $items->sum('december_amount'),

        ];

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

        '', '', '', '', '', '', '', '', '', $items->sum('basic_salary'),

        ...$grandMonthlyTotals,

        $items->sum('total_amount'),

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
                    'PREMIUM PAYROLL - ' .
                    $semesterLabel .
                    ' ' .
                    strtoupper(
                        Carbon::parse(
                            $this->payroll->coverage_from
                        )->format('Y')
                    )
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
                    'M7' => 'BASIC SALARY',
                
                ];
                
                /*
                |--------------------------------------------------------------------------
                | FIRST SEM
                |--------------------------------------------------------------------------
                */
                
                if ($this->payroll->semester == 'first_semester') {
                
                    $headers['N7'] = 'JAN';
                    $headers['O7'] = 'FEB';
                    $headers['P7'] = 'MAR';
                    $headers['Q7'] = 'APR';
                    $headers['R7'] = 'MAY';
                    $headers['S7'] = 'JUN';
                
                }
                
                /*
                |--------------------------------------------------------------------------
                | SECOND SEM
                |--------------------------------------------------------------------------
                */
                
                if ($this->payroll->semester == 'second_semester') {
                
                    $headers['N7'] = 'JUL';
                    $headers['O7'] = 'AUG';
                    $headers['P7'] = 'SEP';
                    $headers['Q7'] = 'OCT';
                    $headers['R7'] = 'NOV';
                    $headers['S7'] = 'DEC';
                
                }
                
                $headers['T7'] = 'TOTAL';
                $headers['U7'] = 'TAX';
                $headers['V7'] = 'NET';
    
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
        
                            $sheet->mergeCells("A{$row}:V{$row}");
        
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
                            $sheet->mergeCells("A{$row}:V{$row}");
        
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
                            $sheet->getStyle("A{$row}:V{$row}")
                                ->getFont()
                                ->setBold(true)
                                ->setSize(11);
                        
                            // Yellow highlight fill
                            $sheet->getStyle("A{$row}:V{$row}")
                                ->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setRGB('FFF2CC'); // light yellow
                        
                            // Thick top + bottom border
                            $sheet->getStyle("A{$row}:V{$row}")
                                ->getBorders()
                                ->getTop()
                                ->setBorderStyle(Border::BORDER_THICK);
                        
                            $sheet->getStyle("A{$row}:V{$row}")
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
                $sheet->getStyle("A7:V{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
                
                    $sheet->getStyle("A8:V{$highestRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);  
                    
                    foreach (['N', 'Q', 'U'] as $col) {
                        $sheet->getStyle("{$col}8:{$col}{$highestRow}")
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
    
                 // FOOTER BOXES
            
$footer = $highestRow + 4;

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
$sheet->mergeCells("I{$footer}:M{$footer}");
$sheet->setCellValue(
"I{$footer}",
'CERTIFIED: Services duly rendered as stated.'
);

// C
$sheet->mergeCells("N{$footer}:T{$footer}");
$sheet->setCellValue(
"N{$footer}",
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
$sheet->setCellValue(
"A{$signameRow}",
'________________________________'
);

$sheet->mergeCells("I{$signameRow}:M{$signameRow}");
$sheet->setCellValue(
"I{$signameRow}",
'________________________________'
);

$sheet->mergeCells("N{$signameRow}:T{$signameRow}");
$sheet->setCellValue(
"N{$signameRow}",
'________________________________'
);

$sheet->mergeCells("A{$signameRow}:H{$signameRow}");
$sheet->mergeCells("A{$nameRow}:H{$nameRow}");
$sheet->setCellValue(
"A{$nameRow}",
strtoupper($this->preparedByName ?: 'BEA MILAN A. CLERIGO')
);

$sheet->mergeCells("I{$signameRow}:M{$signameRow}");
$sheet->mergeCells("I{$nameRow}:M{$nameRow}");
$sheet->setCellValue(
"I{$nameRow}",
strtoupper($this->certifiedByName ?: 'DIR. FRANCISCO F. MENDOZA, JR.')
);

$sheet->mergeCells("N{$signameRow}:T{$signameRow}");
$sheet->mergeCells("N{$nameRow}:T{$nameRow}");
$sheet->setCellValue(
"N{$nameRow}",
'PA ARNUFO R. PAJARILLO, JR.'
);

/*
==================================================
POSITIONS + DATE
==================================================
*/

$positionRow = $nameRow + 1;

$sheet->mergeCells("A{$positionRow}:H{$positionRow}");


$sheet->setCellValue(
"A{$positionRow}",
strtoupper($this->preparedByPosition ?: 'SAO-HRMS')
);


$sheet->setCellValue("G{$positionRow}", 'DATE');

$sheet->mergeCells("I{$positionRow}:M{$positionRow}");
$sheet->setCellValue(
"I{$positionRow}",
strtoupper($this->certifiedByPosition ?: 'DIRECTOR IV - HRMS')
);

$sheet->mergeCells("N{$positionRow}:T{$positionRow}");
$sheet->setCellValue(
"N{$positionRow}",
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
$sheet->mergeCells("A{$lower}:M{$lower}");
$sheet->setCellValue(
"A{$lower}",
'B  CERTIFIED: Supporting documents complete and proper; and cash available in the amount of'
);

/* D */
$sheet->mergeCells("N{$lower}:T{$lower}");
$sheet->setCellValue(
"N{$lower}",
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

$sheet->mergeCells("A{$sigbottomNameRow}:G{$sigbottomNameRow}");
$sheet->mergeCells("A{$bottomNameRow}:G{$bottomNameRow}");
$sheet->setCellValue(
"A{$bottomNameRow}",
'JENNIE CLAIRE L. MORDENO'
);

$sheet->mergeCells("N{$sigbottomNameRow}:T{$sigbottomNameRow}");
$sheet->mergeCells("N{$bottomNameRow}:T{$bottomNameRow}");
$sheet->setCellValue(
"N{$bottomNameRow}",
'ALEX C. ORENDAIN'
);

/*
==================================================
BOTTOM POSITIONS
==================================================
*/

$bottomPositionRow = $bottomNameRow + 1;

$sheet->mergeCells("A{$bottomPositionRow}:E{$bottomPositionRow}");
$sheet->setCellValue(
"A{$bottomPositionRow}",
'Officer-in-Charge, Director IV-FMS'
);

$sheet->mergeCells("F{$bottomPositionRow}:G{$bottomPositionRow}");
$sheet->setCellValue(
"F{$bottomPositionRow}",
'Date'
);

$sheet->mergeCells("N{$bottomPositionRow}:T{$bottomPositionRow}");
$sheet->setCellValue(
"N{$bottomPositionRow}",
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


$sheet->getStyle("A{$nameRow}:T{$nameRow}")
->getFont()
->setBold(true)
->setSize(11);

$sheet->getStyle("A{$bottomNameRow}:T{$bottomNameRow}")
->getFont()
->setBold(true)
->setSize(11);

$sheet->getStyle("A{$footer}:T" . ($bottomPositionRow))
->getAlignment()
->setHorizontal(Alignment::HORIZONTAL_CENTER)
->setVertical(Alignment::VERTICAL_CENTER);


$sheet->getStyle("A{$footer}:T{$footer}")
->applyFromArray([
'font' => [
    'bold' => true,
    'size' => 10,
],
'fill' => [
    'fillType' => Fill::FILL_SOLID,
    'startColor' => [
        'rgb' => 'D9D9D9'
    ]
]
]);
/*
==================================================
BORDERS
==================================================
*/

$sheet->getStyle("A{$footer}:T" . ($lower + 5))
->getBorders()
->getAllBorders()
->setBorderStyle(Border::BORDER_THIN);

$sheet->getStyle("A{$footer}:T" . ($lower + 5))
->getAlignment()
->setVertical(Alignment::VERTICAL_CENTER);
            

            $moneyColumns = [
                'L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC'
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
