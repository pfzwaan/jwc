<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reusable block layouts — currently just the footer: a set of ordered columns,
 * each holding ordered widget blocks. Edited on the Filament "Footer" page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('block_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('block_layout_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_layout_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        Schema::create('block_layout_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_layout_column_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->json('data');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('block_layout_blocks');
        Schema::dropIfExists('block_layout_columns');
        Schema::dropIfExists('block_layouts');
    }
};
