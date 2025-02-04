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
        Schema::create('tranche', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('isActive')
                ->default(false);
            $table->boolean('isDeleted')
                ->default(false);
            $table->timestamps();
        });

        Schema::create('tranche_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tranche_id')
                ->constrained('tranche')
                ->onDelete('cascade');
            $table->string('salary_grade');
            $table->string('step_1');
            $table->string('step_2');
            $table->string('step_3');
            $table->string('step_4');
            $table->string('step_5');
            $table->string('step_6');
            $table->string('step_7');
            $table->string('step_8');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tranche_items');
        Schema::dropIfExists('tranche');
    }
};
