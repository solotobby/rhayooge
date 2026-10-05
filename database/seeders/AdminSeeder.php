<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'dharmie@rhayooge.com')],
            [
                'name' => env('ADMIN_NAME', 'Dharmie'),
                'phone' => env('ADMIN_PHONE', '+2348000000001'),
                'password' => env('ADMIN_PASSWORD', 'password'),
                'provider' => 'email',
                'is_admin' => true,
            ],
        );
    }
}
