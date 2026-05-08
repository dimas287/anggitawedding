<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize
                            {--disk=public : Storage disk to use}
                            {--quality=82 : WebP quality (1-100)}
                            {--max-width=1200 : Max width in pixels (0 to skip resize)}
                            {--max-height=1200 : Max height in pixels (0 to skip resize)}
                            {--folders=* : Specific folders to process (empty = all)}
                            {--dry-run : Show what would be optimized without doing it}';

    protected $description = 'Optimize and compress images in storage to WebP format';

    public function handle(): int
    {
        $disk      = $this->option('disk');
        $quality   = (int) $this->option('quality');
        $maxW      = (int) $this->option('max-width');
        $maxH      = (int) $this->option('max-height');
        $folders   = $this->option('folders') ?: [];
        $dryRun    = $this->option('dry-run');

        $manager = new ImageManager(new Driver());

        // Build list of folders to scan
        if (empty($folders)) {
            $folders = ['landing', 'hero-slides', 'templates/thumbnails', 'brand', 'highlights'];
        }

        $this->info('🔍 Scanning storage/' . $disk . ' for large images...');
        $this->newLine();

        $totalSaved   = 0;
        $totalFiles   = 0;
        $skippedFiles = 0;

        foreach ($folders as $folder) {
            if (!Storage::disk($disk)->exists($folder)) {
                $this->warn("  Folder [$folder] not found — skipping.");
                continue;
            }

            $files = Storage::disk($disk)->files($folder);

            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    continue;
                }

                $sizeBefore = Storage::disk($disk)->size($file);

                // Skip tiny images (< 50 KB)
                if ($sizeBefore < 50 * 1024) {
                    $skippedFiles++;
                    continue;
                }

                $webpPath = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $file);

                // Skip if WebP already exists and is recent
                if (Storage::disk($disk)->exists($webpPath) &&
                    Storage::disk($disk)->lastModified($webpPath) >= Storage::disk($disk)->lastModified($file)) {
                    $skippedFiles++;
                    continue;
                }

                $this->line("  <comment>$file</comment> (" . number_format($sizeBefore / 1024, 1) . ' KB)');

                if ($dryRun) {
                    $this->line('    → [DRY RUN] Would convert to WebP');
                    continue;
                }

                try {
                    $absolutePath = Storage::disk($disk)->path($file);
                    $img = $manager->read($absolutePath);

                    // Resize if needed
                    if ($maxW > 0 || $maxH > 0) {
                        $origW = $img->width();
                        $origH = $img->height();

                        if (($maxW > 0 && $origW > $maxW) || ($maxH > 0 && $origH > $maxH)) {
                            $img->scaleDown(
                                $maxW > 0 ? $maxW : null,
                                $maxH > 0 ? $maxH : null
                            );
                        }
                    }

                    // Encode to WebP
                    $encoded = $img->toWebp($quality);
                    Storage::disk($disk)->put($webpPath, $encoded->toString());

                    $sizeAfter = Storage::disk($disk)->size($webpPath);
                    $saved     = $sizeBefore - $sizeAfter;
                    $pct       = $sizeBefore > 0 ? round($saved / $sizeBefore * 100) : 0;

                    $totalSaved += $saved;
                    $totalFiles++;

                    $this->line("    → <info>$webpPath</info> (" . number_format($sizeAfter / 1024, 1) . " KB) <comment>-$pct%</comment>");

                } catch (\Throwable $e) {
                    $this->error("    ✗ Failed: " . $e->getMessage());
                }
            }
        }

        $this->newLine();
        $this->info("✅ Done! Converted $totalFiles images. Skipped $skippedFiles.");
        $this->info('💾 Total saved: ' . number_format($totalSaved / 1024, 1) . ' KB (' . number_format($totalSaved / 1024 / 1024, 2) . ' MB)');

        return self::SUCCESS;
    }
}
