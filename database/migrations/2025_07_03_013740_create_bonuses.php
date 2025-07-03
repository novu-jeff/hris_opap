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
        Schema::create('payroll_bonuses', function (Blueprint $table) {
            $table->id();
            $table->uuid('batch_id')->nullable();
            $table->integer('employment_type');
            $table->enum('bonus_type', [
                'mid_year',
                'year_end'
            ]);
            $table->string('payroll_date');
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
        Schema::dropIfExists('payroll_bonuses');
    }
};
