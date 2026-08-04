<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dienst;
use App\Support\MediaUrl;
use Illuminate\Http\JsonResponse;

/** CPT listing + detail for Diensten. */
class DienstController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Dienst::published()->ordered()->get()->map(fn (Dienst $d) => $d->toCard())->all(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $dienst = Dienst::published()->where('slug', $slug)->first();

        if ($dienst === null) {
            return response()->json(['message' => 'Dienst niet gevonden.'], 404);
        }

        return response()->json([
            'data' => $dienst->toCard() + [
                'body' => $dienst->body,
                'seo' => $dienst->seo,
                'image' => MediaUrl::resolve($dienst->image),
            ],
        ]);
    }
}
