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
        // Contact Heroes
        Schema::create('contact_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Contact Infos
        Schema::create('contact_infos', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('value');
            $table->string('icon_key');
            $table->string('type')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Contact Social Links
        Schema::create('contact_social_links', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url');
            $table->string('icon_key');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Contact Submissions
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('company')->nullable();
            $table->string('reason')->nullable();
            $table->string('budget')->nullable();
            $table->string('timeline')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_submissions');
        Schema::dropIfExists('contact_social_links');
        Schema::dropIfExists('contact_infos');
        Schema::dropIfExists('contact_heroes');
    }
};


