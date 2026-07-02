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
        Schema::create('employee_offset_credit_usages', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('employee_offset_request_id')
                ->constrained()
                ->cascadeOnDelete();
        
            $table->foreignId('employee_offset_credit_id')
                ->constrained()
                ->cascadeOnDelete();
        
            $table->decimal('hours_used', 5, 2);
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_offset_credit_usages');
    }
};
