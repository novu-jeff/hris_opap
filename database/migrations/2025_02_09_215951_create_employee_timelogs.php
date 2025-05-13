<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_timelogs', function (Blueprint $table) {
            $table->id(); 
            $table->string('origin')
                ->nullable();
            $table->string('employee_id')
                ->nullable();
            $table->string('clock_in')
                ->nullable();
            $table->string('lunch_in')
                ->nullable();
            $table->string('lunch_out')
                ->nullable();
            $table->string('clock_out')
                ->nullable();
            $table->string('tardiness')
                ->nullable();
            $table->string('undertime')
                ->nullable();
            $table->string('undertime_reason')
                ->nullable();
            $table->string('early_lunch_in')
                ->nullable();
            $table->string('early_lunch_in_reason')
                ->nullable();
            $table->string('overtime')
                ->nullable();
            $table->string('overtime_reason')
                ->nullable();
            $table->string('captured_image')
                ->nullable();
            $table->string('captured_location')
                ->nullable();
            $table->longText('accomplishment')
                ->nullable();
            $table->boolean('isComputed')
                ->default(false)
                ->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_timelogs');
    }
};
