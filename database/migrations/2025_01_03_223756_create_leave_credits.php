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
        Schema::create('leave_credits', function(Blueprint $table) {
            $table->id();
            $table->foreignId('leave_type_id')
                ->nullable()
                ->constrained('leave_types')
                ->onDelete('cascade');
            $table->string('employee_no');
            $table->string('credits');
            $table->string('as_of')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_credits');
    }
};
