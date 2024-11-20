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
        Schema::create('other_deductions', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->enum('frequency', [
                'bi_monthly',
                'monthly'
            ]);
            $table->string('month_frequency')
                ->nullable();
            $table->foreignId('eligible')
                ->constrained('job_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_deductions');
    }
};
