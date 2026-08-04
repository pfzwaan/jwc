<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Each dienst carries its own accent colour — the design gives every box on the
 * homepage a different one (blue, orange, green, purple, orange).
 *
 * `accent_color` drives the button, the check ticks and the pill background
 * (at 20%); `accent_label_color` is the pill's text, which the design sets to a
 * darker shade for the Microsoft 365 box only.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diensten', function (Blueprint $table) {
            $table->string('accent_color', 32)->nullable()->after('icon');
            $table->string('accent_label_color', 32)->nullable()->after('accent_color');
        });
    }

    public function down(): void
    {
        Schema::table('diensten', function (Blueprint $table) {
            $table->dropColumn(['accent_color', 'accent_label_color']);
        });
    }
};
