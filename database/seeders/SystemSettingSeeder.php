<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'android_app_url',
                'label' => 'Link Download Aplikasi Android',
                'value' => '[https://play.google.com/store/apps/details?id=com.skanara](https://play.google.com/store/apps/details?id=com.skanara)',
                'type' => 'url',
            ],
            [
                'key' => 'android_app_desc',
                'label' => 'Deskripsi Widget Download',
                'value' => 'Unduh aplikasi Skanara untuk memudahkan manajemen absensi guru dan operasional kiosk sekolah.',
                'type' => 'textarea',
            ],
            [
                'key' => 'android_app_version',
                'label' => 'Versi Aplikasi',
                'value' => 'v1.0.0',
                'type' => 'text',
            ]
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
