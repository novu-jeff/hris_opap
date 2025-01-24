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
        Schema::create('employee_announcements', function(Blueprint $table) {
            $table->id();
            $table->string('banner');
            $table->string('title');
            $table->longText('content');
            $table->boolean('isDeleted')
                ->default(false);
            $table->timestamps();
        });

        Schema::create('employee_announcements_attachments', function(Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')
                ->constrained('employee_announcements')
                ->onDelete('cascade');
            $table->string('name')
                ->nullable();
            $table->string('file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_announcements_attachments');
        Schema::dropIfExists('employee_announcements');
    }
};
