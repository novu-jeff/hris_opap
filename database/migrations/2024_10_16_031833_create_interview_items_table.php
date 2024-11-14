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

        Schema::create('job_interview_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')
                ->constrained('job_interview')
                ->onDelete('cascade');
            $table->longText('question');
            $table->string('response_type');
        });

        Schema::create('job_interview_items_option', function(Blueprint $table) {
            $table->id();
            $table->foreignId('interview_item_id')
                ->constrained('job_interview_items')
                ->onDelete('cascade');
            $table->string('name');
        });

        Schema::create('job_interview_items_responses', function(Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('applicant_users')
                ->onDelete('cascade');
            $table->foreignId('interview_item_id')
                ->constrained('job_interview_items')
                ->onDelete('cascade');
            $table->longText('answer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_interview_items_option');
        Schema::dropIfExists('job_interview_items_responses');
        Schema::dropIfExists('job_interview_items');
    }
};
