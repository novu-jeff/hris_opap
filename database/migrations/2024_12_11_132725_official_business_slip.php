<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_business_slips', function (Blueprint $table) {
            $table->id();

            $table->string('employee_no');        
            $table->date('date_filed')->nullable();
            
            $table->string('destination')->nullable();
            $table->string('purpose')->nullable();
            
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();

            $table->string('requested_by')->nullable();

            $table->string('status')
                ->default('pending');
        
            $table->foreignId('approved_by_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_business_slips');
        
    }
};
