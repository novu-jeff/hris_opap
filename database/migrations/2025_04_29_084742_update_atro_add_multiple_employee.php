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
        Schema::create('employee_atro_relative', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_atro_id')
                ->constrained('employee_atro')
                ->onDelete('cascade');
            $table->string('employee_no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_atro_relative', function (Blueprint $table) {
            $table->dropForeign(['employee_atro_id']);
        });

        Schema::dropIfExists('employee_atro_relative');
    }
};
