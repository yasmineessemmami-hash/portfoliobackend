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
        // About Heroes
        Schema::create('about_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // About Stats
        Schema::create('about_stats', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('value');
            $table->string('label');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // About Introductions
        Schema::create('about_introductions', function (Blueprint $table) {
            $table->id();
            $table->string('avatar_image')->nullable();
            $table->boolean('availability_active')->default(false);
            $table->string('availability_text')->nullable();
            $table->string('full_name');
            $table->string('role_title');
            $table->text('paragraphs')->nullable(); // JSON stored as TEXT for SQLite
            $table->text('tech_stack')->nullable(); // JSON stored as TEXT for SQLite
            $table->timestamps();
        });

        // About Services
        Schema::create('about_services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('features')->nullable(); // JSON stored as TEXT for SQLite
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // About Work Processes
        Schema::create('about_work_processes', function (Blueprint $table) {
            $table->id();
            $table->integer('step');
            $table->string('title');
            $table->text('description');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // About Values
        Schema::create('about_values', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('title');
            $table->text('description');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_values');
        Schema::dropIfExists('about_work_processes');
        Schema::dropIfExists('about_services');
        Schema::dropIfExists('about_introductions');
        Schema::dropIfExists('about_stats');
        Schema::dropIfExists('about_heroes');
    }
};


