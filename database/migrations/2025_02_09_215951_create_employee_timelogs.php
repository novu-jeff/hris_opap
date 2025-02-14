<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_timelogs', function (Blueprint $table) {
            $table->id(); 
            $table->string('origin')
                ->nullable();
            $table->string('biometricdtrid')
                ->nullable();
            $table->string('bsd_no')
                ->nullable()
                ->index();
            $table->string('isindtr')
                ->nullable();
            $table->string('logdatetime')
                ->nullable();
            $table->string('nfcdeviceid')
                ->index()
                ->nullable();
            $table->string('type')
                ->nullable();
            $table->string('ismanual')
                ->nullable();
            $table->string('captured_image')
                ->nullable();
            $table->string('captured_location')
                ->nullable();
            $table->longText('accomplishment')
                ->nullable();
            $table->boolean('isComputed')
                ->default(false)
                ->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_timelogs');
    }
};
