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
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description');
            
            $table->boolean('monday')->default(false);
            $table->tinyText('monday_remarks')->nullable();
            
            $table->boolean('tuesday')->default(false);
            $table->tinyText('tuesday_remarks')->nullable();
            
            $table->boolean('wednesday')->default(false);
            $table->tinyText('wednesday_remarks')->nullable();
            
            $table->boolean('thursday')->default(false);
            $table->tinyText('thursday_remarks')->nullable();
            
            $table->boolean('friday')->default(false);
            $table->tinyText('friday_remarks')->nullable();
            
            $table->boolean('saturday')->default(false);
            $table->tinyText('saturday_remarks')->nullable();
            
            $table->boolean('sunday')->default(false);
            $table->tinyText('sunday_remarks')->nullable();
            
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_schedules');
    }
};
