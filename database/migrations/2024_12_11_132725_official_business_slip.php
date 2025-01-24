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
            $table->enum('status', [
                'approved',
                'disapproved',
                'pending'
            ])->default('pending');

            $table->longText('remarks')
                ->nullable();
        
            $table->foreignId('action_by_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->boolean('isDeleted')
                ->default(false);

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
