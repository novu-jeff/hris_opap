<?php

namespace App\Services;

class Employee1601Service
{
    /**
     * Compute BIR 1601-C Part II: Tax Computation
     *
     * @param array $data - expects:
     *  [
     *      'total_salary' => float, # e.g. 525000.00
     *      'employee_count' => int, # e.g. 5
     *      'month' => string, # e.g. '2025-06'
     *  ]
     * @return array
     */
    public function compute(array $data): array
    {
        $grossTotal = $data['total_salary'];
        $employeeCount = $data['employee_count'];
        $month = $data['month'] ?? now()->format('Y-m');
        $nonTaxableMonthlyLimit = 250000 / 12; # P250,000 annual exemption
        $flatTaxRate = 0.10; # Simplified rate

        # Computations
        $nonTaxable = min($grossTotal, $nonTaxableMonthlyLimit * $employeeCount);
        $taxable = max(0, $grossTotal - $nonTaxable);
        $withheld = $taxable * $flatTaxRate;

        return [
            'form' => 'BIR Form 1601-C',
            'month' => $month,

            # PART II - COMPUTATION OF TAX
            14 => number_format($grossTotal, 2),        # Total Amount of Compensation
            15 => number_format($nonTaxable, 2),        # Less: Non-Taxable Compensation (e.g. MWE)
            16 => '0.00',                                # Statutory Minimum Wage (if any)
            17 => '0.00',                                # Holiday Pay, OT, etc. (MWE)
            18 => '0.00',                                # 13th Month Pay and Other Benefits
            19 => '0.00',                                # De Minimis Benefits
            20 => '0.00',                                # SSS/GSIS/etc.
            21 => number_format($nonTaxable, 2),        # Total Non-Taxable Compensation (sum of 15–20)
            22 => number_format($taxable, 2),           # Taxable Compensation (14 – 21)
            23 => number_format($taxable, 2),           # Taxable compensation not adjusted
            24 => number_format($taxable, 2),           # Net Taxable Compensation (simplified same)
            25 => number_format($withheld, 2),          # Total Tax Required to be Withheld
            26 => '0.00',                                # Adjustments
            27 => '0.00',                                # Additions/Adjustments from previous
            28 => number_format($withheld, 2),          # Total Tax Withheld for the Month
            29 => '0.00',                                # Less: Remitted (if amending)
            30 => number_format($withheld, 2),          # Total Remittance Due
            31 => '0.00',                                # Remitted in advance
            32 => '0.00',                                # Penalty: Surcharge
            33 => '0.00',                                # Penalty: Interest
            34 => '0.00',                                # Penalty: Compromise
            35 => number_format($withheld, 2),          # Total Amount Still Due

            # PART III - PAYMENT DETAILS (blank by default)
            36 => [
                'Cash/Bank Debit Memo' => '',
                'Bank Name/Agency' => '',
                'Number' => '',
                'Date' => '',
                'Amount' => '',
            ],
            37 => [
                'Check' => '',
            ],
            38 => [
                'Tax Debit Memo' => '',
            ],
            39 => [
                'Others' => '',
            ],
        ];
    }

    /**
     * Generate a summary report for BIR 1601C payroll view.
     *
     * @param array $data  Keys expected:
     *   - gross_pay (float)
     *   - non_taxable_other_income (float)
     *   - sss (float)
     *   - philhealth (float)
     *   - pagibig (float)
     *   - wtax_1601c (float)
     *   - expanded_wtax (float)
     *   - period (string)
     *
     * @return array
     */
    public function generateReport(array $data): array
    {
        $grossPay = (float) str_replace(',', '', $data['gross_pay']);
        $nonTaxableOtherIncome = (float) str_replace(',', '', $data['non_taxable_other_income']);
        $amountOfCompensation = $grossPay - $nonTaxableOtherIncome;

        $sss = (float) str_replace(',', '', $data['sss']);
        $philhealth = (float) str_replace(',', '', $data['philhealth']);
        $pagibig = (float) str_replace(',', '', $data['pagibig']);
        $nonTaxableDeductions = $sss + $philhealth + $pagibig;

        $netCompensation = $amountOfCompensation - $nonTaxableDeductions;

        $wtax1601c = (float) str_replace(',', '', $data['wtax_1601c']);
        $expandedWtax = (float) str_replace(',', '', $data['expanded_wtax']);
        $totalWtax = $wtax1601c + $expandedWtax;

        return [
            'period' => $data['period'],
            'gross_pay' => number_format($grossPay, 2),
            'non_taxable_income' => number_format($nonTaxableOtherIncome, 2),
            'amount_of_compensation' => number_format($amountOfCompensation, 2),
            'deductions' => [
                'SSS' => number_format($sss, 2),
                'PhilHealth' => number_format($philhealth, 2),
                'Pag-IBIG' => number_format($pagibig, 2),
                'Total' => number_format($nonTaxableDeductions, 2),
            ],
            'net_compensation' => number_format($netCompensation, 2),
            'withholding_tax' => [
                'WTAX_1601C' => number_format($wtax1601c, 2),
                'Expanded_WTAX' => number_format($expandedWtax, 2),
                'Total' => number_format($totalWtax, 2),
            ],
        ];
    }
}
