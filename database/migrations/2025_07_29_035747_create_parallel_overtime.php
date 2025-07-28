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
        Schema::create('parallel_overtime', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no');
            $table->string('total_overtime')
                ->default('0');
            $table->string('total_overtime_hrs')
                ->default('0');
            $table->string('total_night_diff_amount')
                ->default('0');
            $table->string('total_night_diff_hrs')
                ->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parallel_overtime');
    }
};
