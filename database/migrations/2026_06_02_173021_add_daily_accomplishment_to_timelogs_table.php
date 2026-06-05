<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timelogs', function (Blueprint $table) {
            $table->string('accomplishment_type')->nullable()->after('accomplishment');
            $table->text('accomplishment_details')->nullable()->after('accomplishment_type');
        });
    }

    public function down(): void
    {
        Schema::table('timelogs', function (Blueprint $table) {
            $table->dropColumn([
                'accomplishment_type',
                'accomplishment_details'
            ]);
        });
    }
};