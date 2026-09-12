<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppSettingController extends Controller
{
    /**
     * Show app settings editor (admin only).
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $settings = AppSetting::all();
        return view('admin.settings', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Process text and select/boolean settings
        $settings = $request->input('settings', []);
        foreach ($settings as $key => $value) {
            $setting = AppSetting::find($key);
            if ($setting && $setting->type !== 'file') {
                if ($setting->type === 'boolean' && $value === null) {
                    $value = '0';
                }
                $setting->update(['value' => $value]);
            }
        }

        // Process file settings (logo, favicon, etc.)
        if ($request->hasFile('settings')) {
            $files = $request->file('settings');
            foreach ($files as $key => $file) {
                if ($file && $file->isValid()) {
                    $setting = AppSetting::find($key);
                    if ($setting && $setting->type === 'file') {
                        // Delete old file if exists
                        if ($setting->value) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($setting->value);
                        }
                        // Store new file
                        $path = $this->processAndStoreSettingFile($file, $key);
                        $setting->update(['value' => $path]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Pengaturan aplikasi berhasil diperbarui.');
    }

    /**
     * Process and store file using native PHP to avoid filesystem adapter issues,
     * applying high-performance dimensions and compression for web assets.
     */
    private function processAndStoreSettingFile($file, string $settingKey = ''): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'png');
        $isFavicon = str_contains($settingKey, 'favicon');
        $isLogo = str_contains($settingKey, 'logo');

        $filename = 'settings/' . uniqid() . '.' . $extension;
        $fullPath = storage_path('app/public/' . $filename);
        
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp']);

        if ($isImage) {
            $binary = file_get_contents($file->getPathname());
            $sourceImage = @imagecreatefromstring($binary);
            
            if ($sourceImage) {
                $origWidth = imagesx($sourceImage);
                $origHeight = imagesy($sourceImage);
                
                // Appropriate max dimensions for logos/favicons vs general banners
                if ($isFavicon) {
                    $maxDim = 64;
                } elseif ($isLogo) {
                    $maxDim = 256; // High DPI 2x retina for 32px-128px logos
                } else {
                    $maxDim = 600;
                }

                $newWidth = $origWidth;
                $newHeight = $origHeight;

                if ($origWidth > $maxDim || $origHeight > $maxDim) {
                    $ratio = min($maxDim / $origWidth, $maxDim / $origHeight);
                    $newWidth = max(1, (int) round($origWidth * $ratio));
                    $newHeight = max(1, (int) round($origHeight * $ratio));
                }

                $resized = imagecreatetruecolor($newWidth, $newHeight);

                // Preserve transparency for PNG and WebP
                if (in_array($extension, ['png', 'webp'])) {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                    imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
                } else {
                    $white = imagecolorallocate($resized, 255, 255, 255);
                    imagefill($resized, 0, 0, $white);
                }

                imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                imagedestroy($sourceImage);

                if ($extension === 'png') {
                    imagepng($resized, $fullPath, 9); // Max compression
                } elseif ($extension === 'webp' && function_exists('imagewebp')) {
                    imagewebp($resized, $fullPath, 85);
                } else {
                    imagejpeg($resized, $fullPath, 85);
                }
                imagedestroy($resized);
                
                return $filename;
            }
        }

        // Fallback for .ico or if GD fails
        file_put_contents($fullPath, file_get_contents($file->getPathname()));
        
        return $filename;
    }
}
