<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_clock_in_out', function (Blueprint $table) {
            $table->string('origin')->nullable()->after('id');
            $table->string('biometricdtrid')->nullable()->after('origin');
            $table->string('bsdno')->nullable()->after('biometricdtrid');
            $table->boolean('isindtr')->nullable()->after('bsdno');
            $table->string('nfcdeviceid')->nullable()->after('isindtr');
            $table->integer('type')->default(0)->after('nfcdeviceid');
            $table->boolean('ismanual')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('employee_clock_in_out', function (Blueprint $table) {
            $table->dropColumn([
                'origin',
                'biometricdtrid',
                'bsdno',
                'isindtr',
                'nfcdeviceid',
                'type',
                'ismanual',
            ]);
        });
    }
};
