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
        Schema::create('app_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique();
            $table->string('source')->default('unknown'); // instagram / pdf / cv / unknown
            $table->timestamps();
            
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_keys');
    }
};
