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
        Schema::create('session_events', function (Blueprint $table) {
            $table->id();
            $table->string('user_key', 64)->index();
            $table->string('app_key', 64)->index();
            $table->json('event');
            $table->timestamps();
            
            $table->index(['user_key', 'app_key']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_events');
    }
};
