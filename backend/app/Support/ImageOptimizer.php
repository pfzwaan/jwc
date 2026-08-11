<?php

namespace App\Support;

use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Makes an image web-friendly: correctly rotated, no larger than it needs to be,
 * stripped of camera metadata and encoded with a modern codec.
 *
 * Two callers, one pipeline:
 *   - Admin uploads (see App\Filament\Forms\Components\WebImage) convert to WebP,
 *     because nothing in the CMS needs the original format preserved.
 *   - The images:optimize command keeps each file's format, because the design
 *     assets are referenced by name from seeders and components.
 *
 * GD decides the pixels; the CLI recompressors (pngquant, optipng, jpegoptim)
 * squeeze the bytes. Every CLI step is optional — if the binary is missing the
 * GD output is used as-is, so this degrades rather than fails.
 */
class ImageOptimizer
{
    /**
     * Nothing in the design is displayed wider than ~1400 CSS pixels, so 2560
     * still covers a 2× retina hero while cutting a modern phone photo
     * (4000 px+) down by more than half in each dimension.
     */
    public const MAX_DIMENSION = 2560;

    public const QUALITY = 82;

    /** Formats GD can decode here. SVG is deliberately absent — see optimize(). */
    private const RASTER_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Optimize $source and write the result to $target.
     *
     * @param  string|null  $format  'webp', 'png', 'jpeg' — null keeps the source format.
     * @return string|null The format actually written, or null when the file was
     *                     deliberately left alone (vector, animation, not an image).
     */
    public function optimize(
        string $source,
        string $target,
        ?string $format = null,
        int $maxDimension = self::MAX_DIMENSION,
        int $quality = self::QUALITY,
    ): ?string {
        $mime = $this->mimeOf($source);

        // SVG is already the smallest possible form of itself, and rasterising
        // it would be a downgrade. Animated GIFs lose every frame but the first
        // if GD touches them. Both pass through untouched.
        if (! in_array($mime, self::RASTER_MIMES, true) || $this->isAnimatedGif($source, $mime)) {
            return null;
        }

        $image = $this->decode($source, $mime);

        if ($image === null) {
            return null;
        }

        try {
            $image = $this->autoOrient($image, $source, $mime);
            $image = $this->downscale($image, $maxDimension);

            $format ??= $this->formatFor($mime);

            $this->encode($image, $target, $format, $quality);
        } finally {
            imagedestroy($image);
        }

        $this->recompress($target, $format, $quality);

        return $format;
    }

    /**
     * Optimize a file where it lies, keeping its format and name — the design
     * assets are referenced by name from seeders and components, so renaming
     * them would mean chasing every reference.
     *
     * @param  bool  $dryRun  Measure the saving without touching the file.
     * @return int Bytes saved (0 when the file was skipped or got no smaller).
     */
    public function optimizeInPlace(
        string $path,
        int $maxDimension = self::MAX_DIMENSION,
        int $quality = self::QUALITY,
        bool $dryRun = false,
    ): int {
        $before = filesize($path);

        $temp = $this->temporaryPath();

        try {
            if ($this->optimize($path, $temp, null, $maxDimension, $quality) === null) {
                return 0;
            }

            $after = filesize($temp);

            // Already well-compressed files can come out bigger, and running the
            // command twice shouldn't degrade anything. Keep the original.
            if ($after === false || $before === false || $after >= $before) {
                return 0;
            }

            if (! $dryRun && ! copy($temp, $path)) {
                throw new RuntimeException("Could not write optimized image to {$path}.");
            }

            return $before - $after;
        } finally {
            @unlink($temp);
        }
    }

    public function temporaryPath(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'imgopt_');

        if ($path === false) {
            throw new RuntimeException('Could not create a temporary file for image optimization.');
        }

        return $path;
    }

    private function mimeOf(string $path): ?string
    {
        // getimagesize() sniffs the actual bytes, so a mislabelled extension
        // can't push a file down the wrong branch.
        $info = @getimagesize($path);

        return $info['mime'] ?? null;
    }

    private function formatFor(string $mime): string
    {
        return match ($mime) {
            'image/jpeg' => 'jpeg',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'png',
        };
    }

    /** @return \GdImage|null */
    private function decode(string $path, string $mime)
    {
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/webp' => @imagecreatefromwebp($path),
            default => false,
        };

        return $image === false ? null : $image;
    }

    /**
     * Phone cameras store the picture sideways plus an EXIF orientation flag.
     * Stripping metadata without applying it first would leave the image
     * rotated, so this has to happen before encoding.
     *
     * @param  \GdImage  $image
     * @return \GdImage
     */
    private function autoOrient($image, string $path, string $mime)
    {
        if ($mime !== 'image/jpeg' || ! function_exists('exif_read_data')) {
            return $image;
        }

        // exif_read_data() returns false for a JPEG carrying no EXIF block at
        // all, so this can't be dereferenced directly.
        $exif = @exif_read_data($path);
        $orientation = is_array($exif) ? ($exif['Orientation'] ?? null) : null;

        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => null,
        };

        if ($rotated === null || $rotated === false) {
            return $image;
        }

        imagedestroy($image);

        return $rotated;
    }

    /**
     * @param  \GdImage  $image
     * @return \GdImage
     */
    private function downscale($image, int $maxDimension)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $longest = max($width, $height);

        if ($longest <= $maxDimension) {
            return $image;
        }

        $ratio = $maxDimension / $longest;
        $targetWidth = max(1, (int) round($width * $ratio));
        $targetHeight = max(1, (int) round($height * $ratio));

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);

        // Without this a transparent PNG resamples onto an opaque black canvas.
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        imagedestroy($image);

        return $resized;
    }

    /** @param  \GdImage  $image */
    private function encode($image, string $target, string $format, int $quality): void
    {
        // Re-assert alpha for images that were small enough to skip downscale().
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $written = match ($format) {
            'jpeg' => $this->encodeJpeg($image, $target, $quality),
            'webp' => imagewebp($image, $target, $quality),
            'gif' => imagegif($image, $target),
            default => imagepng($image, $target, 9),
        };

        if ($written === false) {
            throw new RuntimeException("GD could not encode the image as {$format}. Is the extension built with support for it?");
        }
    }

    /** @param  \GdImage  $image */
    private function encodeJpeg($image, string $target, int $quality): bool
    {
        // JPEG has no alpha channel: anything transparent would encode as black,
        // so composite onto white first.
        $canvas = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopy($canvas, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

        $written = imagejpeg($canvas, $target, $quality);

        imagedestroy($canvas);

        return $written;
    }

    /**
     * Hand the encoded file to a specialist. pngquant's palette quantisation is
     * the single biggest win available (routinely 3–4×) and is visually lossless
     * at these settings; optipng then squeezes the deflate stream losslessly.
     */
    private function recompress(string $path, string $format, int $quality): void
    {
        match ($format) {
            'png' => $this->recompressPng($path, $quality),
            'jpeg' => $this->run(['jpegoptim', '--strip-all', '--all-progressive', "--max={$quality}", $path]),
            default => null,
        };
    }

    private function recompressPng(string $path, int $quality): void
    {
        // --skip-if-larger leaves the file alone rather than writing a worse one.
        $this->run([
            'pngquant', '--force', '--skip-if-larger', '--strip',
            '--quality='.max(0, $quality - 20)."-{$quality}",
            '--output', $path, '--', $path,
        ]);

        $this->run(['optipng', '-quiet', '-strip', 'all', '-o2', $path]);
    }

    /** Best-effort: a missing binary or a non-zero exit leaves the file as it was. */
    private function run(array $command): void
    {
        try {
            (new Process($command))->setTimeout(120)->run();
        } catch (\Throwable) {
            // Optimization is never worth failing an upload over.
        }
    }

    /**
     * A GIF is animated when it holds more than one Graphic Control Extension.
     * Cheaper and more reliable than decoding the whole thing.
     */
    private function isAnimatedGif(string $path, ?string $mime): bool
    {
        if ($mime !== 'image/gif') {
            return false;
        }

        $contents = @file_get_contents($path);

        return $contents !== false && substr_count($contents, "\x00\x21\xF9\x04") > 1;
    }
}
