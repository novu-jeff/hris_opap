<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_salary_items', function (Blueprint $table) {
            $table->decimal('aut_month1', 15, 2)
                ->default(0)
                ->after('aut');

            $table->decimal('aut_month2', 15, 2)
                ->default(0)
                ->after('aut_month1');

            $table->decimal('aut_month3', 15, 2)
                ->default(0)
                ->after('aut_month2');

            $table->decimal('aut_total', 15, 2)
                ->default(0)
                ->after('aut_month3');
        });

        // Optional: initialize existing records
        DB::table('payroll_salary_items')->update([
            'aut_month1' => DB::raw('COALESCE(aut,0)'),
            'aut_month2' => 0,
            'aut_month3' => 0,
            'aut_total'  => DB::raw('COALESCE(aut,0)')
        ]);
    }

    public function down(): void
    {
        Schema::table('payroll_salary_items', function (Blueprint $table) {
            $table->dropColumn([
                'aut_month1',
                'aut_month2',
                'aut_month3',
                'aut_total'
            ]);
        });
    }
};