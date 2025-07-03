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

        Schema::create('payroll_salary', function (Blueprint $table) {
            $table->id();
            $table->uuid('batch_id')
                ->nullable();
            $table->date('payroll_date'); 
            $table->string('cut_off_period');
            $table->integer('employment_type');
            $table->string('status')
                ->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_salary');
    }
};
