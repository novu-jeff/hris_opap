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
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->longText('slug');
            $table->string('position');
            $table->string('company_name');
            $table->string('location');
            $table->enum('setup', [
                    'work from home',
                    'onsite',
                    'hybrid'
                ]);
            $table->foreignId('employment_type_id')
                ->nullable()
                ->constrained('employment_types')
                ->onDelete('set null');
            $table->integer('min_salary');
            $table->integer('max_salary');
            $table->longText('description');
            $table->integer('slots');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
