<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityAlbum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clean up titles containing (#IMG_...) using SQL regex / replace
        try {
            $albums = ActivityAlbum::where('title', 'LIKE', '%(#IMG_%')->get();
            foreach ($albums as $album) {
                $cleanedTitle = preg_replace('/\s*\(#IMG_[^\)]+\)/i', '', $album->title);
                $cleanedTitle = trim($cleanedTitle);
                if ($cleanedTitle !== $album->title) {
                    $album->title = $cleanedTitle;
                    $album->saveQuietly();
                }
            }
        } catch (\Throwable $e) {
            // Fallback SQL query if model cannot be used
            DB::statement("UPDATE activity_albums SET title = TRIM(REGEXP_REPLACE(title, '\\\\s*\\\\(#IMG_[^\\\\)]+\\\\)', '')) WHERE title LIKE '%(#IMG_%'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to restore the noisy image filenames into titles
    }
};
