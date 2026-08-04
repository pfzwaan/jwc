<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/** Public health/config probe for the frontend. */
class ConfigController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => [
                'name' => config('app.name'),
                'locale' => config('app.locale'),
                'status' => 'ok',
            ],
        ]);
    }
}
