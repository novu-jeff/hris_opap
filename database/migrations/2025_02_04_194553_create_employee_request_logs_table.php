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
        Schema::create('employee_request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no');
            $table->string('date');
            $table->string('clock_in');
            $table->string('break_out');
            $table->string('break_in');
            $table->string('clock_out');
            $table->longText('reason');
            $table->enum('status', [
                'approved',
                'disapproved',
                'pending'
            ])->default('pending');
            $table->foreignId('action_by_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->boolean('isDeleted')
                ->default(false);
            $table->timestamps();
        });

        Schema::create('employee_request_logs_attachments', function(Blueprint $table) {
            $table->id();
            $table->foreignId('employee_requests_id')
                ->constrained('employee_request_logs')
                ->onDelete('cascade');
            $table->string('attachment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_request_logs_attachments');
        Schema::dropIfExists('employee_request_logs');
    }
};
