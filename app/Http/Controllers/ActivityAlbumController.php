<?php

namespace App\Http\Controllers;

use App\Models\ActivityAlbum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ActivityAlbumController extends Controller
{
    /**
     * Display a listing of the activity albums / photos.
     */
    public function index(Request $request)
    {
        $query = ActivityAlbum::query();

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('caption', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Status filter
        if ($request->has('status') && $request->status !== '' && $request->status !== null) {
            $query->where('is_active', (bool) $request->status);
        }

        $albums = $query->orderBy('order', 'asc')
                        ->orderBy('activity_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(12)
                        ->withQueryString();

        // Get list of existing image files in public/images/albums
        $albumsDir = public_path('images/albums');
        $existingFiles = [];
        if (File::isDirectory($albumsDir)) {
            $files = File::files($albumsDir);
            foreach ($files as $file) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'heic', 'mov', 'mp4'])) {
                    $existingFiles[] = $file->getFilename();
                }
            }
        }

        $categories = ActivityAlbum::distinct()->whereNotNull('category')->pluck('category')->toArray();
        if (empty($categories)) {
            $categories = ['Pemeriksaan', 'Sensus PTMA', 'Edukasi & Sosialisasi', 'Vaksinasi & Sterilisasi', 'Kegiatan Komunitas'];
        }

        $stats = [
            'total_photos' => ActivityAlbum::count(),
            'active_photos' => ActivityAlbum::where('is_active', true)->count(),
            'inactive_photos' => ActivityAlbum::where('is_active', false)->count(),
            'total_files_in_dir' => count($existingFiles),
        ];

        return view('admin.albums.index', compact('albums', 'existingFiles', 'categories', 'stats'));
    }

    /**
     * Store a newly created album photo in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
            'existing_image' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'activity_date' => ['nullable', 'date'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = null;

        // 1. Upload new image file if provided
        if ($request->hasFile('image_file')) {
            $imagePath = $this->optimizeAndStoreAlbumImage($request->file('image_file'));
        } elseif (!empty($request->existing_image)) {
            // 2. Or pick from existing file inside public/images/albums
            $imagePath = 'images/albums/' . basename($request->existing_image);
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Silakan unggah file foto atau pilih foto dari stok folder public/images/albums.');
        }

        $validated['image_path'] = $imagePath;
        $validated['order'] = $validated['order'] ?? 0;
        $validated['category'] = $validated['category'] ?: 'Kegiatan';
        $validated['activity_date'] = $validated['activity_date'] ?: now()->toDateString();
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;

        $album = ActivityAlbum::create($validated);

        return redirect()->route('superadmin.albums.index')
            ->with('success', "Foto kegiatan \"{$album->title}\" berhasil ditambahkan ke album.");
    }

    /**
     * Update the specified album photo in storage.
     */
    public function update(Request $request, ActivityAlbum $album)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
            'existing_image' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'activity_date' => ['nullable', 'date'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image_path'] = $this->optimizeAndStoreAlbumImage($request->file('image_file'));
        } elseif (!empty($request->existing_image)) {
            $validated['image_path'] = 'images/albums/' . basename($request->existing_image);
        }

        $validated['order'] = $validated['order'] ?? 0;
        $validated['category'] = $validated['category'] ?: 'Kegiatan';
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

        $album->update($validated);

        return redirect()->route('superadmin.albums.index')
            ->with('success', "Foto kegiatan \"{$album->title}\" berhasil diperbarui.");
    }

    /**
     * Compress and store an uploaded album image, generating optimized WebP.
     */
    private function optimizeAndStoreAlbumImage($file): string
    {
        $albumsDir = public_path('images/albums');
        if (!File::isDirectory($albumsDir)) {
            File::makeDirectory($albumsDir, 0777, true, true);
        }

        $baseName = 'album_' . date('Ymd_His') . '_' . Str::random(6);
        $binary = file_get_contents($file->getPathname());
        $sourceImage = @imagecreatefromstring($binary);

        if ($sourceImage) {
            $origW = imagesx($sourceImage);
            $origH = imagesy($sourceImage);
            $maxDim = 1200;

            $newW = $origW;
            $newH = $origH;
            if ($origW > $maxDim || $origH > $maxDim) {
                $ratio = min($maxDim / $origW, $maxDim / $origH);
                $newW = max(1, (int) round($origW * $ratio));
                $newH = max(1, (int) round($origH * $ratio));
            }

            $resized = imagecreatetruecolor($newW, $newH);
            $white = imagecolorallocate($resized, 255, 255, 255);
            imagefill($resized, 0, 0, $white);
            imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($sourceImage);

            if (function_exists('imagewebp')) {
                $webpName = $baseName . '.webp';
                imagewebp($resized, $albumsDir . '/' . $webpName, 82);
                imagedestroy($resized);
                return 'images/albums/' . $webpName;
            } else {
                $jpgName = $baseName . '.jpg';
                imagejpeg($resized, $albumsDir . '/' . $jpgName, 82);
                imagedestroy($resized);
                return 'images/albums/' . $jpgName;
            }
        }

        $filename = $baseName . '.' . ($file->getClientOriginalExtension() ?: 'jpg');
        $file->move($albumsDir, $filename);
        return 'images/albums/' . $filename;
    }

    /**
     * Remove the specified album photo from storage.
     */
    public function destroy(ActivityAlbum $album)
    {
        $title = $album->title;
        $album->delete();

        return redirect()->route('superadmin.albums.index')
            ->with('success', "Foto \"{$title}\" berhasil dihapus dari album kegiatan.");
    }

    /**
     * Toggle active status of an album photo.
     */
    public function toggleStatus(ActivityAlbum $album)
    {
        $album->is_active = !$album->is_active;
        $album->save();

        $statusText = $album->is_active ? 'ditampilkan di beranda' : 'disembunyikan dari beranda';

        return redirect()->route('superadmin.albums.index')
            ->with('success', "Foto \"{$album->title}\" berhasil {$statusText}.");
    }

    /**
     * Seed / Inject default 250 album photos stock.
     */
    public function seedDefault()
    {
        $seeder = new \Database\Seeders\ActivityAlbumSeeder();
        $seeder->run();

        return redirect()->route('superadmin.albums.index')
            ->with('success', 'Stok data foto kegiatan (250 album) berhasil di-generate & diinjeksi ke sistem.');
    }
}
