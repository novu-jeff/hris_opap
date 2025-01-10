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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('from_id')->index(); // Add index for 'from_id'
            $table->string('from_role');
            $table->string('to_id')->index(); // Add index for 'to_id'
            $table->string('to_role');
            $table->longText('message')->nullable();
            $table->boolean('isSeen')->default(false);
            $table->timestamps();
        });
        
        Schema::create('messages_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')
                ->constrained('messages')
                ->onDelete('cascade');
            $table->string('original');
            $table->string('attachment');
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages_attachments');
        Schema::dropIfExists('messages');
    }
};
