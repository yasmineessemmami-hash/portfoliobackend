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
        Schema::create('replay_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->unique();
            $table->string('user_key', 255)->index();
            $table->string('app_key', 255)->index();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('last_event_at')->nullable();
            $table->integer('event_count')->default(0);
            
            $table->index(['user_key', 'app_key']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('replay_sessions');
    }
};
