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
        Schema::create('applicant_users', function (Blueprint $table) {
            $table->id();
            $table->string('image')
                ->nullable();
            $table->string('firstname');
            $table->string('middlename')
                ->nullable();
            $table->string('lastname');
            $table->string('phone_no');
            $table->string('tel_no')
                ->nullable();
            $table->string('sex');
            $table->string('birthday');
            $table->string('civil_status');
            $table->string('address');
            $table->string('province');
            $table->string('city');
            $table->string('resume');
            $table->string('email');
            $table->string('password');
            $table->string('level')->nullable();
            $table->string('school_name')->nullable();;
            $table->string('course')->nullable();;
            $table->string('started')->nullable();;
            $table->string('finished')->nullable();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_users');
    }
};
