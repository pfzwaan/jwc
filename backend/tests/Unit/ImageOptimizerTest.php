<?php

namespace Tests\Unit;

use App\Support\ImageOptimizer;
use PHPUnit\Framework\TestCase;

class ImageOptimizerTest extends TestCase
{
    private ImageOptimizer $optimizer;

    /** @var array<int, string> */
    private array $temporaryFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->optimizer = new ImageOptimizer;
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    public function test_it_downscales_an_oversized_photo_and_converts_it_to_webp(): void
    {
        $source = $this->jpeg(4032, 3024);
        $target = $this->temporaryFile();

        $format = $this->optimizer->optimize($source, $target, 'webp');

        $this->assertSame('webp', $format);

        $size = getimagesize($target);

        $this->assertSame('image/webp', $size['mime']);
        $this->assertSame(ImageOptimizer::MAX_DIMENSION, $size[0]);
        $this->assertSame(1920, $size[1], 'The aspect ratio must be preserved.');
        $this->assertLessThan(filesize($source), filesize($target));
    }

    public function test_it_leaves_images_that_are_already_small_enough_at_their_own_size(): void
    {
        $source = $this->jpeg(800, 600);
        $target = $this->temporaryFile();

        $this->optimizer->optimize($source, $target, 'webp');

        [$width, $height] = getimagesize($target);

        $this->assertSame(800, $width);
        $this->assertSame(600, $height);
    }

    public function test_it_keeps_transparency_when_converting_a_png(): void
    {
        $source = $this->transparentPng();
        $target = $this->temporaryFile();

        $this->optimizer->optimize($source, $target, 'webp');

        $image = imagecreatefromwebp($target);
        $alpha = (imagecolorat($image, 5, 5) >> 24) & 0x7F;
        imagedestroy($image);

        $this->assertSame(127, $alpha, 'The transparent corner must stay fully transparent.');
    }

    public function test_it_refuses_to_touch_an_svg(): void
    {
        $source = $this->temporaryFile();
        file_put_contents($source, '<svg xmlns="http://www.w3.org/2000/svg"><rect width="10" height="10"/></svg>');
        $before = file_get_contents($source);

        $this->assertNull($this->optimizer->optimize($source, $this->temporaryFile(), 'webp'));
        $this->assertSame(0, $this->optimizer->optimizeInPlace($source));
        $this->assertSame($before, file_get_contents($source), 'A vector file must be passed through byte-for-byte.');
    }

    public function test_it_refuses_to_touch_an_animated_gif(): void
    {
        // Two Graphic Control Extension blocks — enough to read as animated.
        $source = $this->temporaryFile();
        file_put_contents($source, "GIF89a\x0A\x00\x0A\x00\x00\x00\x00\x00\x21\xF9\x04\x00\x00\x00\x00\x00\x21\xF9\x04\x00\x00\x00\x00\x00\x3B");

        $this->assertNull($this->optimizer->optimize($source, $this->temporaryFile(), 'webp'));
    }

    public function test_it_ignores_a_file_that_is_not_an_image(): void
    {
        $source = $this->temporaryFile();
        file_put_contents($source, 'this is not an image');

        $this->assertNull($this->optimizer->optimize($source, $this->temporaryFile(), 'webp'));
    }

    public function test_optimize_in_place_shrinks_the_file_but_keeps_its_format(): void
    {
        $source = $this->jpeg(4032, 3024);
        $before = filesize($source);

        $saved = $this->optimizer->optimizeInPlace($source);

        $this->assertGreaterThan(0, $saved);
        $this->assertSame($before - $saved, filesize($source));
        $this->assertSame('image/jpeg', getimagesize($source)['mime'], 'Seeders reference these by name — the format must not change.');
        $this->assertSame(ImageOptimizer::MAX_DIMENSION, getimagesize($source)[0]);
    }

    public function test_a_dry_run_reports_the_saving_without_writing(): void
    {
        $source = $this->jpeg(4032, 3024);
        $before = file_get_contents($source);

        $saved = $this->optimizer->optimizeInPlace($source, dryRun: true);

        $this->assertGreaterThan(0, $saved);
        $this->assertSame($before, file_get_contents($source));
    }

    public function test_running_it_twice_does_not_shrink_the_file_further(): void
    {
        $source = $this->jpeg(4032, 3024);

        $this->optimizer->optimizeInPlace($source);
        $size = filesize($source);

        $this->assertSame(0, $this->optimizer->optimizeInPlace($source));
        $this->assertSame($size, filesize($source), 'A second pass must leave an optimized file alone.');
    }

    /**
     * Downscaling replaces hard edges with antialiased ones, which PNG encodes
     * worse than the crisp original — so a flat graphic can come out bigger.
     * The original has to win in that case, or the command would inflate files.
     */
    public function test_it_keeps_the_original_when_optimizing_would_make_it_bigger(): void
    {
        $source = $this->png(3000, 2000);
        $before = file_get_contents($source);

        $this->assertSame(0, $this->optimizer->optimizeInPlace($source));
        $this->assertSame($before, file_get_contents($source));
    }

    private function jpeg(int $width, int $height): string
    {
        $path = $this->temporaryFile();

        imagejpeg($this->noise($width, $height), $path, 95);

        return $path;
    }

    private function png(int $width, int $height): string
    {
        $path = $this->temporaryFile();

        imagepng($this->noise($width, $height), $path);

        return $path;
    }

    private function transparentPng(): string
    {
        $path = $this->temporaryFile();

        $image = imagecreatetruecolor(100, 100);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
        imagefilledrectangle($image, 40, 40, 90, 90, imagecolorallocate($image, 255, 0, 0));
        imagepng($image, $path);
        imagedestroy($image);

        return $path;
    }

    /**
     * Photo-like content: a flat colour would compress to nothing and make the
     * size assertions meaningless.
     *
     * @return \GdImage
     */
    private function noise(int $width, int $height)
    {
        $image = imagecreatetruecolor($width, $height);

        for ($i = 0; $i < 150; $i++) {
            imagefilledellipse(
                $image,
                random_int(0, $width),
                random_int(0, $height),
                random_int(20, (int) ($width / 3)),
                random_int(20, (int) ($height / 3)),
                imagecolorallocate($image, random_int(0, 255), random_int(0, 255), random_int(0, 255)),
            );
        }

        return $image;
    }

    private function temporaryFile(): string
    {
        return $this->temporaryFiles[] = $this->optimizer->temporaryPath();
    }
}
