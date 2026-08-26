<?php

namespace App\Services;

use App\Models\PtmaCatCensus;
use App\Models\Cat;
use App\Models\CatPhoto;
use App\Models\StrayCatSurvey;
use Illuminate\Support\Facades\Storage;

class CatBiometricService
{
    /**
     * Extract a rich, 108-dimensional Spatial Grid Color & Texture Fingerprint from image binary.
     * Divides image into a 3x3 spatial grid:
     * - 9 regions x (6-bin Hue histogram + Mean Saturation + Mean Value) = 72 color dimensions
     * - 9 regions x (Horizontal Sobel Gradient + Vertical Sobel Gradient) = 18 texture/stripe dimensions
     * - 9 regions x (Luminance Contrast Variance) = 9 contrast dimensions
     * - 9 regions x (Local Edge Density) = 9 edge dimensions
     * Total = 108 dimensions, L2-normalized.
     *
     * @param string $binaryData
     * @return array|null
     */
    public static function extractSpatialFingerprint(string $binaryData): ?array
    {
        $im = @imagecreatefromstring($binaryData);
        if (!$im) {
            return null;
        }

        $origW = imagesx($im);
        $origH = imagesy($im);

        // Normalize to canonical 96x96 matrix
        $w = 96;
        $h = 96;
        $thumb = imagecreatetruecolor($w, $h);
        imagecopyresampled($thumb, $im, 0, 0, 0, 0, $w, $h, $origW, $origH);
        imagedestroy($im);

        // Precompute Grayscale and HSV matrix
        $gray = [];
        $hsv = [];
        for ($y = 0; $y < $h; $y++) {
            $gray[$y] = [];
            $hsv[$y] = [];
            for ($x = 0; $x < $w; $x++) {
                $rgb = imagecolorat($thumb, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // Grayscale
                $gray[$y][$x] = (int) (0.299 * $r + 0.587 * $g + 0.114 * $b);

                // HSV
                $hsv[$y][$x] = self::rgbToHsv($r, $g, $b);
            }
        }

        $features = [];
        $cellW = (int) floor($w / 3); // 32 px
        $cellH = (int) floor($h / 3); // 32 px

        // Iterate through 3x3 = 9 spatial cells
        for ($cy = 0; $cy < 3; $cy++) {
            for ($cx = 0; $cx < 3; $cx++) {
                $startX = $cx * $cellW;
                $startY = $cy * $cellH;
                $endX = ($cx === 2) ? $w : ($startX + $cellW);
                $endY = ($cy === 2) ? $h : ($startY + $cellH);

                $cellPixelCount = ($endX - $startX) * ($endY - $startY);
                if ($cellPixelCount === 0) {
                    $cellPixelCount = 1;
                }

                $hueBins = array_fill(0, 6, 0); // 6 bins for Hue (Red, Yellow, Green, Cyan, Blue, Magenta)
                $satSum = 0;
                $valSum = 0;
                $grayValues = [];
                $gradXSum = 0;
                $gradYSum = 0;
                $edgeCount = 0;

                for ($y = $startY; $y < $endY; $y++) {
                    for ($x = $startX; $x < $endX; $x++) {
                        [$hVal, $sVal, $vVal] = $hsv[$y][$x];
                        $satSum += $sVal;
                        $valSum += $vVal;

                        // Hue bin (0-360 mapped to 6 bins)
                        $hBin = min(5, (int) floor($hVal / 60));
                        // Weight hue by saturation (achromatic colors don't have strong hue)
                        $hueBins[$hBin] += $sVal;

                        $gVal = $gray[$y][$x];
                        $grayValues[] = $gVal;

                        // Sobel gradients (clamp to edges)
                        if ($x > 0 && $x < $w - 1 && $y > 0 && $y < $h - 1) {
                            $gx = abs($gray[$y][$x + 1] - $gray[$y][$x - 1]);
                            $gy = abs($gray[$y + 1][$x] - $gray[$y - 1][$x]);
                            $gradXSum += $gx;
                            $gradYSum += $gy;
                            if ($gx + $gy > 35) {
                                $edgeCount++;
                            }
                        }
                    }
                }

                // 1. Hue Bins normalized
                foreach ($hueBins as $hb) {
                    $features[] = round($hb / $cellPixelCount, 4);
                }

                // 2. Mean Saturation & Value
                $features[] = round($satSum / $cellPixelCount, 4);
                $features[] = round($valSum / $cellPixelCount, 4);

                // 3. Texture / Stripe Gradients
                $features[] = round($gradXSum / ($cellPixelCount * 255), 4);
                $features[] = round($gradYSum / ($cellPixelCount * 255), 4);

                // 4. Contrast Variance
                $meanGray = !empty($grayValues) ? (array_sum($grayValues) / count($grayValues)) : 0;
                $varSum = 0;
                foreach ($grayValues as $gv) {
                    $diff = $gv - $meanGray;
                    $varSum += ($diff * $diff);
                }
                $stdDev = sqrt($varSum / $cellPixelCount) / 128.0;
                $features[] = round(min(1.0, $stdDev), 4);

                // 5. Edge Density (Fur & Whisker complexity)
                $features[] = round($edgeCount / $cellPixelCount, 4);
            }
        }

        imagedestroy($thumb);

        // L2 Normalization
        return self::normalizeVector($features);
    }

    /**
     * Compute 64-bit Difference Hash (dHash) for fast structural comparison.
     *
     * @param string $binaryData
     * @return string|null 64-character binary string ('0' and '1')
     */
    public static function extractDHash(string $binaryData): ?string
    {
        $im = @imagecreatefromstring($binaryData);
        if (!$im) {
            return null;
        }

        $w = 9;
        $h = 8;
        $thumb = imagecreatetruecolor($w, $h);
        imagecopyresampled($thumb, $im, 0, 0, 0, 0, $w, $h, imagesx($im), imagesy($im));
        imagedestroy($im);

        $hash = '';
        for ($y = 0; $y < $h; $y++) {
            $prevGray = null;
            for ($x = 0; $x < $w; $x++) {
                $rgb = imagecolorat($thumb, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $gray = (int) (0.299 * $r + 0.587 * $g + 0.114 * $b);

                if ($x > 0) {
                    $hash .= ($gray > $prevGray) ? '1' : '0';
                }
                $prevGray = $gray;
            }
        }

        imagedestroy($thumb);
        return $hash;
    }

    /**
     * Extract legacy 64-bin RGB color histogram.
     */
    public static function extractColorFingerprint(string $binaryData): ?array
    {
        $im = @imagecreatefromstring($binaryData);
        if (!$im) {
            return null;
        }

        $w = imagesx($im);
        $h = imagesy($im);
        $thumb = imagecreatetruecolor(64, 64);
        imagecopyresampled($thumb, $im, 0, 0, 0, 0, 64, 64, $w, $h);
        imagedestroy($im);

        $bins = array_fill(0, 64, 0);
        $totalPixels = 64 * 64;

        for ($x = 0; $x < 64; $x++) {
            for ($y = 0; $y < 64; $y++) {
                $rgb = imagecolorat($thumb, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $rBin = min(3, (int) floor($r / 64));
                $gBin = min(3, (int) floor($g / 64));
                $bBin = min(3, (int) floor($b / 64));

                $binIndex = ($rBin * 16) + ($gBin * 4) + $bBin;
                $bins[$binIndex]++;
            }
        }

        imagedestroy($thumb);

        return array_map(function ($val) use ($totalPixels) {
            return round($val / $totalPixels, 5);
        }, $bins);
    }

    /**
     * Compute cosine similarity between two float arrays (0.0 to 1.0).
     */
    public static function cosineSimilarity(?array $vecA, ?array $vecB): float
    {
        if (empty($vecA) || empty($vecB)) {
            return 0.0;
        }

        $count = min(count($vecA), count($vecB));
        if ($count === 0) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < $count; $i++) {
            $a = (float) $vecA[$i];
            $b = (float) $vecB[$i];
            $dot += ($a * $b);
            $normA += ($a * $a);
            $normB += ($b * $b);
        }

        if ($normA <= 0.0 || $normB <= 0.0) {
            return 0.0;
        }

        $sim = $dot / (sqrt($normA) * sqrt($normB));
        return max(0.0, min(1.0, (float) $sim));
    }

    /**
     * Compute similarity between two 64-bit dHash strings (0.0 to 1.0).
     */
    public static function hashSimilarity(?string $hashA, ?string $hashB): float
    {
        if (empty($hashA) || empty($hashB) || strlen($hashA) !== strlen($hashB)) {
            return 0.5; // neutral default
        }

        $len = strlen($hashA);
        $diff = 0;
        for ($i = 0; $i < $len; $i++) {
            if ($hashA[$i] !== $hashB[$i]) {
                $diff++;
            }
        }

        return max(0.0, min(1.0, 1.0 - ($diff / $len)));
    }

    /**
     * Comprehensive Ensemble Biometric Matcher.
     * Evaluates Deep Embedding (60%), Spatial Texture/Pattern (25%), Color Distribution (10%), Structure Hash (5%).
     *
     * @param array $query ['embedding' => array|null, 'spatial' => array|null, 'color' => array|null, 'hash' => string|null]
     * @param array $target ['embedding' => array|null, 'spatial' => array|null, 'color' => array|null, 'hash' => string|null]
     * @return array ['final_score' => float, 'deep_score' => float, 'spatial_score' => float, 'color_score' => float, 'hash_score' => float]
     */
    public static function evaluateMatchScore(array $query, array $target): array
    {
        $deepScore = null;
        if (!empty($query['embedding']) && !empty($target['embedding'])) {
            $deepScore = self::cosineSimilarity($query['embedding'], $target['embedding']);
        }

        $spatialScore = null;
        if (!empty($query['spatial']) && !empty($target['spatial'])) {
            $spatialScore = self::cosineSimilarity($query['spatial'], $target['spatial']);
        }

        $colorScore = null;
        if (!empty($query['color']) && !empty($target['color'])) {
            $colorScore = self::cosineSimilarity($query['color'], $target['color']);
        }

        $hashScore = null;
        if (!empty($query['hash']) && !empty($target['hash'])) {
            $hashScore = self::hashSimilarity($query['hash'], $target['hash']);
        }

        // Calculate weighted ensemble
        if ($deepScore !== null) {
            // Neural AI is active
            $sScore = $spatialScore ?? 0.5;
            $cScore = $colorScore ?? 0.5;
            $hScore = $hashScore ?? 0.5;

            // Deep cosine similarity for transfer models usually spans [0.3 - 0.95]
            // We scale deep score to calibrate confidence accurately
            $calibratedDeep = pow($deepScore, 1.25);

            $final = ($calibratedDeep * 0.60) + ($sScore * 0.25) + ($cScore * 0.10) + ($hScore * 0.05);
        } elseif ($spatialScore !== null) {
            // High-dimensional Spatial & Texture Pattern mode
            $cScore = $colorScore ?? 0.5;
            $hScore = $hashScore ?? 0.5;
            $final = ($spatialScore * 0.70) + ($cScore * 0.20) + ($hScore * 0.10);
        } elseif ($colorScore !== null) {
            $final = $colorScore;
        } else {
            $final = 0.0;
        }

        return [
            'final_score'   => max(0.0, min(1.0, (float) $final)),
            'deep_score'    => $deepScore !== null ? round($deepScore * 100, 1) : null,
            'spatial_score' => $spatialScore !== null ? round(($spatialScore ?? 0) * 100, 1) : null,
            'color_score'   => $colorScore !== null ? round(($colorScore ?? 0) * 100, 1) : null,
            'hash_score'    => $hashScore !== null ? round(($hashScore ?? 0) * 100, 1) : null,
        ];
    }

    /**
     * Read / ensure a photo has computed spatial and color fingerprints.
     */
    public static function getOrGenerateFingerprints(string $storageRelativePath): ?array
    {
        $fullPath = storage_path('app/public/' . $storageRelativePath);
        if (!file_exists($fullPath)) {
            return null;
        }

        $binary = file_get_contents($fullPath);
        if (!$binary) {
            return null;
        }

        return [
            'spatial' => self::extractSpatialFingerprint($binary),
            'color'   => self::extractColorFingerprint($binary),
            'hash'    => self::extractDHash($binary),
        ];
    }

    /**
     * Index and compute all missing fingerprints & hashes for existing records across all modules.
     *
     * @return array Summary of updated items
     */
    public static function indexAllMissingData(): array
    {
        $updatedCensus = 0;
        $updatedCats = 0;
        $updatedCatPhotos = 0;
        $updatedSurveys = 0;

        // 1. PTMA Cat Census
        $censuses = PtmaCatCensus::all();
        foreach ($censuses as $census) {
            $changed = false;

            // Check each photo slot
            $slots = [
                'wajah'    => $census->foto_wajah,
                'atas'     => $census->foto_atas,
                'samping'  => $census->foto_samping_kiri,
                'opsional' => $census->foto_opsional,
            ];

            $multiFp = is_array($census->spatial_fingerprint) ? $census->spatial_fingerprint : [];

            foreach ($slots as $slotKey => $slotPath) {
                if ($slotPath && empty($multiFp[$slotKey])) {
                    $fps = self::getOrGenerateFingerprints($slotPath);
                    if ($fps) {
                        $multiFp[$slotKey] = $fps;
                        $changed = true;
                    }
                }
            }

            if ($changed || empty($census->spatial_fingerprint)) {
                $census->spatial_fingerprint = $multiFp;
                if (!empty($multiFp['wajah']['color']) && empty($census->color_fingerprint)) {
                    $census->color_fingerprint = $multiFp['wajah']['color'];
                }
                $census->saveQuietly();
                $updatedCensus++;
            }
        }

        // 2. Member Cats (Cat)
        $cats = Cat::whereNotNull('photo_path')->get();
        foreach ($cats as $cat) {
            if (empty($cat->spatial_fingerprint)) {
                $fps = self::getOrGenerateFingerprints($cat->photo_path);
                if ($fps) {
                    $cat->spatial_fingerprint = $fps;
                    $cat->color_fingerprint = $fps['color'];
                    $cat->saveQuietly();
                    $updatedCats++;
                }
            }
        }

        // 3. Cat Photos (CatPhoto)
        $catPhotos = CatPhoto::whereNotNull('photo_path')->get();
        foreach ($catPhotos as $cp) {
            if (empty($cp->spatial_fingerprint)) {
                $fps = self::getOrGenerateFingerprints($cp->photo_path);
                if ($fps) {
                    $cp->spatial_fingerprint = $fps;
                    $cp->color_fingerprint = $fps['color'];
                    $cp->saveQuietly();
                    $updatedCatPhotos++;
                }
            }
        }

        // 4. Stray Cat Surveys
        $surveys = StrayCatSurvey::whereNotNull('photo_path')->get();
        foreach ($surveys as $srv) {
            if (empty($srv->spatial_fingerprint)) {
                $fps = self::getOrGenerateFingerprints($srv->photo_path);
                if ($fps) {
                    $srv->spatial_fingerprint = $fps;
                    $srv->color_fingerprint = $fps['color'];
                    $srv->saveQuietly();
                    $updatedSurveys++;
                }
            }
        }

        return [
            'census'     => $updatedCensus,
            'cats'       => $updatedCats,
            'cat_photos' => $updatedCatPhotos,
            'surveys'    => $updatedSurveys,
        ];
    }

    /**
     * Helper: Convert RGB (0-255) to HSV [H(0-360), S(0-1), V(0-1)].
     */
    private static function rgbToHsv(int $r, int $g, int $b): array
    {
        $rNorm = $r / 255.0;
        $gNorm = $g / 255.0;
        $bNorm = $b / 255.0;

        $max = max($rNorm, $gNorm, $bNorm);
        $min = min($rNorm, $gNorm, $bNorm);
        $delta = $max - $min;

        // Value
        $v = $max;

        // Saturation
        $s = ($max > 0.0001) ? ($delta / $max) : 0.0;

        // Hue
        $h = 0.0;
        if ($delta > 0.0001) {
            if ($max === $rNorm) {
                $h = 60.0 * fmod((($gNorm - $bNorm) / $delta), 6);
            } elseif ($max === $gNorm) {
                $h = 60.0 * ((($bNorm - $rNorm) / $delta) + 2);
            } else {
                $h = 60.0 * ((($rNorm - $gNorm) / $delta) + 4);
            }
            if ($h < 0) {
                $h += 360.0;
            }
        }

        return [$h, $s, $v];
    }

    /**
     * Helper: L2-normalize an array of float numbers.
     */
    public static function normalizeVector(array $vec): array
    {
        $sumSq = 0.0;
        foreach ($vec as $val) {
            $sumSq += ($val * $val);
        }

        if ($sumSq <= 0.0) {
            return $vec;
        }

        $norm = sqrt($sumSq);
        return array_map(function ($val) use ($norm) {
            return round($val / $norm, 5);
        }, $vec);
    }
}
