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
        Schema::create('employee_clock_in_out', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no');
            $table->string('clock_in')
                ->nullable();
            $table->string('clock_out')
                ->nullable();
            $table->string('captured_image_clockin')
                ->nullable();
            $table->string('captured_image_clockout')
                ->nullable();
            $table->string('captured_location_clockin')
                ->nullable();
            $table->string('captured_location_clockout')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_clock_in_out');
    }
};
