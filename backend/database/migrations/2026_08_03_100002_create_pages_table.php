<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // admin/menu label
            $table->string('title');                // <h1> / SEO title
            $table->string('slug')->unique();       // '' or 'home' = homepage
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('is_published')->default(true);

            // Menu — the pages overview (drag to sort) drives the header menu.
            $table->boolean('menu_visibility')->default(true);
            $table->integer('menu_sort')->default(0);
            $table->string('menu_dropdown_type')->default('none'); // none | dropdown | mega
            $table->json('menu_dropdown_cta')->nullable();          // { label, href }
            $table->json('menu_featured_card')->nullable();         // { badge, title, text, image, cta_label, cta_href }
            $table->string('menu_link')->nullable();                // optional href override
            $table->boolean('menu_diensten')->default(false);       // auto-list Diensten as submenu

            $table->json('seo')->nullable();        // { title, description }
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('pages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
