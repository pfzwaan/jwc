<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The project detail page shows a meta card with the live URL, "Categorieën"
 * and "Vaardigheden" (Figma 31852:2728) — both rendered as pills.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('categories')->nullable()->after('excerpt');
            $table->json('skills')->nullable()->after('categories');
            // Optional portrait shot for the phone in the hero mockup; falls
            // back to the main screenshot when empty.
            $table->string('image_mobile')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['categories', 'skills', 'image_mobile']);
        });
    }
};
