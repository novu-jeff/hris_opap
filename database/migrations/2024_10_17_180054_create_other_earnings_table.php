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
            $table->string('code')
                ->nullable();
            $table->string('name');
            $table->enum('amount_type', [
                'fixed_amount',
                'percentage',
                'basic_salary'
            ])->default('fixed_amount');
            $table->string('first_term')
                ->nullable()
                ->default('0');
            $table->string('second_term')
                ->nullable()
                ->default('0');
            $table->boolean('isTaxable')
                ->default(false);
            $table->timestamps();
        });

        Schema::create('employee_earnings', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no');
            $table->foreignId('earning_id')
                ->constrained('other_earnings')
                ->onDelete('cascade');
            $table->enum('amount_type', [
                    'fixed_amount',
                    'percentage',
                    'basic_salary'
                ])->default('fixed_amount');
            $table->string('amount')
                ->nullable()
                ->default(0);
            $table->string('first_term')
                ->nullable()
                ->default('0');
            $table->string('second_term')
                ->nullable()
                ->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_earnings');
        Schema::dropIfExists('other_earnings');
    }
};
