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
        // Services Heroes
        Schema::create('services_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Service Items
        Schema::create('service_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('icon_key');
            $table->string('color')->nullable();
            $table->text('features')->nullable(); // JSON stored as TEXT for SQLite
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Why Choose Me Items
        Schema::create('why_choose_me_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('icon_key');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Deliverable Items
        Schema::create('deliverable_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('icon_key');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliverable_items');
        Schema::dropIfExists('why_choose_me_items');
        Schema::dropIfExists('service_items');
        Schema::dropIfExists('services_heroes');
    }
};


