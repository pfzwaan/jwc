<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Dienst;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Project;
use App\Support\MediaUrl;
use Illuminate\Http\JsonResponse;

/**
 * A single CMS page with its visible blocks, for the frontend [...slug] route.
 * CPT blocks (diensten/blog) are resolved server-side so pages are fully
 * server-rendered (SEO): explicit selection, or the latest when none chosen.
 */
class PageController extends Controller
{
    /** Widget data keys holding an uploaded media path — resolved to loadable URLs at any depth. */
    private const MEDIA_KEYS = ['image', 'bg_image', 'icon', 'logo', 'avatar', 'video'];

    public function show(string $slug): JsonResponse
    {
        $page = Page::published()
            ->where('slug', $slug)
            ->with('visibleBlocks')
            ->first();

        if ($page === null) {
            return response()->json(['message' => 'Pagina niet gevonden.'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'seo' => $page->seo,
                'blocks' => $page->visibleBlocks->map(fn (PageBlock $block) => $this->mapBlock($block))->all(),
            ],
        ]);
    }

    private function mapBlock(PageBlock $block): array
    {
        $data = $this->resolveMedia($block->data ?? []);

        // Widgets that list a CPT get their items resolved here, so the page
        // arrives at the frontend fully rendered.
        if (array_key_exists('dienst_ids', $data)) {
            $data['items'] = $this->diensten($data['dienst_ids'] ?? [], (int) ($data['count'] ?? 4));
        }

        if (array_key_exists('post_ids', $data)) {
            $data['items'] = $this->posts($data['post_ids'] ?? [], (int) ($data['count'] ?? 3));
        }

        if (array_key_exists('project_ids', $data)) {
            $data['items'] = $this->projects($data['project_ids'] ?? [], (int) ($data['count'] ?? 4));
        }

        return [
            'id' => $block->id,
            'type' => $block->type,
            'data' => $data,
        ];
    }

    /**
     * Walk the widget data and turn every stored image path into a URL the
     * browser can load. Recurses so nested repeaters (rows, boxes, cards) are
     * covered without each widget needing its own case here.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function resolveMedia(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->resolveMedia($value);

                continue;
            }

            if (in_array($key, self::MEDIA_KEYS, true) && is_string($value)) {
                $data[$key] = MediaUrl::resolve($value);
            }
        }

        return $data;
    }

    /** @param array<int,int> $ids */
    private function diensten(array $ids, int $count = 4): array
    {
        if (empty($ids)) {
            return Dienst::published()->ordered()->limit(max(1, $count))
                ->get()->map(fn (Dienst $d) => $d->toCard())->all();
        }

        $items = Dienst::published()->whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)
            ->map(fn ($id) => $items->get($id))
            ->filter()
            ->map(fn (Dienst $d) => $d->toCard())
            ->values()->all();
    }

    /** @param array<int,int> $ids */
    private function projects(array $ids, int $count = 4): array
    {
        if (empty($ids)) {
            return Project::published()->ordered()->limit(max(1, $count))
                ->get()->map(fn (Project $p) => $p->toCard())->all();
        }

        $items = Project::published()->whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)
            ->map(fn ($id) => $items->get($id))
            ->filter()
            ->map(fn (Project $p) => $p->toCard())
            ->values()->all();
    }

    /** @param array<int,int> $ids */
    private function posts(array $ids, int $count = 3): array
    {
        if (empty($ids)) {
            return BlogPost::published()->ordered()->limit(max(1, $count))
                ->get()->map(fn (BlogPost $p) => $p->toCard())->all();
        }

        $items = BlogPost::published()->whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)
            ->map(fn ($id) => $items->get($id))
            ->filter()
            ->map(fn (BlogPost $p) => $p->toCard())
            ->values()->all();
    }
}
