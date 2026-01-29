<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tranche', function (Blueprint $table) {
            $table->year('year')->nullable()->after('name');
            $table->boolean('is_active')->default(false)->after('eligible');
        });
    }

    public function down(): void
    {
        Schema::table('tranche', function (Blueprint $table) {
            $table->dropColumn(['year', 'is_active']);
        });
    }
};
