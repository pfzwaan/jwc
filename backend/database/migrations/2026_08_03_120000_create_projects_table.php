<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Custom post type: Projecten (realised websites / ICT projects).
 *
 * Previously the projects lived inside the widget's own repeater, which meant
 * the homepage slider and the Website & Hosting grid each held their own copy.
 * As a CPT they are edited once and listed by both, and each gets a detail page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image')->nullable();       // screenshot shown in the card
            $table->string('url')->nullable();         // the live site
            $table->string('accent_color', 32)->nullable();
            $table->boolean('is_featured')->default(false); // adds the card shadow
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();      // detail page content
            $table->json('gallery')->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('seo')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
