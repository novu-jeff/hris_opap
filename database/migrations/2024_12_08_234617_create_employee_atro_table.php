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
        Schema::create('employee_atro', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no');
            $table->string('date');
            $table->string('start_time');
            $table->string('end_time');
            $table->longText('justification');
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
        Schema::dropIfExists('employee_atro');
    }
};
