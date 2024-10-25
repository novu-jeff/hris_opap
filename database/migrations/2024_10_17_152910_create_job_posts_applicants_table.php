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
        Schema::create('job_applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('applicant_users')
                ->onDelete('cascade');
            $table->string('applicant_no');
            $table->foreignId('job_id')
                ->constrained('job_posts');
            $table->string('status');
            $table->boolean('isInterviewResponded')
                ->default(false);
            $table->boolean('isSignedJobOffer')
                ->default(false);
            $table->timestamps();
        });

        Schema::create('job_applicants_interview', function(Blueprint $table) {
            $table->id();
            $table->foreignId('job_applicants_id')
                ->constrained('job_applicants')
                ->onDelete('cascade');
            $table->foreignId('job_interview_id')
                ->constrained('job_interview')
                ->onDelete('cascade');
        });

        Schema::create('job_applicants_offer', function(Blueprint $table) {
            $table->id();
            $table->foreignId('job_applicants_id')
                ->constrained('job_applicants')
                ->onDelete('cascade');
            $table->string('subject');
            $table->longText('body');
            $table->string('starting_date');
            $table->string('salary');
            $table->string('attachment');
            $table->string('signed_attachment')
                ->nullable();
        });

        Schema::create('job_applicants_requirements', function(Blueprint $table) {
            $table->id();
            $table->foreignId('job_applicants_id')
                ->constrained('job_applicants')
                ->onDelete('cascade');
            $table->foreignId('requirement_id')
                ->constrained('job_requirements')
                ->onDelete('cascade');
            $table->string('attachment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applicants_requirements');
        Schema::dropIfExists('job_applicants_interview');
        Schema::dropIfExists('job_applicants_offer');
        Schema::dropIfExists('job_applicants');
    }
};
