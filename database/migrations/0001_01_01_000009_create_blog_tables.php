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
        // Blog Heroes
        Schema::create('blog_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Blog Authors
        Schema::create('blog_authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->text('social_links')->nullable(); // JSON stored as TEXT for SQLite
            $table->timestamps();
        });

        // Blog Posts
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content'); // JSON stored as TEXT for SQLite
            $table->string('image')->nullable();
            $table->enum('media_type', ['image', 'emoji', 'icon'])->default('image');
            $table->string('category')->nullable();
            $table->text('tags')->nullable(); // JSON stored as TEXT for SQLite
            $table->unsignedBigInteger('author_id');
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->foreign('author_id')->references('id')->on('blog_authors')->onDelete('cascade');
        });

        // Blog Subscriptions
        Schema::create('blog_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->boolean('is_subscribed')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_subscriptions');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_authors');
        Schema::dropIfExists('blog_heroes');
    }
};


