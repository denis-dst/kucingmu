<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCompressionService
{
    /**
     * Target maximum file size in kilobytes (default: 200 KB = 204,800 bytes).
     */
    public const TARGET_MAX_KB = 200;

    /**
     * Compress and store an image from any source (UploadedFile, base64 data URL, binary string, or path).
     * 
     * @param mixed $input UploadedFile|string
     * @param string $directory Target storage folder (e.g. 'cats', 'ptma-census', 'stray-surveys', 'biometrics')
     * @param string $disk Target storage disk (default: 'public')
     * @param int $maxSizeKb Maximum target file size in KB (default: 200)
     * @return string|null Relative storage path (e.g. 'cats/67c2a1b2c3d4e.jpg') or null on failure
     */
    public static function compressAndStore(
        mixed $input,
        string $directory = 'cats',
        string $disk = 'public',
        int $maxSizeKb = self::TARGET_MAX_KB
    ): ?string {
        if (empty($input)) {
            return null;
        }

        $compressedBinary = self::compressToBinary($input, $maxSizeKb);
        if (!$compressedBinary) {
            return null;
        }

        $filename = rtrim($directory, '/') . '/' . uniqid('img_', true) . '.jpg';
        Storage::disk($disk)->put($filename, $compressedBinary);

        return $filename;
    }

    /**
     * Compress any image input into optimized JPEG binary bytes with max file size <= $maxSizeKb (200 KB).
     *
     * @param mixed $input UploadedFile|string (base64 or binary or path)
     * @param int $maxSizeKb Target max size in KB (default: 200 KB)
     * @return string|null Raw JPEG binary bytes
     */
    public static function compressToBinary(mixed $input, int $maxSizeKb = self::TARGET_MAX_KB): ?string
    {
        $binary = self::extractBinary($input);
        if (!$binary) {
            return null;
        }

        $sourceImage = @imagecreatefromstring($binary);
        if (!$sourceImage) {
            return null;
        }

        // Auto-fix orientation if EXIF orientation metadata exists
        $sourceImage = self::autoRotateFromBinary($sourceImage, $binary);

        $origW = imagesx($sourceImage);
        $origH = imagesy($sourceImage);

        if ($origW <= 0 || $origH <= 0) {
            imagedestroy($sourceImage);
            return null;
        }

        $maxTargetBytes = $maxSizeKb * 1024; // 200 * 1024 = 204,800 bytes
        $maxDimension = 1200; // Optimal for sharp cat details and KTAM cards

        // Initial dimension resize
        $currentW = $origW;
        $currentH = $origH;

        if ($currentW > $maxDimension || $currentH > $maxDimension) {
            $ratio = min($maxDimension / $currentW, $maxDimension / $currentH);
            $currentW = (int) round($currentW * $ratio);
            $currentH = (int) round($currentH * $ratio);
        }

        $currentW = max(1, $currentW);
        $currentH = max(1, $currentH);

        $workingImage = imagecreatetruecolor($currentW, $currentH);
        $white = imagecolorallocate($workingImage, 255, 255, 255);
        imagefill($workingImage, 0, 0, $white);

        imagecopyresampled(
            $workingImage,
            $sourceImage,
            0, 0, 0, 0,
            $currentW, $currentH,
            $origW, $origH
        );
        imagedestroy($sourceImage);

        // Quality iteration candidates
        $qualities = [85, 78, 72, 65, 58, 50, 42, 35];
        $bestOutput = null;

        foreach ($qualities as $q) {
            ob_start();
            imagejpeg($workingImage, null, $q);
            $output = ob_get_clean();

            $bestOutput = $output;
            if (strlen($output) <= $maxTargetBytes) {
                break;
            }
        }

        // If still exceeding 200 KB (rare, for extreme noise), downscale dimensions further and compress
        if ($bestOutput && strlen($bestOutput) > $maxTargetBytes) {
            $scaleSteps = [0.8, 0.65, 0.5];
            foreach ($scaleSteps as $scale) {
                $smallerW = max(1, (int) round($currentW * $scale));
                $smallerH = max(1, (int) round($currentH * $scale));

                $scaledImage = imagecreatetruecolor($smallerW, $smallerH);
                $white = imagecolorallocate($scaledImage, 255, 255, 255);
                imagefill($scaledImage, 0, 0, $white);

                imagecopyresampled(
                    $scaledImage,
                    $workingImage,
                    0, 0, 0, 0,
                    $smallerW, $smallerH,
                    $currentW, $currentH
                );

                foreach ([75, 65, 55, 45] as $q) {
                    ob_start();
                    imagejpeg($scaledImage, null, $q);
                    $output = ob_get_clean();

                    $bestOutput = $output;
                    if (strlen($output) <= $maxTargetBytes) {
                        imagedestroy($scaledImage);
                        break 2;
                    }
                }
                imagedestroy($scaledImage);
            }
        }

        imagedestroy($workingImage);

        return $bestOutput;
    }

    /**
     * Extract raw binary data from various types of inputs.
     *
     * @param mixed $input
     * @return string|null
     */
    public static function extractBinary(mixed $input): ?string
    {
        if (empty($input)) {
            return null;
        }

        if ($input instanceof UploadedFile) {
            $pathname = $input->getPathname();
            if (file_exists($pathname) && is_readable($pathname)) {
                return file_get_contents($pathname);
            }
            return null;
        }

        if (is_string($input)) {
            // Check if base64 Data URL (e.g. data:image/jpeg;base64,xxxx)
            if (preg_match('/^data:image\/[a-zA-Z0-9\+\-\.]+;base64,(.+)$/s', $input, $matches)) {
                $decoded = base64_decode($matches[1], true);
                if ($decoded !== false) {
                    return $decoded;
                }
            }

            // Check if raw base64 string
            if (preg_match('/^[a-zA-Z0-9\/\r\n+]+={0,2}$/', $input) && strlen($input) > 100 && (strlen($input) % 4 === 0)) {
                $decoded = base64_decode($input, true);
                if ($decoded !== false && @imagecreatefromstring($decoded)) {
                    return $decoded;
                }
            }

            // Check if existing file path
            if (file_exists($input) && is_file($input) && is_readable($input)) {
                return file_get_contents($input);
            }

            // Otherwise treat as raw binary bytes if it's already binary
            if (@imagecreatefromstring($input)) {
                return $input;
            }
        }

        return null;
    }

    /**
     * Automatically rotate GD image resource according to EXIF Orientation flag.
     *
     * @param \GdImage|resource $gdImage
     * @param string $binary
     * @return \GdImage|resource
     */
    protected static function autoRotateFromBinary($gdImage, string $binary)
    {
        if (!function_exists('exif_read_data')) {
            return $gdImage;
        }

        try {
            $stream = fopen('php://memory', 'r+');
            if (!$stream) {
                return $gdImage;
            }
            fwrite($stream, $binary);
            rewind($stream);

            $exif = @exif_read_data($stream);
            fclose($stream);

            if (!empty($exif['Orientation'])) {
                switch ((int) $exif['Orientation']) {
                    case 3:
                        $rotated = imagerotate($gdImage, 180, 0);
                        if ($rotated) {
                            imagedestroy($gdImage);
                            return $rotated;
                        }
                        break;
                    case 6:
                        $rotated = imagerotate($gdImage, -90, 0);
                        if ($rotated) {
                            imagedestroy($gdImage);
                            return $rotated;
                        }
                        break;
                    case 8:
                        $rotated = imagerotate($gdImage, 90, 0);
                        if ($rotated) {
                            imagedestroy($gdImage);
                            return $rotated;
                        }
                        break;
                }
            }
        } catch (\Throwable $e) {
            // Silently fallback to unrotated image if EXIF parsing fails
        }

        return $gdImage;
    }
}
