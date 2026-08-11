<?php

namespace App\Console\Commands;

use App\Support\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * Optimizes images that are already on disk — the two groups that the upload
 * pipeline (App\Filament\Forms\Components\WebImage) can't reach:
 *
 *  - the design assets that ship with the repo, which the seeders point at and
 *    which never pass through the admin at all;
 *  - anything uploaded before that pipeline existed, or uploaded when the size
 *    and quality settings were different.
 *
 * Files are rewritten in place, keeping name and format: seeders and components
 * reference the design assets by path, and upload paths are stored in the
 * database. That also makes the command safe to re-run — an already-optimized
 * file yields no further saving and is left alone.
 */
class OptimizeImages extends Command
{
    protected $signature = 'images:optimize
        {--path=* : Files or directories to optimize (defaults to the design assets and the uploads disk)}
        {--design : Only the frontend design assets}
        {--uploads : Only images uploaded through the admin}
        {--max-dimension= : Longest edge in pixels}
        {--quality= : Encoder quality, 1-100}
        {--dry-run : Report what would be saved without changing anything}';

    protected $description = 'Compress and downscale existing images for the web';

    /**
     * The design assets. The backend container mounts the frontend's public
     * directory (see docker-compose.yml); outside Docker the repo layout applies.
     */
    private const DESIGN_PATHS = [
        '/var/www/frontend-public/images',
        __DIR__.'/../../../../frontend/public/images',
    ];

    public function handle(ImageOptimizer $optimizer): int
    {
        $paths = $this->resolvePaths();

        if ($paths === []) {
            $this->error('No images found. Pass --path= to point at a directory or file.');

            return self::FAILURE;
        }

        $maxDimension = (int) ($this->option('max-dimension') ?: ImageOptimizer::MAX_DIMENSION);
        $quality = (int) ($this->option('quality') ?: ImageOptimizer::QUALITY);
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->comment('Dry run — no files will be changed.');
        }

        $files = $this->imagesIn($paths);

        $rows = [];
        $totalBefore = 0;
        $totalSaved = 0;
        $skipped = 0;

        $this->withProgressBar($files, function (string $file) use ($optimizer, $paths, $maxDimension, $quality, $dryRun, &$rows, &$totalBefore, &$totalSaved, &$skipped): void {
            $before = filesize($file) ?: 0;
            $saved = $optimizer->optimizeInPlace($file, $maxDimension, $quality, $dryRun);

            $totalBefore += $before;
            $totalSaved += $saved;

            if ($saved === 0) {
                $skipped++;

                return;
            }

            $rows[] = [
                $this->relativeName($file, $paths),
                $this->bytes($before),
                $this->bytes($before - $saved),
                sprintf('-%d%%', (int) round($saved / $before * 100)),
            ];
        });

        $this->newLine(2);

        if ($rows === []) {
            $this->info(sprintf('Nothing to do — all %d image(s) are already optimized.', count($files)));

            return self::SUCCESS;
        }

        // Biggest win first: that's the file worth looking at if something
        // turned out to be over-compressed.
        usort($rows, fn (array $a, array $b): int => (int) filter_var($a[3], FILTER_SANITIZE_NUMBER_INT) <=> (int) filter_var($b[3], FILTER_SANITIZE_NUMBER_INT));

        $this->table(['Image', 'Before', 'After', 'Saved'], $rows);

        $this->info(sprintf(
            '%s %s across %d image(s) — %s to %s (-%d%%). %d already optimal.',
            $dryRun ? 'Would save' : 'Saved',
            $this->bytes($totalSaved),
            count($rows),
            $this->bytes($totalBefore),
            $this->bytes($totalBefore - $totalSaved),
            $totalBefore > 0 ? (int) round($totalSaved / $totalBefore * 100) : 0,
            $skipped,
        ));

        if ($dryRun) {
            $this->comment('Re-run without --dry-run to apply.');
        }

        return self::SUCCESS;
    }

    /**
     * Explicit --path wins; otherwise the design assets and everything already
     * uploaded through the admin, unless one of them is asked for on its own.
     *
     * @return array<int, string>
     */
    private function resolvePaths(): array
    {
        $paths = (array) $this->option('path');

        if ($paths === []) {
            $design = (bool) $this->option('design');
            $uploads = (bool) $this->option('uploads');

            // Neither flag means both.
            $wantsBoth = $design === $uploads;

            if ($design || $wantsBoth) {
                foreach (self::DESIGN_PATHS as $candidate) {
                    if (is_dir($candidate)) {
                        $paths[] = $candidate;

                        break;
                    }
                }
            }

            if ($uploads || $wantsBoth) {
                // Uploads keep their format and name here: the paths are stored
                // in the database, so renaming would orphan every reference.
                // New uploads arrive as WebP already (see WebImage); this is for
                // everything saved before that, and for re-runs after a change
                // to the size or quality settings.
                $disk = Storage::disk('public')->path('');

                if (is_dir($disk)) {
                    $paths[] = rtrim($disk, '/');
                }
            }
        }

        return array_values(array_filter($paths, function (string $path): bool {
            if (file_exists($path)) {
                return true;
            }

            $this->warn("Skipping {$path} — no such file or directory.");

            return false;
        }));
    }

    /**
     * @param  array<int, string>  $paths
     * @return array<int, string>
     */
    private function imagesIn(array $paths): array
    {
        $files = [];
        $directories = [];

        foreach ($paths as $path) {
            is_dir($path) ? $directories[] = $path : $files[] = $path;
        }

        if ($directories !== []) {
            // SVG is absent on purpose: it is already the smallest form of
            // itself and GD would only rasterise it.
            $finder = (new Finder)->files()->in($directories)->name('/\.(png|jpe?g|gif|webp)$/i');

            foreach ($finder as $file) {
                /** @var SplFileInfo $file */
                $files[] = $file->getRealPath();
            }
        }

        sort($files);

        return $files;
    }

    /**
     * Name each file relative to the root it was found under, so the report
     * reads "diensten/legacy.jpg" rather than an absolute path that is mostly
     * container layout — and stays right whatever the disk is configured to.
     *
     * @param  array<int, string>  $roots
     */
    private function relativeName(string $path, array $roots): string
    {
        foreach ($roots as $root) {
            $prefix = rtrim($root, '/').'/';

            if (str_starts_with($path, $prefix)) {
                return substr($path, strlen($prefix));
            }
        }

        return basename($path);
    }

    private function bytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return "{$bytes} B";
        }

        $units = ['KB', 'MB', 'GB'];
        $value = $bytes / 1024;
        $unit = 0;

        while ($value >= 1024 && $unit < count($units) - 1) {
            $value /= 1024;
            $unit++;
        }

        return sprintf('%.1f %s', $value, $units[$unit]);
    }
}
