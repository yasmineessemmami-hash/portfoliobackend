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
        Schema::create('replay_events', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->string('user_key', 255)->index();
            $table->string('app_key', 255)->index();
            $table->json('events'); // Array of rrweb events
            $table->bigInteger('timestamp')->index(); // Unix timestamp in milliseconds
            $table->timestamps();
            
            $table->index(['session_id', 'timestamp']);
            $table->index(['user_key', 'app_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('replay_events');
    }
};
