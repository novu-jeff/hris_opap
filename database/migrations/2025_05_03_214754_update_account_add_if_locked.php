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
        Schema::table('employee_account', function (Blueprint $table) {
            $table->boolean('isLocked')
                ->default(false)
                ->after('last_password_updated');
            $table->integer('login_attempts')
                ->after('isLocked')
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_account', function (Blueprint $table) {
            $table->dropColumn('isLocked');
            $table->dropColumn('login_attempts');
        });
    }
};
