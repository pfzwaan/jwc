<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\MediaUrl;
use Illuminate\Http\JsonResponse;

/**
 * Public site settings for the frontend header/footer (logo, contact, socials).
 * Never returns settings stored with is_public = false.
 */
class SettingController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $values = Setting::publicValues();

        // Resolve logo values to loadable URLs.
        foreach (['logo', 'logo_white'] as $logoKey) {
            if (! empty($values[$logoKey])) {
                $values[$logoKey] = MediaUrl::resolve($values[$logoKey]);
            }
        }

        return response()->json(['data' => $values]);
    }
}
