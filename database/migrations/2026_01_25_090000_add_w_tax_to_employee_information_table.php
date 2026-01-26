<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_information', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_information', 'w_tax')) {
                $table->decimal('w_tax', 12, 2)
                    ->nullable()
                    ->default(0)
                    ->after('salary');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_information', function (Blueprint $table) {
            if (Schema::hasColumn('employee_information', 'w_tax')) {
                $table->dropColumn('w_tax');
            }
        });
    }
};

