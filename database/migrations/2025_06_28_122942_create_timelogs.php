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

        $isExternalTimelogs = config('app.external_timelogs');

        if ($isExternalTimelogs !== false && $isExternalTimelogs !== 'false') {
            return;
        }

        Schema::create('timelogs', function (Blueprint $table) {
            $table->id(); 
            $table->string('employee_id');
            $table->dateTime('timestamp');
            $table->boolean('status')->nullable();
            $table->boolean('isWeb')->nullable();
            $table->string('captured_image')->nullable();
            $table->string('captured_location')->nullable();
            $table->string('accomplishment')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        $isExternalTimelogs = config('app.external_timelogs');

        if ($isExternalTimelogs !== false && $isExternalTimelogs !== 'false') {
            return;
        }

        Schema::dropIfExists('timelogs');
    }
};
