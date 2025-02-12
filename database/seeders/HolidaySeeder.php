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
            ['name' => "New Year’s Day", 'type' => "regular", 'date' => "01-01", 'isDeleted' => false],
            ['name' => "Araw ng Kagitingan", 'type' => "regular", 'date' => "04-09", 'isDeleted' => false],
            ['name' => "Maundy Thursday", 'type' => "regular", 'date' => "04-17", 'isDeleted' => false],
            ['name' => "Good Friday", 'type' => "regular", 'date' => "04-18", 'isDeleted' => false],
            ['name' => "Labor Day", 'type' => "regular", 'date' => "05-01", 'isDeleted' => false],
            ['name' => "Independence Day", 'type' => "regular", 'date' => "06-12", 'isDeleted' => false],
            ['name' => "National Heroes Day", 'type' => "regular", 'date' => "08-25", 'isDeleted' => false],
            ['name' => "Bonifacio Day", 'type' => "regular", 'date' => "11-30", 'isDeleted' => false],
            ['name' => "Christmas Day", 'type' => "regular", 'date' => "12-25", 'isDeleted' => false],
            ['name' => "Rizal Day", 'type' => "regular", 'date' => "12-30", 'isDeleted' => false],
            
            // Special Non-Working Days
            ['name' => "Ninoy Aquino Day", 'type' => "special-non-working", 'date' => "08-21", 'isDeleted' => false],
            ['name' => "All Saints Day", 'type' => "special-non-working", 'date' => "11-01", 'isDeleted' => false],
            ['name' => "Feast of the Immaculate Conception of Mary", 'type' => "special-non-working", 'date' => "12-08", 'isDeleted' => false],
            ['name' => "Last Day of the Year", 'type' => "special-non-working", 'date' => "12-31", 'isDeleted' => false],
            
            // Special Working Day
            ['name' => "EDSA People Power Revolution Anniversary", 'type' => "special-working", 'date' => "02-25", 'isDeleted' => false],
            
            // Additional Special (Non-Working) Days
            ['name' => "Chinese New Year", 'type' => "special-non-working", 'date' => "01-29", 'isDeleted' => false],
            ['name' => "Black Saturday", 'type' => "special-non-working", 'date' => "04-19", 'isDeleted' => false],
            ['name' => "Christmas Eve", 'type' => "special-non-working", 'date' => "12-24", 'isDeleted' => false],
            ['name' => "All Saints' Day Eve", 'type' => "special-non-working", 'date' => "10-31", 'isDeleted' => false],
        ];
        
        foreach ($holidays as $holiday) {
            DB::table('holidays')->updateOrInsert(
                ['name' => $holiday['name'], 'date' => $holiday['date']],
                $holiday
            );
        }
    }
}
