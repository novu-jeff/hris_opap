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
        Schema::create('employee_offset_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_offset_request_id')
                ->constrained('employee_offset_requests')
                ->cascadeOnDelete();

            $table->string('attachment');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_offset_attachments');
    }
};