<?php

namespace App\Support;

class ResponsiveImage
{
    public static function build(?string $src, array $widths = [], ?string $sizes = null): array
    {
        $fallback = [
            'src' => $src,
            'fallback_srcset' => null,
            'webp_srcset' => null,
            'sizes' => $sizes,
            'width' => null,
            'height' => null,
        ];

        if (!$src || !str_starts_with($src, '/storage/')) {
            return $fallback;
        }

        $publicPath = public_path(ltrim($src, '/'));
        if (!is_file($publicPath)) {
            return $fallback;
        }

        $dimensions = @getimagesize($publicPath);
        if (!$dimensions) {
            return $fallback;
        }

        [$originalWidth, $originalHeight] = $dimensions;
        $extension = self::extensionFromMime($dimensions['mime'] ?? null);
        if (!$extension) {
            return $fallback;
        }

        $normalizedWidths = collect($widths)
            ->map(fn ($width) => (int) $width)
            ->filter(fn ($width) => $width > 0 && $width < $originalWidth)
            ->unique()
            ->sort()
            ->values()
            ->all();

        if (empty($normalizedWidths)) {
            return array_merge($fallback, [
                'width' => $originalWidth,
                'height' => $originalHeight,
            ]);
        }

        $directory = public_path('storage/cache/responsive');
        if (!is_dir($directory)) {
            @mkdir($directory, 0755, true);
        }

        $basename = pathinfo($publicPath, PATHINFO_FILENAME);
        $signature = substr(md5($publicPath.'|'.filemtime($publicPath)), 0, 12);
        $fallbackSources = [];
        $webpSources = [];

        foreach ($normalizedWidths as $targetWidth) {
            $relativeBase = sprintf('/storage/cache/responsive/%s-%s-%d', $basename, $signature, $targetWidth);
            $fallbackVariant = public_path(ltrim($relativeBase.'.'.$extension, '/'));
            $webpVariant = public_path(ltrim($relativeBase.'.webp', '/'));

            self::ensureVariant($publicPath, $fallbackVariant, $targetWidth, $extension, 82);
            self::ensureVariant($publicPath, $webpVariant, $targetWidth, 'webp', 78);

            if (is_file($fallbackVariant)) {
                $fallbackSources[] = $relativeBase.'.'.$extension.' '.$targetWidth.'w';
            }

            if (is_file($webpVariant)) {
                $webpSources[] = $relativeBase.'.webp '.$targetWidth.'w';
            }
        }

        $bestWidth = end($normalizedWidths) ?: $originalWidth;
        $bestSrc = !empty($fallbackSources)
            ? sprintf('/storage/cache/responsive/%s-%s-%d.%s', $basename, $signature, $bestWidth, $extension)
            : $src;

        return [
            'src' => $bestSrc,
            'fallback_srcset' => !empty($fallbackSources) ? implode(', ', $fallbackSources) : null,
            'webp_srcset' => !empty($webpSources) ? implode(', ', $webpSources) : null,
            'sizes' => $sizes,
            'width' => $originalWidth,
            'height' => $originalHeight,
        ];
    }

    private static function ensureVariant(string $sourcePath, string $targetPath, int $targetWidth, string $format, int $quality): void
    {
        if (is_file($targetPath) && filemtime($targetPath) >= filemtime($sourcePath)) {
            return;
        }

        $sourceImage = @imagecreatefromstring(file_get_contents($sourcePath));
        if (!$sourceImage) {
            return;
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        if ($sourceWidth <= 0 || $sourceHeight <= 0) {
            imagedestroy($sourceImage);
            return;
        }

        $targetHeight = max(1, (int) round(($targetWidth / $sourceWidth) * $sourceHeight));
        $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if (in_array($format, ['png', 'webp'], true)) {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
            imagefilledrectangle($resizedImage, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        imagecopyresampled(
            $resizedImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight
        );

        @mkdir(dirname($targetPath), 0755, true);

        match ($format) {
            'jpg', 'jpeg' => imagejpeg($resizedImage, $targetPath, $quality),
            'png' => imagepng($resizedImage, $targetPath, 8),
            'webp' => imagewebp($resizedImage, $targetPath, $quality),
            'gif' => imagegif($resizedImage, $targetPath),
            default => null,
        };

        imagedestroy($resizedImage);
        imagedestroy($sourceImage);
    }

    private static function extensionFromMime(?string $mime): ?string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => null,
        };
    }
}
