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
        Schema::create('job_applicant_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('applicant_users')
                ->onDelete('cascade');
            $table->foreignId('skill_id')
                ->constrained('skills_list')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applicant_skills');
    }
};
