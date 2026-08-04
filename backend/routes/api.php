<?php

use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DienstController;
use App\Http\Controllers\Api\FooterController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // Public config + health.
    Route::get('/config', ConfigController::class);

    // Public site settings (logo, contact, socials) — no private keys.
    Route::get('/settings', SettingController::class);

    // Editable footer columns (Filament: "Footer" page).
    Route::get('/footer', FooterController::class);

    // Header menu tree (the pages overview drives the menu) + CMS pages.
    Route::get('/menu', MenuController::class);
    Route::get('/pages/{slug}', [PageController::class, 'show']);

    // CPT listings used by the page widgets and overview pages.
    Route::get('/diensten', [DienstController::class, 'index']);
    Route::get('/diensten/{slug}', [DienstController::class, 'show']);
    Route::get('/blog', [BlogController::class, 'index']);
    Route::get('/blog/{slug}', [BlogController::class, 'show']);
    Route::get('/projecten', [ProjectController::class, 'index']);
    Route::get('/projecten/{slug}', [ProjectController::class, 'show']);

    // Contact / offerte form.
    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware(['throttle:10,1', 'honeypot']);
});
