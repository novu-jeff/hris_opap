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
        $product = config('app.product');

        if ($product === 'government') {
            Schema::create('other_earnings', function (Blueprint $table) {
                $table->id();
                $table->string('code')->nullable();
                $table->string('name');
                $table->enum('amount_basis', ['entry', 'basic_salary', 'percentage']);
                $table->float('amount')->nullable();
                $table->enum('frequency_basis', ['monthly', 'yearly', 'month_picked']);
                $table->string('frequency')->nullable();
                $table->string('eligible');
                $table->boolean('isTaxable')->default(false);
                $table->float('forcasted')->default(0)->nullable();
                $table->enum('duration', ['days', 'months', 'years'])->nullable();
                $table->integer('count')->nullable();
                $table->enum('context', ['from', 'prior_to', 'subsequent_to'])->nullable();
                $table->string('date')->nullable();
                $table->timestamps();
            });
        }

        if ($product === 'private') {
            Schema::create('other_earnings', function (Blueprint $table) {
                $table->id();
                $table->string('code')->nullable();
                $table->string('name');
                $table->string('first_term')->default('0');
                $table->string('second_term')->default('0');
                $table->boolean('isTaxable')->default(false);
                $table->timestamps();
            });

            Schema::create('employee_earnings', function (Blueprint $table) {
                $table->id();
                $table->string('employee_no');
                $table->foreignId('earning_id')
                    ->constrained('other_earnings')
                    ->onDelete('cascade');
                $table->string('first_term')->default('0');
                $table->string('second_term')->default('0');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $product = config('app.product');

        if ($product === 'private') {
            Schema::dropIfExists('employee_earnings');
        }

        Schema::dropIfExists('other_earnings');
    }
};
