<?php

namespace App\Services;

use DateTime;

class Employee2316Service
{
    public function compute(array $data): array
    {
        $year = $data['year'];

        $date_hired = new DateTime($data['date_hired']);
        $resigned_date = isset($data['resigned_date']) ? new DateTime($data['resigned_date']) : new DateTime("{$year}-12-31");

        # Ensure dates are within the year
        $from = max($date_hired, new DateTime("{$year}-01-01"));
        $to = min($resigned_date, new DateTime("{$year}-12-31"));

        # Calculate number of months worked (at least 1 full day counts as a month)
        $monthsWorked = ($from->diff($to)->m + 1) + ($from->diff($to)->y * 12);
        if ($monthsWorked > 12) $monthsWorked = 12;
        if ($monthsWorked < 1) $monthsWorked = 1;

        # Monthly and adjusted salary
        $monthlySalary = $data['salary'];
        $adjustedAnnualSalary = $monthlySalary * $monthsWorked;

        # Taxable computation
        $nonTaxableLimit = 250_000 * ($monthsWorked / 12);
        $nonTaxable = min($adjustedAnnualSalary, $nonTaxableLimit);
        $taxable = max(0, $adjustedAnnualSalary - $nonTaxableLimit);

        # Compute withholding tax
        $withheld = $this->computeWithholdingTax($adjustedAnnualSalary);

        return [
            1    => $year,
            '2_from'    => $from->format('Y-m-d'),
            '2_to'      => $to->format('Y-m-d'),
            3    => $data['tin'],
            4    => $data['name'],
            5    => '',
            6    => '',
            '6A' => '',
            '6B' => '',
            '6C' => '',
            '6D' => '',
            '6E' => '',
            7    => '',
            8    => '',
            9    => '',
            10   => number_format($monthlySalary, 2),
            11   => 'No',
            12   => '',
            13   => $data['company_name'],
            14   => $data['company_address'],
            '14A'=> '',
            15   => 'Main Employer',
            16   => '',
            17   => '',
            18   => '',
            '18A'=> '',
            19   => number_format($adjustedAnnualSalary, 2),
            20   => number_format($nonTaxable, 2),
            21   => number_format($taxable, 2),
            22   => '0.00',
            23   => number_format($taxable, 2),
            24   => number_format($withheld, 2),
            '25A'=> number_format($withheld, 2),
            '25B'=> '0.00',
            26   => number_format($withheld, 2),
            27   => '0.00',
            28   => number_format($withheld, 2),
            29   => number_format($nonTaxable, 2),
            30   => '0.00',
            31   => '0.00',
            32   => '0.00',
            33   => '0.00',
            34   => '0.00',
            35   => '0.00',
            36   => '0.00',
            37   => '0.00',
            38   => number_format($nonTaxable, 2),
            39   => number_format($taxable, 2),
            40   => '0.00',
            41   => '0.00',
            42   => '0.00',
            43   => '0.00',
            44   => '0.00',
            '44A'=> '',
            '44B'=> '',
            45   => '0.00',
            46   => '0.00',
            47   => '0.00',
            48   => '0.00',
            49   => '0.00',
            50   => '0.00',
            51   => '0.00',
            '51A'=> '',
            '51B'=> '',
            52   => number_format($taxable, 2),
            53   => 'Substituted Filing Confirmed',
            54   => 'Signed',
        ];
    }


    private function computeWithholdingTax(float $annualIncome): float
    {
        # TRAIN tax table for individual taxpayers
        switch (true) {
            case ($annualIncome <= 250000):
                return 0;

            case ($annualIncome <= 400000):
                return ($annualIncome - 250000) * 0.15;

            case ($annualIncome <= 800000):
                return 22500 + ($annualIncome - 400000) * 0.20;

            case ($annualIncome <= 2000000):
                return 102500 + ($annualIncome - 800000) * 0.25;

            case ($annualIncome <= 8000000):
                return 402500 + ($annualIncome - 2000000) * 0.30;

            default:
                return 2202500 + ($annualIncome - 8000000) * 0.35;
        }
    }

}
