<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('landing_stats', function (Blueprint $table) {
            $table->id();
            $table->string('value'); // e.g., "500+"
            $table->string('label'); // e.g., "Schools Trust Us"
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable(); // e.g., "Principal, Greenwood High"
            $table->text('content');
            $table->integer('rating')->default(5);
            $table->string('avatar_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Display name
            $table->string('component'); // Blade component name e.g., 'landing.hero'
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // For future extensibility (bg color, etc.)
            $table->timestamps();
        });

        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('content'); // HTML content
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('landing_pages');
        Schema::dropIfExists('landing_sections');
        Schema::dropIfExists('landing_faqs');
        Schema::dropIfExists('landing_testimonials');
        Schema::dropIfExists('landing_stats');
    }
};
