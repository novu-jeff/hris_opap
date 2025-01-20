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
            $table->boolean('isToUpdatePassword')
                ->default(false)
                ->after('isNew');
            $table->string('last_password_updated')
                ->after('isToUpdatePassword')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_account', function (Blueprint $table) {
            $table->dropColumn('isToUpdatePassword');
            $table->dropColumn('last_password_updated');
        });
    }
};
