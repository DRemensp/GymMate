<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateIosPwa extends Command
{
    protected $signature   = 'pwa:generate-ios';
    protected $description = 'Generate iOS PWA splash screens and 180px icon from public/icons/icon-512.png';

    // [width, height] in pixels (portrait, @physical resolution)
    const SPLASHES = [
        [1320, 2868], // iPhone 16 Pro Max
        [1206, 2622], // iPhone 16 Pro
        [1290, 2796], // iPhone 16 Plus / 15 Pro Max / 15 Plus / 14 Pro Max
        [1179, 2556], // iPhone 16 / 15 Pro / 15 / 14 Pro
        [1284, 2778], // iPhone 14 Plus / 13 Pro Max / 12 Pro Max
        [1170, 2532], // iPhone 13 / 13 Pro / 12 / 12 Pro
        [1125, 2436], // iPhone 13 mini / 12 mini / SE 3rd / X / XS / 11 Pro
        [1242, 2688], // iPhone 11 Pro Max / XS Max
        [828,  1792], // iPhone 11 / XR
        [1242, 2208], // iPhone 8 Plus / 7 Plus / 6s Plus
        [750,  1334], // iPhone SE 2nd / 8 / 7 / 6s
        [640,  1136], // iPhone SE 1st gen
        [1536, 2048], // iPad mini / Air / 9.7" / 10.2"
        [1668, 2224], // iPad Pro 10.5" / Air 10.9"
        [1668, 2388], // iPad Pro 11"
        [2048, 2732], // iPad Pro 12.9"
    ];

    public function handle(): int
    {
        if (!function_exists('imagecreatetruecolor')) {
            $this->error('PHP GD extension is not enabled. Enable it in php.ini and retry.');
            return self::FAILURE;
        }

        $srcPath = public_path('icons/icon-512.png');
        if (!file_exists($srcPath)) {
            $this->error("Source icon not found: {$srcPath}");
            return self::FAILURE;
        }

        $src = imagecreatefrompng($srcPath);
        imagesavealpha($src, true);

        // 180×180 apple-touch-icon
        $this->makeIcon($src, 180, public_path('icons/icon-180.png'));
        $this->info('✓ icon-180.png');

        // Splash screens
        $splashDir = public_path('icons/splash');
        if (!is_dir($splashDir)) {
            mkdir($splashDir, 0755, true);
        }

        foreach (self::SPLASHES as [$w, $h]) {
            $this->makeSplash($src, $w, $h, "{$splashDir}/splash-{$w}x{$h}.png");
            $this->info("✓ splash-{$w}x{$h}.png");
        }

        imagedestroy($src);
        $this->info("\nAll iOS PWA assets generated in public/icons/");
        return self::SUCCESS;
    }

    private function makeIcon($src, int $size, string $dest): void
    {
        $img = imagecreatetruecolor($size, $size);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparent);
        imagecopyresampled($img, $src, 0, 0, 0, 0, $size, $size, imagesx($src), imagesy($src));
        imagepng($img, $dest, 6);
        imagedestroy($img);
    }

    private function makeSplash($src, int $w, int $h, string $dest): void
    {
        $img     = imagecreatetruecolor($w, $h);
        $bgColor = imagecolorallocate($img, 9, 9, 11); // #09090b
        imagefill($img, 0, 0, $bgColor);

        // Icon centred, ~25% of the shorter edge
        $iconSize = (int)(min($w, $h) * 0.25);
        $x = (int)(($w - $iconSize) / 2);
        $y = (int)(($h - $iconSize) / 2);
        imagecopyresampled($img, $src, $x, $y, 0, 0, $iconSize, $iconSize, imagesx($src), imagesy($src));

        imagepng($img, $dest, 6);
        imagedestroy($img);
    }
}
