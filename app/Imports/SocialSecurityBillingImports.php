<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class SocialSecurityBillingImports implements ToArray
{
    public function array(array $array)
    {
        return $array;
    }
}
