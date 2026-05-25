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
        Schema::create('payroll_gratuity', function (Blueprint $table) {
            $table->id();

            $table->uuid('batch_id')->nullable();

            $table->unsignedBigInteger('employment_type')->nullable();

            $table->date('payroll_date')->nullable();

            $table->text('remarks')->nullable();

            $table->json('selected_employees')->nullable();

            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_gratuity');
    }
};
