<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Ikeja & Environs (Oregun, Allen, GRA, Alausa)',
                'fee' => 5000,
                'estimated_days' => '1–2 business days',
                'sort_order' => 1,
            ],
            [
                'name' => 'Lagos Mainland (Yaba, Surulere, Maryland, Gbagada, Magodo)',
                'fee' => 6000,
                'estimated_days' => '1–2 business days',
                'sort_order' => 2,
            ],
            [
                'name' => 'Lagos Island (Ikoyi, Victoria Island, Marina, Oniru)',
                'fee' => 8000,
                'estimated_days' => '1–2 business days',
                'sort_order' => 3,
            ],
            [
                'name' => 'Lekki Phase 1, Ikate & Osapa London',
                'fee' => 8500,
                'estimated_days' => '1–2 business days',
                'sort_order' => 4,
            ],
            [
                'name' => 'Ajah, Chevron, Sangotedo & Awoyaya',
                'fee' => 10000,
                'estimated_days' => '2–3 business days',
                'sort_order' => 5,
            ],
            [
                'name' => 'Outskirts Lagos (Ikorodu, Badagry, Epe)',
                'fee' => 12000,
                'estimated_days' => '2–4 business days',
                'sort_order' => 6,
            ],
            [
                'name' => 'Abuja FCT (Central, Maitama, Wuse, Garki, Jabi)',
                'fee' => 15000,
                'estimated_days' => '3–5 business days',
                'sort_order' => 7,
            ],
            [
                'name' => 'Interstate Nationwide (Other States)',
                'fee' => 18000,
                'estimated_days' => '3–6 business days',
                'sort_order' => 8,
            ],
        ];

        foreach ($locations as $loc) {
            \App\Models\ShippingLocation::updateOrCreate(
                ['name' => $loc['name']],
                [
                    'fee' => $loc['fee'],
                    'estimated_days' => $loc['estimated_days'],
                    'sort_order' => $loc['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
