<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Custom post type: Diensten (services). Its own model + Filament resource,
 * exposed via /api/v1/diensten. Widgets can pick specific diensten; when none
 * are picked they fall back to the latest.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diensten', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('label')->nullable();      // pill above the title, e.g. "ICT SUPPORT"
            $table->text('excerpt')->nullable();      // short intro for cards
            $table->longText('body')->nullable();     // rich text (HTML)
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->json('features')->nullable();     // checklist items
            $table->string('cta_label')->nullable();
            $table->string('cta_href')->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('seo')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diensten');
    }
};
