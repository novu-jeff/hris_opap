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
        $product = config('app.product');

        if($product == 'government') {
            Schema::create('payroll_overtime_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_id')
                    ->constrained('payroll_overtime')
                    ->onDelete('cascade');
                $table->string('employee_no');
                $table->string('name');
                $table->string('position');
                $table->string('basic_salary');
                $table->string('duration');
                $table->decimal('amount', 12, 2)->default(0);
                $table->decimal('tax', 12, 2)->default(0);
                $table->string('net_amount');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_overtime_items');
    }
};
