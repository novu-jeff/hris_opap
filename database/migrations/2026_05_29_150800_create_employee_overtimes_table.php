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
        Schema::create('employee_overtimes', function (Blueprint $table) {
            $table->id();

            $table->string('employee_no')->index();
            $table->date('work_date')->index();

            $table->integer('total_minutes')->default(0);
            $table->integer('required_minutes')->default(480);
            $table->integer('overtime_minutes')->default(0);

            $table->decimal('overtime_hours', 8, 2)->default(0);

            $table->dateTime('scheduled_out')->nullable();
            $table->dateTime('actual_out')->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'cancelled'
            ])->default('pending');

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique(['employee_no', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_overtimes');
    }
};