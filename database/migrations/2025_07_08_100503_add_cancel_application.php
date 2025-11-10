<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE employee_leave MODIFY COLUMN status ENUM('approved', 'disapproved', 'pending', 'cancelled') DEFAULT 'pending'");
        DB::statement("ALTER TABLE employee_business_slips MODIFY COLUMN status ENUM('approved', 'disapproved', 'pending', 'cancelled') DEFAULT 'pending'");
        DB::statement("ALTER TABLE employee_atro MODIFY COLUMN status ENUM('approved', 'disapproved', 'pending', 'cancelled') DEFAULT 'pending'");

        Schema::table('employee_request_logs_attachments', function (Blueprint $table) {
            $table->dropForeign(['employee_requests_id']);
        });

        Schema::rename('employee_request_logs', 'employee_time_adjustments');
        Schema::rename('employee_request_logs_attachments', 'employee_time_adjustments_attach');

        Schema::table('employee_time_adjustments_attach', function (Blueprint $table) {
            $table->foreign('employee_requests_id')
                ->references('id')
                ->on('employee_time_adjustments')
                ->onDelete('cascade');
        });

        DB::statement("ALTER TABLE employee_time_adjustments MODIFY COLUMN status ENUM('approved', 'disapproved', 'pending', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE employee_leave MODIFY COLUMN status ENUM('approved', 'disapproved', 'pending') DEFAULT 'pending'");
        DB::statement("ALTER TABLE employee_business_slips MODIFY COLUMN status ENUM('approved', 'disapproved', 'pending') DEFAULT 'pending'");
        DB::statement("ALTER TABLE employee_atro MODIFY COLUMN status ENUM('approved', 'disapproved', 'pending') DEFAULT 'pending'");
        DB::statement("ALTER TABLE employee_time_adjustments MODIFY COLUMN status ENUM('approved', 'disapproved', 'pending') DEFAULT 'pending'");

        Schema::table('employee_time_adjustments_attach', function (Blueprint $table) {
            $table->dropForeign(['employee_requests_id']);
        });

        Schema::rename('employee_time_adjustments', 'employee_request_logs');
        Schema::rename('employee_time_adjustments_attach', 'employee_request_logs_attachments');

        Schema::table('employee_request_logs_attachments', function (Blueprint $table) {
            $table->foreign('employee_requests_id')
                ->references('id')
                ->on('employee_request_logs')
                ->onDelete('cascade');
        });
    }
};
