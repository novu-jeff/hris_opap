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
        Schema::create('employee_aut', function (Blueprint $table) {
            $table->id();
            $table->string('date')
                ->nullable();
            $table->string('bsd_no')
                ->nullable();
            $table->string('absences')
                ->nullable();
            $table->string('undertime')
                ->nullable();
            $table->string('lates')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_aut');
    }
};
