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
        Schema::create('user_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique();
            $table->string('label')->default('unknown'); // client / recruiter / unknown
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_keys');
    }
};
