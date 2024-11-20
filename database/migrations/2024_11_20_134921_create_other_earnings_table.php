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
        Schema::create('other_earnings', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->enum('amount_basis', [
                'entry',
                'basic_salary',
                'percentage',
            ]);
            $table->float('amount');
            $table->enum('frequency', [
                'monthly',
                'yearly',
                'month_picked'
            ]);
            $table->string('month_frequency')
                ->nullable();
            $table->foreignId('eligible')
                ->constrained('job_categories');
            $table->boolean('isTaxable')
                ->default(false);
            $table->float('forcasted')
                ->default(0)
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_earnings');
    }
};
