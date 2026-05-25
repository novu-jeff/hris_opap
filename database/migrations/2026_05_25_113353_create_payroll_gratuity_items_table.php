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
        Schema::create('payroll_gratuity_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('payroll_id')->nullable();

            $table->string('employee_no')->nullable();

            $table->unsignedBigInteger('employment_type_id')->nullable();

            $table->string('name')->nullable();

            $table->string('position')->nullable();

            $table->decimal('basic_salary', 12, 2)->default(0);

            $table->text('remarks')->nullable();

            $table->date('date_hired')->nullable();

            $table->decimal('gratuity_pay', 12, 2)->default(0);

            $table->decimal('tax', 12, 2)->default(0);

            $table->decimal('net_amount', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_gratuity_items');
    }
};
