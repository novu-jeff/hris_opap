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
        Schema::create('payroll', function (Blueprint $table) {
            $table->id();
            $table->uuid('batch_id')->nullable();
            $table->string('type');
            $table->string('payroll_date');
            $table->string('cut_off_period');
            $table->string('employment_type');
            $table->json('saved_payroll')
                ->nullable();
            $table->string('status')
                ->default('pending');
            $table->boolean('isUploadedGSIS')
                ->default(false);
            $table->boolean('isUploadedHDMF')
                ->default(false);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll');
    }
};
