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
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')
                ->constrained('payroll')
                ->onDelete('cascade');
            $table->string('employee_no')
                ->nullable();
            $table->string('employment_type')
                ->nullable();
            $table->string('name')
                ->nullable();
            $table->string('position')
                ->nullable();
            $table->string('basic_salary')
                ->nullable();
            $table->string('pera')
                ->nullable();
            $table->string('overtime')
                ->nullable();
            $table->string('gross_amount_earned')
                ->nullable();
            $table->string('rlip')
                ->nullable();
            $table->string('hdmf')
                ->nullable();
            $table->string('philhealth')
                ->nullable();
            $table->string('consoloan')
                ->nullable();
            $table->string('emergency_loan')
                ->nullable();
            $table->string('plreg')
                ->nullable();
            $table->string('mpl')
                ->nullable();
            $table->string('cpl')
                ->nullable();
            $table->string('mp2')
                ->nullable();
            $table->string('mplstlms')
                ->nullable();
            $table->string('cir375_cir449')
                ->nullable();
            $table->string('w_tax')
                ->nullable();
            $table->string('uca')
                ->nullable();
            $table->string('aut')
                ->nullable();
            $table->string('total_deductions')
                ->nullable();
            $table->string('net_amount')
                ->nullable();
            $table->string('dbp')
                ->nullable();
            $table->string('kawani')
                ->nullable();
            $table->string('lbp_payroll_account')
                ->nullable();
            $table->string('salary')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
