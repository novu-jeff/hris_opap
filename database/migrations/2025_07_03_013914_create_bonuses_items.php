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
        Schema::create('payroll_bonuses_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')
                ->constrained('payroll_bonuses')
                ->onDelete('cascade');
            $table->string('employee_no');
            $table->string('name');
            $table->string('position');
            $table->decimal('basic_salary', 12, 2);
            $table->decimal('bonus', 12, 2);
            $table->decimal('cash_gift', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_bonuses_items');
    }
};
