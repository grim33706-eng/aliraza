<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Stores admin uploads on the public disk and returns a path relative to
 * public/ (e.g. "storage/uploads/2026/10/abc.webp") so views can use asset().
 * Images are resized and re-encoded as WebP; videos are stored as-is.
 */
class MediaStore
{
    public const IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    public const VIDEO_MIMES = ['video/mp4', 'video/webm'];

    /**
     * @return array{path: string, width: ?int, height: ?int}
     */
    public function storeImage(UploadedFile $file, int $maxWidth = 2000, bool $square = false): array
    {
        if (! in_array($file->getMimeType(), self::IMAGE_MIMES, true)) {
            throw new RuntimeException('Images must be JPG, PNG, WebP or GIF.');
        }

        $source = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));
        if (! $source) {
            throw new RuntimeException('That image could not be read. Try another file.');
        }

        [$w, $h] = [imagesx($source), imagesy($source)];
        if ($square) {
            // Centre-crop to a square (profile photos).
            $side = min($w, $h);
            $size = min($side, $maxWidth);
            $image = imagecreatetruecolor($size, $size);
            imagecopyresampled($image, $source, 0, 0, intdiv($w - $side, 2), intdiv($h - $side, 2), $size, $size, $side, $side);
        } elseif ($w > $maxWidth) {
            $image = imagescale($source, $maxWidth, (int) round($h * $maxWidth / $w), IMG_BICUBIC);
        } else {
            $image = $source;
        }
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $relative = $this->directory().'/'.Str::random(24).'.webp';
        ob_start();
        imagewebp($image, null, 82);
        Storage::disk('public')->put($relative, (string) ob_get_clean());

        return ['path' => 'storage/'.$relative, 'width' => imagesx($image), 'height' => imagesy($image)];
    }

    public function storeVideo(UploadedFile $file): string
    {
        if (! in_array($file->getMimeType(), self::VIDEO_MIMES, true)) {
            throw new RuntimeException('Videos must be MP4 or WebM.');
        }

        $name = Str::random(24).'.'.($file->getMimeType() === 'video/webm' ? 'webm' : 'mp4');

        return 'storage/'.$file->storeAs($this->directory(), $name, 'public');
    }

    /** Delete a previous upload (never the bundled images/videos). */
    public function delete(?string $path): void
    {
        if ($path && str_starts_with($path, 'storage/uploads/')) {
            Storage::disk('public')->delete(Str::after($path, 'storage/'));
        }
    }

    /** Largest upload PHP will accept, in bytes (smaller of the two ini limits). */
    public static function maxUploadBytes(): int
    {
        $toBytes = function (string $value): int {
            $number = (int) $value;

            return match (strtolower(substr(trim($value), -1))) {
                'g' => $number * 1024 ** 3,
                'm' => $number * 1024 ** 2,
                'k' => $number * 1024,
                default => $number,
            };
        };

        return min($toBytes((string) ini_get('upload_max_filesize')), $toBytes((string) ini_get('post_max_size')));
    }

    private function directory(): string
    {
        return 'uploads/'.now()->format('Y/m');
    }
}
