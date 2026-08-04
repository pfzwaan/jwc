<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dienst;
use App\Models\Page;
use App\Support\MediaUrl;
use Illuminate\Http\JsonResponse;

/**
 * Header menu tree — the pages overview (drag to sort) drives the navigation.
 * Returns visible root pages, each with visible children plus its dropdown /
 * mega-menu config (type, CTA, featured card). Hrefs stay raw (frontend safeUrl).
 */
class MenuController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $roots = Page::inMenu()
            ->roots()
            ->with(['children' => fn ($q) => $q->inMenu()])
            ->orderBy('menu_sort')
            ->get();

        return response()->json([
            'data' => $roots->map(fn (Page $page) => $this->mapItem($page))->all(),
        ]);
    }

    private function mapItem(Page $page): array
    {
        return [
            'id' => $page->id,
            'label' => $page->name,
            'href' => $page->menuHref(),
            'type' => $page->menu_dropdown_type, // none | dropdown | mega
            'cta' => $this->mapCta($page->menu_dropdown_cta),
            'featured' => $this->mapFeatured($page->menu_featured_card),
            'children' => $page->menu_diensten
                ? $this->dienstChildren()
                : $page->children->map(function (Page $child) use ($page): array {
                    $child->setRelation('parent', $page); // cheap, correct fullPath()

                    return [
                        'id' => $child->id,
                        'label' => $child->name,
                        'href' => $child->menuHref(),
                    ];
                })->all(),
        ];
    }

    /** Published diensten as menu children — same set (and order) as /diensten. */
    private function dienstChildren(): array
    {
        return Dienst::published()
            ->ordered()
            ->get()
            ->map(fn (Dienst $dienst): array => [
                'id' => 'dienst-'.$dienst->id,
                // The short label ("Cloud Diensten") reads far better in a menu
                // than the full page title ("Veilig werken in de cloud").
                'label' => $dienst->label ?: $dienst->title,
                'href' => '/diensten/'.$dienst->slug,
            ])
            ->all();
    }

    /** @param array<string,mixed>|null $cta */
    private function mapCta(?array $cta): ?array
    {
        $label = trim((string) ($cta['label'] ?? ''));
        $href = trim((string) ($cta['href'] ?? ''));

        if ($label === '' || $href === '') {
            return null;
        }

        return ['label' => $label, 'href' => $href];
    }

    /** @param array<string,mixed>|null $card */
    private function mapFeatured(?array $card): ?array
    {
        if (trim((string) ($card['title'] ?? '')) === '') {
            return null;
        }

        return [
            'badge' => $card['badge'] ?? null,
            'title' => $card['title'],
            'text' => $card['text'] ?? null,
            'image' => MediaUrl::resolve($card['image'] ?? null),
            'ctaLabel' => $card['cta_label'] ?? null,
            'ctaHref' => $card['cta_href'] ?? null,
        ];
    }
}
