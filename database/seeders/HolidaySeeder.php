<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidaySeeder extends Seeder
{
    public function run()
    {
        $holidays = [
            // Regular Holidays
            ['name' => "New Year’s Day", 'type' => "regular", 'date' => "01-01", 'isActive' => true, 'isYearly' => true],
            ['name' => "Araw ng Kagitingan", 'type' => "regular", 'date' => "04-09", 'isActive' => true, 'isYearly' => true],
            ['name' => "Maundy Thursday", 'type' => "regular", 'date' => "04-17", 'isActive' => true, 'isYearly' => false],
            ['name' => "Good Friday", 'type' => "regular", 'date' => "04-18", 'isActive' => true, 'isYearly' => false],
            ['name' => "Labor Day", 'type' => "regular", 'date' => "05-01", 'isActive' => true, 'isYearly' => true],
            ['name' => "Independence Day", 'type' => "regular", 'date' => "06-12", 'isActive' => true, 'isYearly' => true],
            ['name' => "National Heroes Day", 'type' => "regular", 'date' => "08-25", 'isActive' => true, 'isYearly' => false],
            ['name' => "Bonifacio Day", 'type' => "regular", 'date' => "11-30", 'isActive' => true, 'isYearly' => true],
            ['name' => "Christmas Day", 'type' => "regular", 'date' => "12-25", 'isActive' => true, 'isYearly' => true],
            ['name' => "Rizal Day", 'type' => "regular", 'date' => "12-30", 'isActive' => true, 'isYearly' => true],
            
            // Special Non-Working Days
            ['name' => "Ninoy Aquino Day", 'type' => "special-non-working", 'date' => "08-21", 'isActive' => true, 'isYearly' => true],
            ['name' => "All Saints Day", 'type' => "special-non-working", 'date' => "11-01", 'isActive' => true, 'isYearly' => true],
            ['name' => "Feast of the Immaculate Conception of Mary", 'type' => "special-non-working", 'date' => "12-08", 'isActive' => true, 'isYearly' => true],
            ['name' => "Last Day of the Year", 'type' => "special-non-working", 'date' => "12-31", 'isActive' => true, 'isYearly' => true],
            
            // Special Working Day
            ['name' => "EDSA People Power Revolution Anniversary", 'type' => "special-working", 'date' => "02-25", 'isActive' => true, 'isYearly' => true],
            
            // Additional Special (Non-Working) Days
            ['name' => "Chinese New Year", 'type' => "special-non-working", 'date' => "01-29", 'isActive' => true, 'isYearly' => false],
            ['name' => "Black Saturday", 'type' => "special-non-working", 'date' => "04-19", 'isActive' => true, 'isYearly' => false],
            ['name' => "Christmas Eve", 'type' => "special-non-working", 'date' => "12-24", 'isActive' => true, 'isYearly' => true],
            ['name' => "All Saints' Day Eve", 'type' => "special-non-working", 'date' => "10-31", 'isActive' => true, 'isYearly' => true],
        ];
        
        foreach ($holidays as $holiday) {
            DB::table('holidays')->updateOrInsert(
                ['name' => $holiday['name'], 'date' => $holiday['date']],
                $holiday
            );
        }
    }
}
