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
        Schema::create('bank_informations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('account_number');
            $table->foreignId('department_id')
                ->constrained('departments')
                ->onDelete('cascade');
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_informations');
    }
};
