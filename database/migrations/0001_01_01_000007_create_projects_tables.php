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
        // Projects Heroes
        Schema::create('projects_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Projects
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image');
            $table->string('image_type')->default('image');
            $table->string('key')->nullable();
            $table->text('tech_stack')->nullable(); // JSON stored as TEXT for SQLite
            $table->string('github_url')->nullable();
            $table->string('live_url')->nullable();
            $table->string('contact_email')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
        Schema::dropIfExists('projects_heroes');
    }
};


