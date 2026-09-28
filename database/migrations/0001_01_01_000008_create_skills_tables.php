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
        // Skills Heroes
        Schema::create('skills_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle');
            $table->text('description')->nullable();
            $table->string('cv_label')->default('Download CV');
            $table->string('cv_file_url')->nullable();
            $table->timestamps();
        });

        // Skills Social Links
        Schema::create('skills_social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform');
            $table->string('url');
            $table->string('icon_key');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Skill Categories
        Schema::create('skill_categories', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('icon_key');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Skills
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('skill_categories')->onDelete('cascade');
        });

        // Learning Focus
        Schema::create('learning_focus', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('topics')->nullable(); // JSON stored as TEXT for SQLite
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_focus');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_categories');
        Schema::dropIfExists('skills_social_links');
        Schema::dropIfExists('skills_heroes');
    }
};


