<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OptimizeImagesCommandTest extends TestCase
{
    /** @var array<int, string> */
    private array $temporaryDirectories = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryDirectories as $directory) {
            foreach (glob($directory.'/*') ?: [] as $file) {
                @unlink($file);
            }

            @rmdir($directory);
        }

        parent::tearDown();
    }

    public function test_it_shrinks_the_images_under_a_given_path(): void
    {
        $directory = $this->directoryWithPhoto('big.jpg');
        $before = filesize($directory.'/big.jpg');

        $this->artisan('images:optimize', ['--path' => [$directory]])
            ->assertSuccessful();

        clearstatcache();

        $this->assertLessThan($before, filesize($directory.'/big.jpg'));
    }

    public function test_a_dry_run_leaves_the_files_alone(): void
    {
        $directory = $this->directoryWithPhoto('big.jpg');
        $before = file_get_contents($directory.'/big.jpg');

        $this->artisan('images:optimize', ['--path' => [$directory], '--dry-run' => true])
            ->expectsOutputToContain('Dry run')
            ->assertSuccessful();

        $this->assertSame($before, file_get_contents($directory.'/big.jpg'));
    }

    public function test_it_ignores_svgs_so_vector_assets_are_never_rasterised(): void
    {
        $directory = $this->directoryWithPhoto('big.jpg');
        $svg = $directory.'/icon.svg';
        file_put_contents($svg, '<svg xmlns="http://www.w3.org/2000/svg"><rect width="10" height="10"/></svg>');
        $before = file_get_contents($svg);

        $this->artisan('images:optimize', ['--path' => [$directory]])->assertSuccessful();

        $this->assertSame($before, file_get_contents($svg));
    }

    public function test_the_uploads_flag_reaches_images_uploaded_through_the_admin(): void
    {
        Storage::fake('public');

        $path = Storage::disk('public')->path('diensten');
        mkdir($path, 0777, true);
        $this->writePhoto($path.'/legacy.jpg');
        $before = filesize($path.'/legacy.jpg');

        $this->artisan('images:optimize', ['--uploads' => true])
            ->expectsOutputToContain('diensten/legacy.jpg')
            ->assertSuccessful();

        clearstatcache();

        $this->assertLessThan($before, filesize($path.'/legacy.jpg'));
        $this->assertSame(
            'image/jpeg',
            getimagesize($path.'/legacy.jpg')['mime'],
            'Upload paths are stored in the database, so the format must not change.',
        );
    }

    public function test_it_fails_when_the_given_path_does_not_exist(): void
    {
        $this->artisan('images:optimize', ['--path' => ['/no/such/directory']])
            ->expectsOutputToContain('No images found')
            ->assertFailed();
    }

    private function directoryWithPhoto(string $name): string
    {
        $directory = sys_get_temp_dir().'/imgcmd_'.bin2hex(random_bytes(6));
        mkdir($directory);

        $this->temporaryDirectories[] = $directory;

        $this->writePhoto($directory.'/'.$name);

        return $directory;
    }

    /** Oversized and photo-like, so there is a real saving to measure. */
    private function writePhoto(string $path): void
    {
        $image = imagecreatetruecolor(3200, 2400);

        for ($i = 0; $i < 120; $i++) {
            imagefilledellipse(
                $image,
                random_int(0, 3200),
                random_int(0, 2400),
                random_int(40, 900),
                random_int(40, 900),
                imagecolorallocate($image, random_int(0, 255), random_int(0, 255), random_int(0, 255)),
            );
        }

        imagejpeg($image, $path, 95);
        imagedestroy($image);
    }
}
