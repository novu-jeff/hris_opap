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
        Schema::create('employee_leave_dates', function(Blueprint $table) {
            $table->id();
            $table->foreignId('employee_leave_id')
                ->nullable()
                ->constrained('employee_leave')
                ->onDelete('set null');
            $table->string('employee_no');
            $table->string('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leave_dates');
    }
};
