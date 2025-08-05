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

        Schema::create('other_deductions', function (Blueprint $table) {
            $table->id();
            $table->string('code')
                ->nullable();
            $table->string('name');
            $table->string('amount')
                ->default('0');
            $table->timestamps();
        });

        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no');
            $table->foreignId('deduction_id')
                ->constrained('other_deductions')
                ->onDelete('cascade');
            $table->string('amount')    
                ->nullable()
                ->default('0');
            $table->string('valid_until');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_deductions');
        Schema::dropIfExists('other_deductions');
    }
};
