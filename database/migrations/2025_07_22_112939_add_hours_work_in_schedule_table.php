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
        Schema::table('shift_schedule', function (Blueprint $table) {
            $table->decimal('work_hours', 5, 2)->nullable()->after('end_shift')->default(8);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_schedule', function (Blueprint $table) {
            $table->dropColumn('work_hours');
        });
    }
};
