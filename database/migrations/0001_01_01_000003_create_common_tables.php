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
        // Site Settings
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('site_mode', ['normal', 'maintenance'])->default('normal');
            $table->timestamp('maintenance_until')->nullable();
            $table->string('full_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->timestamps();
        });

        // Themes
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('theme_key')->unique();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Theme Palettes
        Schema::create('theme_palettes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('palette'); // JSON stored as TEXT for SQLite
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Icons (General Icons)
        Schema::create('icons', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('icon');
            $table->string('library');
            $table->timestamps();
        });

        // Social Media Icons
        Schema::create('social_media_icons', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('icon');
            $table->string('library');
            $table->timestamps();
        });

        // Social Links (Common - polymorphic)
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform');
            $table->string('url');
            $table->string('icon_key');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['owner_type', 'owner_id']);
        });

        // Meta Pages (SEO)
        Schema::create('meta_pages', function (Blueprint $table) {
            $table->id();
            $table->string('page');
            $table->string('slug')->nullable();
            $table->string('locale', 5)->default('en');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('keywords')->nullable(); // JSON stored as TEXT for SQLite
            $table->timestamps();
            $table->unique(['page', 'locale']);
        });

        // Admins
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
        Schema::dropIfExists('meta_pages');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('social_media_icons');
        Schema::dropIfExists('icons');
        Schema::dropIfExists('theme_palettes');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('site_settings');
    }
};
