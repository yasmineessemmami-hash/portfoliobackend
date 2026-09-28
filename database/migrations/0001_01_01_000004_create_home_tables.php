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
        // Home Heroes
        Schema::create('home_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('status_text');
            $table->boolean('status_active')->default(false);
            $table->string('full_name');
            $table->string('role_title');
            $table->string('headline');
            $table->string('subheadline');
            $table->timestamps();
        });

        // Home Featured Projects
        Schema::create('home_featured_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable();
            $table->string('image_type')->default('image');
            $table->string('icon_key')->nullable();
            $table->text('tech')->nullable(); // JSON stored as TEXT for SQLite
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_featured_projects');
        Schema::dropIfExists('home_heroes');
    }
};


