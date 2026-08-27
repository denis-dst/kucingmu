<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityAlbum;

class ActivityAlbumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $files = [
            "IMG_2855.JPG",
            "IMG_3142.JPG",
            "IMG_5803.JPG",
            "IMG_5804.JPG",
            "IMG_5805.JPG",
            "IMG_5806.JPG",
            "IMG_5807.JPG",
            "IMG_5808.JPG",
            "IMG_5809.JPG",
            "IMG_5810.JPG",
            "IMG_5811.JPG",
            "IMG_5812.JPG",
            "IMG_5813.JPG",
            "IMG_5814.JPG",
            "IMG_5815.JPG",
            "IMG_5816.JPG",
            "IMG_5817.JPG",
            "IMG_5818.JPG",
            "IMG_5819.JPG",
            "IMG_5820.JPG",
            "IMG_5821.JPG",
            "IMG_5822.JPG",
            "IMG_5823.JPG",
            "IMG_5824.JPG",
            "IMG_5825.JPG",
            "IMG_5826.JPG",
            "IMG_5827.JPG",
            "IMG_5828.JPG",
            "IMG_5829.JPG",
            "IMG_5830.JPG",
            "IMG_5831.JPG",
            "IMG_5832.JPG",
            "IMG_5833.JPG",
            "IMG_5834.JPG",
            "IMG_5835.JPG",
            "IMG_5836.JPG",
            "IMG_5837.JPG",
            "IMG_5838.JPG",
            "IMG_5839.JPG",
            "IMG_5840.JPG",
            "IMG_5841.JPG",
            "IMG_5842.JPG",
            "IMG_5843.JPG",
            "IMG_5844.JPG",
            "IMG_5845.JPG",
            "IMG_5846.JPG",
            "IMG_5847.JPG",
            "IMG_5848.JPG",
            "IMG_5849.JPG",
            "IMG_5850.JPG",
            "IMG_5851.JPG",
            "IMG_5852.JPG",
            "IMG_5853.JPG",
            "IMG_5854.JPG",
            "IMG_5855.JPG",
            "IMG_5856.JPG",
            "IMG_5857.JPG",
            "IMG_5858.JPG",
            "IMG_5859.JPG",
            "IMG_5860.JPG",
            "IMG_5861.JPG",
            "IMG_5862.JPG",
            "IMG_5863.JPG",
            "IMG_5864.JPG",
            "IMG_5865.JPG",
            "IMG_5866.JPG",
            "IMG_5867.JPG",
            "IMG_5868.JPG",
            "IMG_5869.JPG",
            "IMG_5870.JPG",
            "IMG_5871.JPG",
            "IMG_5872.JPG",
            "IMG_5873.JPG",
            "IMG_5874.JPG",
            "IMG_5875.JPG",
            "IMG_5876.JPG",
            "IMG_5877.JPG",
            "IMG_5878.JPG",
            "IMG_5879.JPG",
            "IMG_5880.JPG",
            "IMG_5881.JPG",
            "IMG_5882.JPG",
            "IMG_5883.JPG",
            "IMG_5884.JPG",
            "IMG_5885.JPG",
            "IMG_5886.JPG",
            "IMG_5887.JPG",
            "IMG_5888.JPG",
            "IMG_5889.JPG",
            "IMG_5890.JPG",
            "IMG_5891.JPG",
            "IMG_5892.JPG",
            "IMG_5893.JPG",
            "IMG_5894.JPG",
            "IMG_5895.JPG",
            "IMG_5896.JPG",
            "IMG_5897.JPG",
            "IMG_5898.JPG",
            "IMG_5899.JPG",
            "IMG_5900.JPG",
            "IMG_5901.JPG",
            "IMG_5902.JPG",
            "IMG_5903.JPG",
            "IMG_5904.JPG",
            "IMG_5905.JPG",
            "IMG_5906.JPG",
            "IMG_5907.JPG",
            "IMG_5908.JPG",
            "IMG_5909.JPG",
            "IMG_5910.JPG",
            "IMG_5911.JPG",
            "IMG_5912.JPG",
            "IMG_5913.JPG",
            "IMG_5914.JPG",
            "IMG_5915.JPG",
            "IMG_5916.JPG",
            "IMG_5917.JPG",
            "IMG_5918.JPG",
            "IMG_5919.JPG",
            "IMG_5920.JPG",
            "IMG_5921.JPG",
            "IMG_5922.JPG",
            "IMG_5923.JPG",
            "IMG_5924.JPG",
            "IMG_5925.JPG",
            "IMG_5926.JPG",
            "IMG_5927.JPG",
            "IMG_5928.JPG",
            "IMG_5929.JPG",
            "IMG_5930.JPG",
            "IMG_5931.JPG",
            "IMG_5932.JPG",
            "IMG_5933.JPG",
            "IMG_5934.JPG",
            "IMG_5935.JPG",
            "IMG_5936.JPG",
            "IMG_5937.JPG",
            "IMG_5938.JPG",
            "IMG_5939.JPG",
            "IMG_5940.JPG",
            "IMG_5941.JPG",
            "IMG_5942.JPG",
            "IMG_5943.JPG",
            "IMG_5944.JPG",
            "IMG_5945.JPG",
            "IMG_5946.JPG",
            "IMG_5947.JPG",
            "IMG_5948.JPG",
            "IMG_5949.JPG",
            "IMG_5950.JPG",
            "IMG_5951.JPG",
            "IMG_5952.JPG",
            "IMG_5953.JPG",
            "IMG_5954.JPG",
            "IMG_5955.JPG",
            "IMG_5956.JPG",
            "IMG_5957.JPG",
            "IMG_5958.JPG",
            "IMG_5959.JPG",
            "IMG_5960.JPG",
            "IMG_5961.JPG",
            "IMG_5962.JPG",
            "IMG_5963.JPG",
            "IMG_5964.JPG",
            "IMG_5965.JPG",
            "IMG_5969.JPG",
            "IMG_5970.JPG",
            "IMG_5971.JPG",
            "IMG_5972.JPG",
            "IMG_5973.JPG",
            "IMG_5974.JPG",
            "IMG_5975.JPG",
            "IMG_5976.JPG",
            "IMG_5977.JPG",
            "IMG_5978.JPG",
            "IMG_5979.JPG",
            "IMG_5980.JPG",
            "IMG_5981.JPG",
            "IMG_5982.JPG",
            "IMG_5983.JPG",
            "IMG_5984.JPG",
            "IMG_5985.JPG",
            "IMG_5986.JPG",
            "IMG_5987.JPG",
            "IMG_5988.JPG",
            "IMG_5989.JPG",
            "IMG_5990.JPG",
            "IMG_5991.JPG",
            "IMG_5992.JPG",
            "IMG_5993.JPG",
            "IMG_5994.JPG",
            "IMG_5995.JPG",
            "IMG_5996.JPG",
            "IMG_5997.JPG",
            "IMG_5998.JPG",
            "IMG_5999.JPG",
            "IMG_6001.JPG",
            "IMG_6002.JPG",
            "IMG_6003.JPG",
            "IMG_6004.JPG",
            "IMG_6005.JPG",
            "IMG_6006.JPG",
            "IMG_6007.JPG",
            "IMG_6008.JPG",
            "IMG_7471.JPG",
        ];

        // 1. Delete all HEIC/MOV if any exist
        ActivityAlbum::where('image_path', 'like', '%.HEIC')
            ->orWhere('image_path', 'like', '%.heic')
            ->orWhere('image_path', 'like', '%.MOV')
            ->orWhere('image_path', 'like', '%.mov')
            ->delete();

        // 2. Insert or update all JPGs as inactive first
        $order = 1;
        foreach ($files as $filename) {
            if (str_starts_with($filename, 'IMG_28')) {
                $category = 'Pemeriksaan Kesehatan';
                $title = "Pemeriksaan Medis & Vaksinasi Kucing";
                $caption = "Pemeriksaan klinis komprehensif, pembersihan telinga, dan pemberian vitamin oleh dokter hewan mitra KucingMu.";
            } elseif (str_starts_with($filename, 'IMG_31')) {
                $category = 'Surveilans & Morfometri';
                $title = "Surveilans Lapangan & Identifikasi";
                $caption = "Pencatatan morfometri, identifikasi pola warna/bulu, dan pemantauan kesehatan kucing di lingkungan kampus.";
            } elseif (str_starts_with($filename, 'IMG_74')) {
                $category = 'Edukasi & Komunitas';
                $title = "Kegiatan Edukasi & Sosialisasi KucingMu";
                $caption = "Sosialisasi kesejahteraan hewan dan penerbitan Kartu Tanda Anggota Muhammadiyah (KTAM) Kucing bersama relawan.";
            } else {
                $category = 'Layanan Dokter Hewan';
                $title = "Pelayanan Kesehatan Kucing Komunitas";
                $caption = "Dokumentasi tindakan dokter hewan, pemeriksaan fisik dasar, dan konsultasi kesehatan kucing peliharaan.";
            }

            ActivityAlbum::updateOrCreate(
                ['image_path' => 'images/albums/' . $filename],
                [
                    'title' => $title,
                    'caption' => $caption,
                    'category' => $category,
                    'activity_date' => now()->subDays(max(1, 200 - $order))->toDateString(),
                    'order' => 0,
                    'is_active' => false,
                ]
            );

            $order++;
        }

        // Set all to inactive first
        ActivityAlbum::query()->update(['is_active' => false, 'order' => 0]);

        // 3. Pick 10 random JPG photos to be active on the homepage slider
        $randomActive = ActivityAlbum::where(function($q) {
            $q->where('image_path', 'like', '%.JPG')
              ->orWhere('image_path', 'like', '%.jpg')
              ->orWhere('image_path', 'like', '%.JPEG')
              ->orWhere('image_path', 'like', '%.jpeg');
        })->inRandomOrder()->take(10)->get();

        $slideOrder = 1;
        foreach ($randomActive as $album) {
            $album->is_active = true;
            $album->order = $slideOrder++;
            $album->save();
        }
    }
}
