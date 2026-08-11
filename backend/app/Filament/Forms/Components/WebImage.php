<?php

namespace App\Filament\Forms\Components;

use App\Support\ImageOptimizer;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Illuminate\Http\File;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * A FileUpload that guarantees whatever staff drop in comes back out
 * web-friendly: rotated the right way up, capped at a sane resolution, stripped
 * of camera metadata and re-encoded as WebP.
 *
 * Use this instead of FileUpload for every *image* field in the admin. Straight
 * off a phone, a hero photo lands as a 6 MB, 4032 px JPEG; through here it's a
 * couple of hundred KB at 2560 px, with no action needed from the person
 * uploading it.
 *
 * Non-raster uploads (SVG logos, animated GIFs) are stored untouched — see
 * ImageOptimizer for why. Video belongs on a plain FileUpload; this class
 * accepts images only.
 */
class WebImage extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->image();

        $this->helperText('Wordt automatisch verkleind en geoptimaliseerd voor het web.');

        $this->saveUploadedFileUsing(static function (BaseFileUpload $component, TemporaryUploadedFile $file): ?string {
            return self::store($component, $file);
        });
    }

    private static function store(BaseFileUpload $component, TemporaryUploadedFile $file): ?string
    {
        $source = $file->getRealPath();

        // A remote temporary-upload disk has no local path to read. Nothing to
        // optimize from, so store the original rather than lose the upload.
        if ($source === false || ! is_readable($source)) {
            return $component->saveUploadedFile($file);
        }

        $optimizer = app(ImageOptimizer::class);
        $target = $optimizer->temporaryPath();

        try {
            $format = $optimizer->optimize($source, $target, 'webp');

            // SVG, animated GIF, or something GD couldn't read: keep the original.
            if ($format === null) {
                return $component->saveUploadedFile($file);
            }

            $name = self::webpName($component->getUploadedFileNameForStorage($file));

            $path = $component->getDisk()->putFileAs(
                $component->getDirectory(),
                new File($target),
                $name,
                $component->getVisibility(),
            );

            return $path === false ? null : $path;
        } finally {
            @unlink($target);
        }
    }

    /**
     * Keep the app's own naming policy (Filament hands us a ULID by default) and
     * only correct the extension, so the stored name never lies about its format.
     */
    private static function webpName(string $name): string
    {
        return preg_replace('/\.[^.\/]*$/', '', $name).'.webp';
    }
}
