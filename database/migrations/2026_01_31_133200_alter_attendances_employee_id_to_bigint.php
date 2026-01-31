<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix: employee_id stores BSD numbers (e.g. 10111111111111) which exceed INT max (2147483647).
     * Run on mysql2 (external timelogs DB) when config app.external_timelogs is true.
     */
    public function up(): void
    {
        if (!config('app.external_timelogs')) {
            return;
        }

        DB::connection('mysql2')->statement('ALTER TABLE attendances MODIFY employee_id BIGINT UNSIGNED NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!config('app.external_timelogs')) {
            return;
        }

        DB::connection('mysql2')->statement('ALTER TABLE attendances MODIFY employee_id INT UNSIGNED NOT NULL');
    }
};
