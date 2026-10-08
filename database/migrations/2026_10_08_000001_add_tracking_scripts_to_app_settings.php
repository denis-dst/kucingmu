<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            [
                'key' => 'google_analytics_id',
                'value' => null,
                'label' => 'Google Analytics (GA4) Measurement ID',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'google_analytics_script',
                'value' => null,
                'label' => 'Google Analytics Tag Script (<script>...</script>)',
                'type' => 'textarea',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'google_tag_manager_id',
                'value' => null,
                'label' => 'Google Tag Manager Container ID',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'google_tag_manager_head',
                'value' => null,
                'label' => 'Google Tag Manager Head Script',
                'type' => 'textarea',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'google_tag_manager_body',
                'value' => null,
                'label' => 'Google Tag Manager Body (<noscript>) Script',
                'type' => 'textarea',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'custom_head_scripts',
                'value' => null,
                'label' => 'Skrip Tambahan Header (<head>)',
                'type' => 'textarea',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'custom_body_scripts',
                'value' => null,
                'label' => 'Skrip Tambahan Body (sebelum </body>)',
                'type' => 'textarea',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            if (!DB::table('app_settings')->where('key', $setting['key'])->exists()) {
                DB::table('app_settings')->insert($setting);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('app_settings')->whereIn('key', [
            'google_analytics_id',
            'google_analytics_script',
            'google_tag_manager_id',
            'google_tag_manager_head',
            'google_tag_manager_body',
            'custom_head_scripts',
            'custom_body_scripts',
        ])->delete();
    }
};
