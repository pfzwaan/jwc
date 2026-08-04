<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Spam honeypot. Public forms render a hidden "website" field that humans leave
 * empty but bots tend to fill. If it arrives non-empty we drop the request.
 */
class Honeypot
{
    /** Hidden field name — must match the frontend Honeypot component. */
    public const FIELD = 'website';

    public function handle(Request $request, Closure $next): Response
    {
        if (filled($request->input(self::FIELD))) {
            // Looks automated — reject without processing (and without hints).
            abort(422, 'Ongeldige inzending.');
        }

        return $next($request);
    }
}
