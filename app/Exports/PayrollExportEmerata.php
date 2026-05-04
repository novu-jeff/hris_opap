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
    
        foreach ($items as $item) {
            $rows->push([
                $counter,                    // A
                strtoupper($item->name),     // B
                '', '', '', '', '',          // C-G filler
            
                strtoupper($item->position), // H
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
                $footer = $highestRow + 4;
    
                $sheet->mergeCells("A{$footer}:H{$footer}");
                $sheet->setCellValue("A{$footer}", 'A  PREPARED BY:');
    
                $sheet->mergeCells("I{$footer}:P{$footer}");
                $sheet->setCellValue("I{$footer}", 'B  CERTIFIED:');
    
                $sheet->mergeCells("Q{$footer}:Z{$footer}");
                $sheet->setCellValue("Q{$footer}", 'C  APPROVED FOR PAYMENT:');
    
                $sheet->mergeCells("A" . ($footer + 2) . ":H" . ($footer + 2));
                $sheet->setCellValue("A" . ($footer + 2), $this->preparedByName ?: 'Prepared By');
    
                $sheet->mergeCells("I" . ($footer + 2) . ":P" . ($footer + 2));
                $sheet->setCellValue("I" . ($footer + 2), $this->certifiedByName ?: 'Certified By');
    
                $sheet->mergeCells("Q" . ($footer + 2) . ":Z" . ($footer + 2));
                $sheet->setCellValue("Q" . ($footer + 2), 'PA ARNUFO R. PAJARILLO');
    
                $sheet->getStyle("A{$footer}:Z" . ($footer + 3))
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
                
                       
    
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
    