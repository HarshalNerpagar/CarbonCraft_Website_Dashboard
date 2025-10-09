<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Seed the services table with CarbonCraft card types
     */
    public function run(): void
    {
        $services = [
            [
                'id' => 1,
                'service_name' => 'Metal Card - DIY Service',
                'icon' => '🎨',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'service_name' => 'Metal Card - Full Service',
                'icon' => '✨',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'service_name' => 'Plastic Card - DIY Service',
                'icon' => '🎨',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'service_name' => 'Plastic Card - Full Service',
                'icon' => '✨',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 5,
                'service_name' => 'Tap & Pay Card - DIY Service',
                'icon' => '📱',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 6,
                'service_name' => 'Tap & Pay Card - Full Service',
                'icon' => '📱',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        // Use insert instead of updateOrCreate to preserve IDs
        DB::table('services')->insertOrIgnore($services);
    }
}
