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
            $table->string('clock_in_am')
                ->nullable();
            $table->string('clock_out_am')
                ->nullable();
            $table->string('mins_consumed_am')
                ->nullable();
            $table->string('clock_in_pm')
                ->nullable();
            $table->string('clock_out_pm')
                ->nullable();
            $table->string('mins_consumed_pm')
                ->nullable();
            $table->string('captured_image_clockin')
                ->nullable();
            $table->string('captured_image_clockout')
                ->nullable();
            $table->string('captured_location_clockin')
                ->nullable();
            $table->string('captured_location_clockout')
                ->nullable();
            $table->boolean('isLate')
                ->default(false);
            $table->boolean('isHalfDay')
                ->default(false);
            $table->boolean('isUnderTime')
                ->default(false);
            $table->string('total_mins_consumed')
                ->nullable();
            $table->string('mins_ot')
                ->nullable();
            $table->string('overall_mins')
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
