<?php

namespace Tests\Unit;

use App\Services\ImageCompressionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageCompressionServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Helper to create a dummy raw JPEG binary with given dimensions and noise.
     */
    protected function createDummyImageBinary(int $width = 1600, int $height = 1200): string
    {
        $im = imagecreatetruecolor($width, $height);
        for ($x = 0; $x < $width; $x += 20) {
            for ($y = 0; $y < $height; $y += 20) {
                $color = imagecolorallocate($im, ($x * 13) % 255, ($y * 17) % 255, (($x + $y) * 19) % 255);
                imagefilledrectangle($im, $x, $y, $x + 20, $y + 20, $color);
            }
        }

        ob_start();
        imagejpeg($im, null, 100);
        $binary = ob_get_clean();
        imagedestroy($im);

        return $binary;
    }

    public function test_compresses_uploaded_file_to_under_200kb(): void
    {
        $binary = $this->createDummyImageBinary(2000, 1500);
        $file = UploadedFile::fake()->createWithContent('cat_highres.jpg', $binary);

        $savedPath = ImageCompressionService::compressAndStore($file, 'cats', 'public', 200);

        $this->assertNotNull($savedPath);
        $this->assertStringStartsWith('cats/', $savedPath);
        $this->assertTrue(Storage::disk('public')->exists($savedPath));

        $storedBytes = strlen(Storage::disk('public')->get($savedPath));
        $this->assertLessThanOrEqual(200 * 1024, $storedBytes);
        $this->assertGreaterThan(0, $storedBytes);
    }

    public function test_compresses_camera_base64_to_under_200kb(): void
    {
        $binary = $this->createDummyImageBinary(1920, 1080);
        $base64 = 'data:image/jpeg;base64,' . base64_encode($binary);

        $savedPath = ImageCompressionService::compressAndStore($base64, 'ptma-census', 'public', 200);

        $this->assertNotNull($savedPath);
        $this->assertStringStartsWith('ptma-census/', $savedPath);
        $this->assertTrue(Storage::disk('public')->exists($savedPath));

        $storedBytes = strlen(Storage::disk('public')->get($savedPath));
        $this->assertLessThanOrEqual(200 * 1024, $storedBytes);
    }

    public function test_compress_to_binary_returns_valid_jpeg_under_target_size(): void
    {
        $binary = $this->createDummyImageBinary(1800, 1200);
        $compressed = ImageCompressionService::compressToBinary($binary, 200);

        $this->assertNotNull($compressed);
        $this->assertLessThanOrEqual(200 * 1024, strlen($compressed));

        $check = @imagecreatefromstring($compressed);
        $this->assertNotFalse($check);
        if ($check) {
            imagedestroy($check);
        }
    }

    public function test_returns_null_for_empty_or_invalid_input(): void
    {
        $this->assertNull(ImageCompressionService::compressAndStore(null));
        $this->assertNull(ImageCompressionService::compressAndStore(''));
        $this->assertNull(ImageCompressionService::compressAndStore('invalid-not-an-image-content'));
    }
}
