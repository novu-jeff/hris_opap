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
         Schema::create('employment_type_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employment_type_id')
                  ->constrained('employment_types')
                  ->onDelete('cascade');

            $table->boolean('is_salary')->default(false);
            $table->boolean('is_ot_pay')->default(false);
            $table->boolean('is_clothing_allowance')->default(false);
            $table->boolean('is_mid_year')->default(false);
            $table->boolean('is_year_end')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_type_settings');
    }
};
