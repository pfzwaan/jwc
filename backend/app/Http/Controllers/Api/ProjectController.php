<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

/** CPT listing + detail for Projecten. */
class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Project::published()->ordered()->get()->map(fn (Project $p) => $p->toCard())->all(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $project = Project::published()->where('slug', $slug)->first();

        if ($project === null) {
            return response()->json(['message' => 'Project niet gevonden.'], 404);
        }

        return response()->json([
            'data' => $project->toCard() + [
                'body' => $project->body,
                'gallery' => $project->gallery,
                'seo' => $project->seo,
            ],
        ]);
    }
}
