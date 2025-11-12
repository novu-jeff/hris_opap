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
        Schema::create('payroll_clothing_allowance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')
                ->constrained('payroll_clothing_allowance')
                ->onDelete('cascade');
            $table->string('employee_no');
            $table->string('name');
            $table->string('position');
            $table->decimal('allowance', 12, 2)->default(0);
            $table->string('date_hired');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_clothing_allowance_items');
    }
};
