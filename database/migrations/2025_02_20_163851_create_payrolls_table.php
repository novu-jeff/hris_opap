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
        Schema::create('payroll', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no');
            $table->string('payroll_date');
            $table->string('cut_off_period');
            $table->string('position');
            $table->string('salary_grade');
            $table->float('basic_salary');
            $table->float('gross_amount_earned');
            $table->float('total_deductions');
            $table->float('net_amount');
            $table->timestamps();
        });

        Schema::create('payroll_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')
                ->constrained('payroll')
                ->onDelete('cascade');
            $table->string('name');
            $table->float('amount');
        });

        Schema::create('payroll_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')
                ->constrained('payroll')
                ->onDelete('cascade');
            $table->string('name');
            $table->float('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll');
    }
};
