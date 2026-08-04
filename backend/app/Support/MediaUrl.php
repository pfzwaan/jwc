<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Resolves a stored image value to a URL the frontend can load.
 * - empty              -> '' (frontend shows its placeholder / design default)
 * - absolute (http)    -> as-is
 * - root-relative (/…) -> as-is (e.g. seeded design assets served by the frontend)
 * - storage path       -> absolute backend URL (http://localhost:8002/storage/…)
 */
class MediaUrl
{
    /**
     * Public URL for an uploaded file, built from the configured APP_URL rather
     * than the incoming request. The Next.js server renders pages by calling the
     * API over the container-internal INTERNAL_API_URL (http://backend:8002/api),
     * so a request-based helper like asset() would hand the browser
     * http://backend:8002/storage/… — a hostname only Docker can resolve.
     */
    private static function storageUrl(string $path): string
    {
        return Storage::disk('public')->url(ltrim($path, '/'));
    }

    public static function resolve(?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '/')) {
            return $value;
        }

        return self::storageUrl($value);
    }

    /**
     * Absolute URL for use inside the Filament admin (a different origin than the
     * frontend). Root-relative design assets (/images/…) are served by the
     * frontend; uploads live in backend storage. Returns null when empty so
     * ImageColumn shows its placeholder.
     */
    public static function admin(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        if (str_starts_with($value, '/')) {
            return rtrim((string) config('app.frontend_url'), '/').$value;
        }

        return self::storageUrl($value);
    }
}
