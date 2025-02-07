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
        Schema::create('employee_leave_cards', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no');
            $table->string('period')
                ->nullable();
            $table->tinyText('vl_particulars')
                ->nullable();
            $table->string('vl_earned')
                ->nullable();
            $table->string('vl_aut_w_pay')
                ->nullable();
            $table->string('vl_bal')
                ->nullable();
            $table->string('vl_aut_wo_pay')
                ->nullable();
            $table->tinyText('vl_remarks')
                ->nullable();
            $table->string('sl_earned')
                ->nullable();
            $table->string('sl_aut_w_pay')
                ->nullable();
            $table->string('sl_bal')
                ->nullable();
            $table->string('sl_aut_wo_pay')
                ->nullable();
            $table->tinyText('sl_particulars')
                ->nullable();
            $table->tinyText('sl_remarks')
                ->nullable();
            $table->string('year')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leave_cards');
    }
};
