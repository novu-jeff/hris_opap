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

        Schema::create('shift_schedule', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description');
            $table->string('shift_duration');
            $table->string('earliest_in')
                ->nullable();
            $table->string('latest_in')
                ->nullable();
            $table->string('start_shift')
                ->nullable();
            $table->string('break_out');
            $table->string('break_in');
            $table->string('end_shift')
                ->nullable();
            $table->string('work_setup');
            $table->string('min_ot_mins')
                ->nullable()
                ->default(0);
            $table->string('max_ot_time')
                ->nullable();
            $table->string('mobile_earliest_clockin')
                ->nullable();
            $table->string('mobile_latest_clockin')
                ->nullable();
            $table->string('web_earliest_clockin')
                ->nullable();
            $table->string('web_latest_clockin')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_schedule');
    }
};
