<?php

namespace App\Support;

/**
 * Protects seeded design images from being wiped by an unrelated save.
 *
 * The seeders store design assets as root-relative paths ("/images/design/
 * hero.png") which the frontend serves from its own public/ directory.
 * Filament's FileUpload can only represent files that exist on the uploads
 * disk, so it hydrates such a value as an empty array — and saving the form
 * writes that emptiness straight back, silently clearing the image even though
 * nobody touched the field.
 *
 * We therefore re-apply the stored value whenever the submitted one is empty
 * *and* the stored one is a path FileUpload could never have held. A real
 * upload ("diensten/01KZ….png") is left alone: FileUpload can display it, so an
 * empty submission there genuinely means staff removed the image.
 */
class MediaState
{
    /**
     * Merge $submitted over $stored, restoring design-asset images that
     * FileUpload dropped. Recurses so nested widget data (rows, boxes,
     * cards, galleries) is covered too.
     *
     * @param  array<string, mixed>  $submitted
     * @param  array<string, mixed>  $stored
     * @return array<string, mixed>
     */
    public static function preserveDesignAssets(array $submitted, array $stored): array
    {
        foreach ($stored as $key => $old) {
            $new = $submitted[$key] ?? null;

            if (is_array($old) && is_array($new) && ! self::isEmpty($new)) {
                $submitted[$key] = self::preserveDesignAssets($new, $old);

                continue;
            }

            if (! self::isEmpty($new)) {
                continue;
            }

            // The field came back empty — restore it only if what's stored is a
            // design asset (a whole gallery of them, or a single image).
            if (self::isDesignAsset($old) || self::isDesignAssetList($old)) {
                $submitted[$key] = $old;
            }
        }

        return $submitted;
    }

    /** FileUpload reports "no file" as null, '' or an empty array. */
    private static function isEmpty(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [];
    }

    /**
     * A path the frontend serves itself ("/images/…") or an external URL —
     * never something that lives on the uploads disk.
     */
    private static function isDesignAsset(mixed $value): bool
    {
        if (! is_string($value) || $value === '') {
            return false;
        }

        $isExternal = str_starts_with($value, '/')
            || str_starts_with($value, 'http://')
            || str_starts_with($value, 'https://');

        if (! $isExternal) {
            return false;
        }

        // Require an image extension so link fields ("/contact", "/diensten"),
        // which staff must be able to clear, are never resurrected.
        $path = parse_url($value, PHP_URL_PATH) ?: $value;

        return (bool) preg_match('/\.(png|jpe?g|gif|webp|avif|svg)$/i', $path);
    }

    /** A gallery: a non-empty list made purely of design assets. */
    private static function isDesignAssetList(mixed $value): bool
    {
        if (! is_array($value) || $value === [] || ! array_is_list($value)) {
            return false;
        }

        foreach ($value as $item) {
            if (! self::isDesignAsset($item)) {
                return false;
            }
        }

        return true;
    }
}
