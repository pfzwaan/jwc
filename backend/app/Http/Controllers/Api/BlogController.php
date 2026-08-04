<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;

/** CPT listing + detail for Blogs. */
class BlogController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => BlogPost::published()->ordered()->get()->map(fn (BlogPost $p) => $p->toCard())->all(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $post = BlogPost::published()->where('slug', $slug)->first();

        if ($post === null) {
            return response()->json(['message' => 'Blog niet gevonden.'], 404);
        }

        return response()->json([
            'data' => $post->toCard() + [
                'body' => $post->body,
                'seo' => $post->seo,
            ],
        ]);
    }
}
