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
        Schema::create('employee_leave_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_leave_id')
                ->references('id')
                ->on('employee_leave')
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
        Schema::dropIfExists('employee_leave_attachments');
    }
};