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
        Schema::create('employee_business_slip_attachments', function (Blueprint $table) {
            $table->id();
        
            $table->unsignedBigInteger('employee_business_slip_id');
        
            $table->string('attachment');
        
            $table->timestamps();
        
            $table->foreign('employee_business_slip_id', 'obs_attach_fk')
                ->references('id')
                ->on('employee_business_slips') // <-- Correct table name
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_business_slip_attachments');
    }
};