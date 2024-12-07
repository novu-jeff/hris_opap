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
            $table->string('mobile_earliest_clockin')
                ->nullable();
            $table->string('mobile_latest_clockin')
                ->nullable();
            $table->string('web_earliest_clockin')
                ->nullable();
            $table->string('web_latest_clockin')
                ->nullable();
            $table->string('break_from')
                ->nullable();
            $table->string('break_to')
                ->nullable();
            $table->string('min_ot_mins')
                ->nullable();
            $table->string('max_ot_time')
                ->nullable();
            $table->boolean('is_late_strict')
                ->nullable()
                ->default(false);
            $table->boolean('is_strict_undertime')
                ->nullable()
                ->default(false);
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
