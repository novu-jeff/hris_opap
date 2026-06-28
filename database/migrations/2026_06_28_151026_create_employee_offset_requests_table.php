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
        Schema::create('employee_offset_requests', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('employee_information_id')
                ->constrained('employee_information')
                ->cascadeOnDelete();
        
            $table->string('office_order_no')->nullable();
        
            $table->date('filing_date');
        
            $table->date('date_from');
            $table->date('date_to');
        
            $table->decimal('hours_requested', 5, 2);
        
            $table->text('reason')->nullable();
        
            $table->enum('status', [
                'Pending',
                'Recommended',
                'Certified',
                'Approved',
                'Disapproved'
            ])->default('Pending');
        
            
            $table->text('remarks')->nullable();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_offset_requests');
    }
};